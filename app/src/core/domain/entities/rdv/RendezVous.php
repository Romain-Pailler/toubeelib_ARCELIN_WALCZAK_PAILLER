<?php

namespace toubeelib\core\domain\entities\rdv;

use toubeelib\core\domain\entities\Entity;
use toubeelib\core\dto\RendezvousDTO;

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


    public function toDTO(): RendezvousDTO
    {
        return new RendezvousDTO($this);
    }

}