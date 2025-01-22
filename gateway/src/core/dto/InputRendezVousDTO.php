<?php

namespace toubeelib\core\dto;

class InputRendezVousDTO extends DTO
{
    protected string $praticien;
    protected String $patient;
    protected String $specialite;
    protected String $date;


    public function __construct(string $prat, string $pat, string $spe, string $date) {

        $this->praticien=$prat;
        $this->patient=$pat;
        $this->specialite=$spe;
        $this->date=$date;

    }
}