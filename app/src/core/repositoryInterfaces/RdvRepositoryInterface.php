<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\rdv\Praticien;
use toubeelib\core\domain\entities\rdv\Specialite;

interface RdvRepositoryInterface
{
    public function save(Rdv $praticien): string;
    public function getRdvById(string $id): Rdv;
}