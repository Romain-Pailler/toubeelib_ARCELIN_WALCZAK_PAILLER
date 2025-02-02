<?php

namespace rdvs\infrastructure\repositories;

use Ramsey\Uuid\Uuid;
use rdvs\core\domain\entities\praticien\Praticien;
use rdvs\core\domain\entities\praticien\Specialite;
use rdvs\core\repositoryInterfaces\PraticienRepositoryInterface;
use rdvs\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ArrayPraticienRepository implements PraticienRepositoryInterface
{

    const SPECIALITES = [
        'A' => [
            'ID' => 'A',
            'label' => 'Dentiste',
            'description' => 'Spécialiste des dents'
        ],
        'B' => [
            'ID' => 'B',
            'label' => 'Ophtalmologue',
            'description' => 'Spécialiste des yeux'
        ],
        'C' => [
            'ID' => 'C',
            'label' => 'Généraliste',
            'description' => 'Médecin généraliste'
        ],
        'D' => [
            'ID' => 'D',
            'label' => 'Pédiatre',
            'description' => 'Médecin pour enfants'
        ],
        'E' => [
            'ID' => 'E',
            'label' => 'Médecin du sport',
            'description' => 'Maladies et trausmatismes liés à la pratique sportive'
        ],
    ];

    private array $praticiens = [];

    public function __construct()
    {
        $this->praticiens['p1'] = new Praticien('Dupont', 'Jean', 'nancy', '0123456789');
        $this->praticiens['p1']->setSpecialite(new Specialite('A', 'Dentiste', 'Spécialiste des dents'));
        $this->praticiens['p1']->addVacancePeriod('2024-09-02', '2024-09-04');
        $this->praticiens['p1']->setID('p1');

        $this->praticiens['p2'] = new Praticien('Durand', 'Pierre', 'vandeuve', '0123456789');
        $this->praticiens['p2']->setSpecialite(new Specialite('B', 'Ophtalmologue', 'Spécialiste des yeux'));
        $this->praticiens['p2']->setID('p2');

        $this->praticiens['p3'] = new Praticien('Martin', 'Marie', '3lassou', '0123456789');
        $this->praticiens['p3']->setSpecialite(new Specialite('C', 'Généraliste', 'Médecin généraliste'));
        $this->praticiens['p3']->setID('p3');
    }

    public function getSpecialiteById(string $id): Specialite
    {

        $specialite = self::SPECIALITES[$id] ??
            throw new RepositoryEntityNotFoundException("Specialite $id not found");

        return new Specialite($specialite['ID'], $specialite['label'], $specialite['description']);
    }

    public function getPraticiensBySpecialite(string $specialite): array
    {

        $retour = [];

        foreach ($this->praticiens as $praticien)
            if ($praticien->specialite->label == $specialite)
                array_push($retour, $praticien);

        return $retour;
    }



    public function getPraticiensByCity(string $city): array
    {

        $retour = [];

        foreach ($this->praticiens as $praticien)
            if ($praticien->adresse == $city)
                array_push($retour, $praticien);

        return $retour;
    }

    public function save(Praticien $praticien): string
    {
        // TODO : prévoir le cas d'une mise à jour - le praticient possède déjà un ID
        $ID = Uuid::uuid4()->toString();
        $praticien->setID($ID);
        $this->praticiens[$ID] = $praticien;
        return $ID;
    }

    public function getPraticienById(string $id): Praticien
    {
        $praticien = $this->praticiens[$id] ??
            throw new RepositoryEntityNotFoundException("Praticien $id not found");

        return $praticien;
    }
}
