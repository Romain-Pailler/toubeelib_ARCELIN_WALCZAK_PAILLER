<?php

namespace rdvs\core\repositoryInterfaces;

use rdvs\core\domain\entities\praticien\Praticien;
use rdvs\core\domain\entities\praticien\Specialite;
use rdvs\core\domain\entities\rdv\Rendezvous;

interface RdvRepositoryInterface
{

    public function getRendezvousById(string $id): Rendezvous;
    public function getRendezvousByPraticienId(string $id_prat): array;

    public function save(Rendezvous $rdv): string;

}