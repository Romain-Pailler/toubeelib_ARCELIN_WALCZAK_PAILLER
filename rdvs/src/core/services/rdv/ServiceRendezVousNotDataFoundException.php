<?php

namespace rdvs\core\services\rdv;

class ServiceRendezVousNotDataFoundException extends \Exception
{
    public function __construct($message = "Rendez-vous non trouvé", $code = 404, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}