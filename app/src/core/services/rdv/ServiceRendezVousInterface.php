<?php

namespace toubeelib\core\services\rdv;

use toubeelib\core\dto\InputRendezVousDTO;
use toubeelib\core\dto\RendezVousDTO;

interface ServiceRendezVousInterface
{

    public function creerRendezvous(InputRendezVousDTO $informations): RendezVousDTO;
    public function annulerRendezvous(string $id, string $annuler_par);
    public function getRendezvousById(string $id): RendezVousDTO;
    public function listeDisposPraticien(string $id_prat, string $date_deb, string $date_fin): array;
    public function listeDisposPraticienIndividuel(string $id_prat, string $date_debut, string $date_fin): array;
    public function changePatient(string $id, string $new_patient);
    public function changeSpecialite(string $id, string $new_spe);
}
