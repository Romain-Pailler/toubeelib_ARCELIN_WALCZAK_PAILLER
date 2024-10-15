<?php

namespace toubeelib\core\services\rdv;


use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DomainException;
use toubeelib\core\domain\entities\rdv\RendezVous;
use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\RendezVousDTO;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib\core\services\rdv\ServiceRendezVousNotDataFoundException;

use Monolog\Logger; // Use the correct namespace for Logger
use Monolog\Handler\StreamHandler; // Use the correct namespace for StreamHandler
use Monolog\Handler\FirePHPHandler; // Use the correct namespace for FirePHPHandler
use toubeelib\core\services\praticien\ServicePraticienInvalidDataException;

class ServiceRendezVous implements ServiceRendezVousInterface
{

    //constantes
    const HEURE_RDV_DEBUT = '08:00';
    const HEURE_RDV_FIN = '18:00';
    const INTERVAL_HEURE = 'PT30M';

    //attributs
    private RdvRepositoryInterface $rendezvousRepository;
    private PraticienRepositoryInterface $praticienRepository;


    public function __construct(RdvRepositoryInterface $rdvRepository, PraticienRepositoryInterface $praticienRepository)
    {
        $this->rendezvousRepository = $rdvRepository;
        $this->praticienRepository = $praticienRepository;
    }

    public function creerRendezvous(InputRendezVousDTO $rdv): RendezVousDTO
    {

        try {
            $this->praticienRepository->getPraticienById($rdv->praticien);
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousNotDataFoundException("Le praticien n'existe pas");
        }
        try {
            if ($this->praticienRepository->getPraticienById($rdv->praticien)->getSpecialite()->getId() != $rdv->specialite) {
                throw new ServiceRendezVousIncorrectDataException();
            }
        } catch (ServiceRendezVousIncorrectDataException $e) {
            throw new ServiceRendezVousIncorrectDataException('La spécialité ne correspond pas à celle du praticien');
        }

        try {
            print_r($this->listeDisposPraticienIndividuel($rdv->praticien, $rdv->date, $rdv->date));

            if (!in_array(new DateTimeImmutable($rdv->date), $this->listeDisposPraticienIndividuel($rdv->praticien, $rdv->date, $rdv->date))) {
                print_r('----------------------------------probleme de date');
                throw new ServiceRendezVousIncorrectDataException();
            }
        } catch (ServiceRendezVousIncorrectDataException $e) {
            throw new ServicePraticienInvalidDataException("La date est invalide.");
        }


        $retour = new RendezVous($rdv->praticien, $rdv->patient, $rdv->specialite, new \DateTimeImmutable($rdv->date));

        $this->displayInLogger('Rendez-vous créer : Praticien -> ' . $rdv->praticien . ' / Patient -> ' . $rdv->patient . ' / Specialite -> ' . $rdv->specialite);

        $this->rendezvousRepository->save($retour);

        return new RendezVousDTO($retour);
    }


    public function listeDisposPraticien(string $id_prat, string $date_deb, string $date_fin): array
    {

        //retour
        $retour = [];

        $praticien = $this->praticienRepository->getPraticienById($id_prat);


        //day
        $deb_day = new DateTimeImmutable($date_deb);
        $fin_day = new DateTimeImmutable($date_fin);
        $interval_day = new DateInterval('P1D');

        //hour
        $deb_hour = new DateTimeImmutable(self::HEURE_RDV_DEBUT);
        $fin_hour = new DateTimeImmutable(self::HEURE_RDV_FIN);
        $interval_hour = new DateInterval(self::INTERVAL_HEURE);

        $period_day = new DatePeriod($deb_day, $interval_day, $fin_day->modify('+1 day'));

        foreach ($period_day as $day) {

            // Vérifie si le jour est un samedi (6) ou un dimanche (7)
            if (in_array($day->format('N'), [6, 7]))
                continue; // Saute ce jour

            $deb_hour_of_day = $day->setTime($deb_hour->format('H'), $deb_hour->format('i'));
            $fin_hour_of_day = $day->setTime($fin_hour->format('H'), $fin_hour->format('i'));
            $period_hour = new DatePeriod($deb_hour_of_day, $interval_hour, $fin_hour_of_day);

            foreach ($period_hour as $hour) {

                // Exclure la pause de midi (entre 12h00 et 14h00)
                if ($hour->format('H') >= 12 && $hour->format('H') < 14)
                    continue; // Saute cet horaire

                if ($this->praticienEstDisponible($id_prat, $hour))
                    $retour[] = [
                        'jour' => $day->format('l'), // Nom du jour (en anglais)
                        'date' => $day->format('Y-m-d'), // Date du jour
                        'heure' => $hour->format('H:i'), // Heure disponible
                        'praticien_nom' => $praticien->nom, // Nom du praticien
                        'praticien_prenom' => $praticien->prenom // Prénom du praticien
                    ];
            }
        }

        return $retour;
    }



