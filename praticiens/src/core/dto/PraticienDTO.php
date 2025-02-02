<?php

namespace praticiens\core\dto;

use praticiens\core\domain\entities\praticien\Praticien;
use praticiens\core\dto\DTO;

class PraticienDTO extends DTO
{
    protected string $ID;
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $tel;
    protected string $specialite_label;
    protected string $specialite_id;


    protected array $jours_semaine_off = [];
    protected array $vacance_period = [];

    protected string $heure_debut = '10:00';
    protected string $heure_fin = '18:00';

    public function __construct(Praticien $p)
    {
        $this->ID = $p->getID();
        $this->nom = $p->nom;
        $this->prenom = $p->prenom;
        $this->adresse = $p->adresse;
        $this->tel = $p->tel;
        $this->specialite_label = $p->specialite->label;
        $this->specialite_id = $p->specialite->getID();
    }
}
