<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\RendezVousDTO;

interface ServiceRendezVousInterface
{

    public function creerRendezvous(InputRendezVousDTO $informations): RendezVousDTO;
    public function annulerRendezvous(string $id, string $annuler_par);


}