    public function listeDisposPraticienIndividuel(string $id_prat, string $date_debut, string $date_fin): array
    {
        // Retour : on conserve un tableau d'objets DateTimeImmutable
        $retour = [];

        $praticien = $this->praticienRepository->getPraticienById($id_prat);

        // Day
        $deb_day = new DateTimeImmutable($date_debut);
        $fin_day = new DateTimeImmutable($date_fin);
        $interval_day = new DateInterval('P1D');

        // Hour
        $deb_hour = new DateTimeImmutable($praticien->heure_debut);
        $fin_hour = new DateTimeImmutable($praticien->heure_fin);
        $interval_hour = new DateInterval(self::INTERVAL_HEURE);

        $period_day = new DatePeriod($deb_day, $interval_day, $fin_day->modify('+1 day'));

        foreach ($period_day as $day) {
            // Vérifie si le jour est un samedi (6) ou un dimanche (7) ou un jour de vacances
            if (in_array($day->format('N'), $praticien->jours_semaine_off)) {
                var_dump($day->format('Y-m-d') . " est un jour non ouvré.");
                continue;
            }

            // Vérification des périodes de vacances
            if ($this->dateEstVacance($day, $praticien->vacance_period)) {
                var_dump($day->format('Y-m-d') . " est en vacances.");
                continue;
            }

            $deb_hour_of_day = $day->setTime($deb_hour->format('H'), $deb_hour->format('i'));
            $fin_hour_of_day = $day->setTime($fin_hour->format('H'), $fin_hour->format('i'));
            $period_hour = new DatePeriod($deb_hour_of_day, $interval_hour, $fin_hour_of_day);

            foreach ($period_hour as $hour) {
                // Exclure la pause de midi (entre 12h00 et 14h00)
                if ($hour->format('H') >= 12 && $hour->format('H') < 14) {
                    continue; // Saute cet horaire
                }

                // Si le praticien est disponible à cette heure, on l'ajoute
                if ($this->praticienEstDisponible($id_prat, $hour)) {
                    // Ajouter l'objet DateTimeImmutable seulement si non présent dans le tableau
                    if (!in_array($hour, $retour)) {
                        $retour[] = $hour; // Ajout de l'objet DateTimeImmutable
                    }
                }
            }
        }

        return $retour; // Retourne un tableau d'objets DateTimeImmutable
    }



    private function dateEstVacance(DateTimeImmutable $date, array $vacationPeriods): bool
    {
        foreach ($vacationPeriods as $period) {
            $start = new DateTimeImmutable($period['start']);
            $end = new DateTimeImmutable($period['end']);
            if ($date >= $start && $date <= $end->modify('+1 day')) {
                var_dump($date->format('Y-m-d'), " est dans la période: {$start->format('Y-m-d')} à {$end->format('Y-m-d')}");
                return true; // La date est dans les vacances
            }
        }
        return false; // La date n'est pas dans les vacances
    }

    public function rdvsDePatient(string $id_patient): array
    {
        return $this->rendezvousRepository->getRendezvousByPatientId($id_patient);
    }





    public function praticienEstDisponible($id_prat, DateTimeImmutable $date): bool
    {

        $res = true;

        $liste_rdv_prat = $this->rendezvousRepository->getRendezvousByPraticienId($id_prat);

        foreach ($liste_rdv_prat as $rdv) {

            if ($rdv->date == $date)
                $res = false;
        }

        return $res;
    }



    public function changePatient(string $id, string $new_patient)
    {

        $rdv = $this->rendezvousRepository->getRendezvousById($id);

        $rdv->setPatient($new_patient);

        $this->displayInLogger('Le patient du rendez-vous *' . $id . '* devient : *' . $new_patient . '*');
    }


