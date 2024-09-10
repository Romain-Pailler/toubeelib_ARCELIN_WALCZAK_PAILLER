<?php

namespace toubeelib\core\domain\entities\rdv;


use toubeelib\core\domain\entities\Entity;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\rdv\RdvNotFoundException;
use DateTime;
use Ramsey\Uuid\Uuid;
use toubeelib\core\dto\RdvDTO;

class RendezVous extends Entity
{   
    /**
     * Déclarations des attributs
    */

    //protected Patient $patient;
    protected Praticien $praticien;
    protected DateTime $date;
    protected String $desc;
    protected String $statue;
    protected String $type;
    protected String $specialite;

    /**
     * Constructeur
     */
    public function __construct(Praticien $prat, DateTime $date, String $desc)
    {
        $this->praticien=$prat;
        $this->date=$date;
        $this->desc=$desc;
    }

    /**
     * Fonction créerRendezvous
     */
    public static function creerRendezvous(array $informations){
        new Rdv($informations['praticien'],$informations['date'],$informations['desc']);
    }

    /**
     * Fonction afficherInformations
     */
    public function afficherInformations(int $id){
        if(rdv_exist($id))
            return $this.toDTO();
        else
            return new RdvNotFoundException();
    }

    /**
     * Fonction toDTO
     */
    public function toDTO(): RdvDTO
    {
        return new RdvDTO($this);
    }
}