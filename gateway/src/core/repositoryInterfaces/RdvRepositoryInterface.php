<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\domain\entities\rdv\Rendezvous;

interface RdvRepositoryInterface
{

    public function getRendezvousById(string $id): Rendezvous;
    public function getRendezvousByPraticienId(string $id_prat): array;

    public function save(Rendezvous $rdv): string;

}