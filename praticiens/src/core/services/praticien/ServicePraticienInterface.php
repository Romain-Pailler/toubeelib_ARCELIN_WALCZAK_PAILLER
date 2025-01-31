<?php

namespace praticiens\core\services\praticien;

use praticiens\core\dto\InputPraticienDTO;
use praticiens\core\dto\PraticienDTO;
use praticiens\core\dto\SpecialiteDTO;

interface ServicePraticienInterface
{

    public function createPraticien(InputPraticienDTO $p): PraticienDTO;
    public function getPraticienById(string $id): PraticienDTO;
    public function getSpecialiteById(string $id): SpecialiteDTO;
    public function getPraticiensBySpecialite(string $spe): array;
    public function getPraticiensByCity(string $city): array;
    public function getPraticien(): array;
}
