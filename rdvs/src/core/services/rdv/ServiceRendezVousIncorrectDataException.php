<?php

namespace rdvs\core\services\rdv;

class ServiceRendezVousIncorrectDataException extends \Exception
{
    public function __construct($message = "Donnée incorrecte", $code = 402, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}