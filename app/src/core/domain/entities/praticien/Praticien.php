<?php

namespace toubeelib\core\domain\entities\praticien;

use toubeelib\core\domain\entities\Entity;
use toubeelib\core\dto\PraticienDTO;

class Praticien extends Entity
{
    protected string $nom;
    protected string $prenom;
    protected string $adresse;
    protected string $tel;
    protected ?Specialite $specialite = null;

    protected array $jours_semaine_off = [6, 7];
    protected array $vacance_period = [];

    protected string $heure_debut = '08:00';
    protected string $heure_fin = '18:00';

    public function __construct(string $nom, string $prenom, string $adresse, string $tel)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->tel = $tel;
    }

    public function setSpecialite(Specialite $specialite): void
    {
        $this->specialite = $specialite;
    }

    public function getSpecialite(): ?Specialite
    {
        return $this->specialite;
    }

    public function toDTO(): PraticienDTO
    {
        return new PraticienDTO($this);
    }

    // Méthodes pour les jours de semaine off
    public function addJourOff(int $jour): void
    {
        if (!in_array($jour, $this->jours_semaine_off)) {
            $this->jours_semaine_off[] = $jour;
        }
    }

    public function removeJourOff(int $jour): void
    {
        $this->jours_semaine_off = array_diff($this->jours_semaine_off, [$jour]);
    }

    // Méthodes pour la période de vacances
    public function addVacancePeriod(string $start, string $end): void
    {
        $this->vacance_period[] = ['start' => $start, 'end' => $end];
    }

    // Méthodes pour les heures de début et de fin
    public function setHeureDebut(string $heure): void
    {
        $this->heure_debut = $heure;
    }

    public function setHeureFin(string $heure): void
    {
        $this->heure_fin = $heure;
    }
}
