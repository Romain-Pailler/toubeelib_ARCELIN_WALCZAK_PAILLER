<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\domain\entities\rdv\RendezVous;
use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\RendezVousDTO;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

use Monolog\Logger; // Use the correct namespace for Logger
use Monolog\Handler\StreamHandler; // Use the correct namespace for StreamHandler
use Monolog\Handler\FirePHPHandler; // Use the correct namespace for FirePHPHandler


class ServiceRendezVous implements ServiceRendezVousInterface
{
    private RdvRepositoryInterface $rendezvousRepository;
    private PraticienRepositoryInterface $praticienRepository;


    public function __construct(RdvRepositoryInterface $rdvRepository, PraticienRepositoryInterface $praticienRepository)
    {
        $this->rendezvousRepository = $rdvRepository;
        $this->praticienRepository = $praticienRepository;
    }

    public function creerRendezvous(InputRendezVousDTO $rdv): RendezVousDTO
    {

        //praticien ID valide ?
        if($this->praticienRepository->getPraticienById($rdv->praticien)==null)throw new ServiceRendezVousNoDataFoundException('invalid Praticien ID');

        //Specialite valide ?
        if($this->praticienRepository->getPraticienById($rdv->praticien)->getSpecialite()->getId()!=$rdv->specialite)throw new ServiceRendezVousIncorrectDataException('invalid Specialite');

        $retour = new RendezVous($rdv->praticien, $rdv->patient, $rdv->specialite, new \DateTimeImmutable($rdv->date));

        $this->displayInLogger('Rendez-vous créer : Praticien -> '.$rdv->praticien.' / Patient -> '.$rdv->patient.' / Specialite -> '.$rdv->specialite);

        $this->rendezvousRepository->save($retour);
    
        return new RendezVousDTO($retour);

    }



    public function changePatient( string $id, string $new_patient ){

        $rdv = $this->rendezvousRepository->getRendezvousById($id);

        $this->displayInLogger('Le patient du rendez-vous *'.$id.'* devient : *'.$new_patient.'*');
        
        $rdv->setPatient($new_patient);

    }


    public function displayInLogger(string $message){
        $logger = new Logger('logger');

        $logger->pushHandler(new StreamHandler(__DIR__.'/app.log'));

        $logger->info($message);
    }

    public function changeSpecialite( string $id, string $new_spe ){

        $rdv = $this->rendezvousRepository->getRendezvousById($id);

        $this->displayInLogger('La specialite du rendez-vous *'.$id.'* devient : *'.$new_spe.'*');
        

        $rdv->setSpecialite($new_spe);
    }


    public function getRendezvousById(string $id): RendezVousDTO
    {
        try {
            $praticien = $this->rendezvousRepository->getRendezvousById($id);
            return new RendezVousDTO($praticien);
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServiceRendezVousNoDataFoundException('invalid Praticien ID');
        }
    }


    public function annulerRendezvous(string $id_rdv)
    {

        $rdv=$this->rendezvousRepository->getRendezvousById($id_rdv);
        $rdv->setStatut('Annuler');

        $this->displayInLogger('Le rendez-vous *'.$id_rdv.'* a été annuler');

    }
}