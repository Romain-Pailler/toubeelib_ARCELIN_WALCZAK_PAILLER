<?php

namespace rdvs\core\dto;

use rdvs\core\domain\entities\rdv\Rendezvous;
use rdvs\core\dto\DTO;

class RendezVousDTO extends DTO
{
    protected string $ID;
    protected string $praticien;
    protected string $patient;
    protected string $statut;
    protected string $specialite_label;
    protected string $date;

    public function __construct(RendezVous $p)
    {
        $this->ID = $p->getID();
        $this->praticien = $p->praticien;
        $this->patient = $p->patient;
        $this->statut = $p->statut;
        $this->specialite_label = $p->specialite;
        $this->date = $p->date->format('Y-m-d H:i:s');
    }


}