    public function displayInLogger(string $message)
    {
        $logger = new Logger('logger');

        $logger->pushHandler(new StreamHandler('../logs/app.log'));

        $logger->info($message);
    }

    public function changeSpecialite(string $id, string $new_spe)
    {

        $rdv = $this->rendezvousRepository->getRendezvousById($id);

        $rdv->setSpecialite($new_spe);

        $this->displayInLogger('La specialite du rendez-vous *' . $id . '* devient : *' . $new_spe . '*');
    }


    public function getRendezvousById(string $id): RendezVousDTO
    {
        try {
            $rdv = $this->rendezvousRepository->getRendezvousById($id);
            return new RendezVousDTO($rdv);
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousNotDataFoundException();
        }
    }


    public function annulerRendezvous(string $id_rdv, string $annulePar)
    {
        try {
            // Récupérer le rendez-vous par son ID
            $rdv = $this->rendezvousRepository->getRendezvousById($id_rdv);
        } catch (RepositoryEntityNotFoundException $e) {
            // Lever une exception personnalisée si le rendez-vous n'existe pas
            throw new ServiceRendezVousNotDataFoundException("Rendez-vous inexistant", 404);
        }

        // Vérifier si le rendez-vous a déjà été honoré
        if ($rdv->getStatut() === 'Honoré') {
            throw new DomainException('Le rendez-vous étant déjà honoré, il ne peut pas être annulé');
        }

        // Vérifier qui annule le rendez-vous et mettre à jour le statut en conséquence
        if ($annulePar === 'patient') {
            $rdv->setStatut('Annulé par le patient');
        } elseif ($annulePar === 'praticien') {
            $rdv->setStatut('Annulé par le praticien');
        } else {
            // Lever une exception si la valeur fournie pour "annulerPar" est invalide
            throw new \InvalidArgumentException('Annulation invalide : "annulerPar" doit etre "patient" ou "praticien"');
        }

        // Sauvegarder les modifications dans le repository
        $this->rendezvousRepository->save($rdv);


        // Enregistrer l'annulation dans les logs
        $this->displayInLogger('Le rendez-vous *' . $id_rdv . '* a été annulé.');
    }

    public function marquerRendezvousHonore(string $id_rdv)
    {
        $rdv = $this->rendezvousRepository->getRendezvousById($id_rdv);
        if ($rdv->getStatut() === 'Annulé par le patient' || $rdv->getStatut() === 'Annulé par le praticien') {
            throw new \DomainException('Le rendez-vous étant déjà annulé il ne peut pas être honoré');
        }
        $rdv->setStatut('Honoré');
        $this->rendezvousRepository->save($rdv);
        $this->displayInLogger('Le rendez-vous *' . $id_rdv . '* a été honorer.');
    }

    public function marquerRendezvousNonHonore(string $id_rdv)
    {
        $rdv = $this->rendezvousRepository->getRendezvousById($id_rdv);
        $rdv->setStatut('Non honoré');
        $this->rendezvousRepository->save($rdv);
        $this->displayInLogger('Le rendez-vous *' . $id_rdv . '* n\'est pas honoré.');
    }

    public function marquerRendezvousPaye(string $id_rdv)
    {
        $rdv = $this->rendezvousRepository->getRendezvousById($id_rdv);
        if ($rdv->getStatut() !== 'Honoré') {
            throw new \DomainException('Le rendez-vous doit être honoré avant d\'être payé.');
        }
        $rdv->setStatut('Payé');
        $this->rendezvousRepository->save($rdv);
        $this->displayInLogger('Le rendez-vous *' . $id_rdv . '* a été payer.');
    }

    public function transmettreAuxOrganismes(string $id_rdv)
    {
        $rdv = $this->rendezvousRepository->getRendezvousById($id_rdv);
        if ($rdv->getStatut() !== 'Payé') {
            throw new \DomainException('Le rendez-vous doit être payé avant transmission aux organismes sociaux.');
        }
        $rdv->setStatut('Transmis aux organismes sociaux');
        $this->rendezvousRepository->save($rdv);
        $this->displayInLogger('Le rendez-vous *' . $id_rdv . '* a été transmis aux organismes sociaux.');
    }
}
