<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\rdv\RendezVous;

interface RdvRepositoryInterface
{
    public function save(RendezVous $rdv): string;
    public function consulerRdvById(int $id): RendezVous;
}