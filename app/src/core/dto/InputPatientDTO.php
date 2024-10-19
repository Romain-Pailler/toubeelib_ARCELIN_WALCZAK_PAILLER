<?php

namespace toubeelib\core\dto;

class InputPatientDTO extends DTO
{
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $tel;
    protected string $dateNaissance;

    public function __construct(string $nom, string $prenom, string $adresse, string $tel, string $dateNaissance) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->tel = $tel;
        $this->dateNaissance = $dateNaissance;
    }
}
