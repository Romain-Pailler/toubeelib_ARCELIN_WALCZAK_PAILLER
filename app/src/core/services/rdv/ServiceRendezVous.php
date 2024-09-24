<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\domain\entities\rdv\RendezVous;
use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\RendezVousDTO;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use Monolog\Handler\RotatingFileHandler;


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

        $this->rendezvousRepository->save($retour);
    
        return new RendezVousDTO($retour);

    }



    public function changePatient( string $id, string $new_patient ){

        $rdv = $this->rendezvousRepository->getRendezvousById($id);


        // Log to a new file every day
        $log->

        $rdv->setPatient($new_patient);
    }

    public function changeSpecialite( string $id, string $new_spe ){

        $rdv = $this->rendezvousRepository->getRendezvousById($id);

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

    }
}