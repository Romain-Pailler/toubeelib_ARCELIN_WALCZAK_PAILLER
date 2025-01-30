<?php

namespace praticiens\core\repositoryInterfaces;

use praticiens\core\domain\entities\praticien\Praticien;
use praticiens\core\domain\entities\praticien\Specialite;

interface PraticienRepositoryInterface
{

    public function getSpecialiteById(string $id): Specialite;
    public function save(Praticien $praticien): string;
    public function getPraticienById(string $id): Praticien;
    public function getPraticiensBySpecialite(string $specialite): array;
    public function getPraticiensByCity(string $city): array;
    public function getPraticiens(): array;
}
