<?php

namespace rdvs\core\domain\entities\rdv;

use rdvs\core\domain\entities\Entity;
use rdvs\core\dto\RendezvousDTO;

class RendezVous extends Entity
{

    protected String $praticien;
    protected String $patient;
    protected String $specialite;
    protected String $statut;
    protected \DateTimeImmutable $date;

    public function __construct(string $prat, string $pat, string $spe, \DateTimeImmutable $date) {

        $this->praticien=$prat;
        $this->patient=$pat;
        $this->specialite=$spe;
        $this->date=$date;
        $this->statut='prévu';

    }

    public function setStatut(string $value){
        $this->statut=$value;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setPatient(string $patient){
        $this->patient=$patient;
    }

    public function setSpecialite(string $spe){
        $this->specialite=$spe;
    }


    public function toDTO(): RendezvousDTO
    {
        return new RendezvousDTO($this);
    }

}