<?php

namespace toubeelib\core\services\rdv;

class ServiceRendezVousNotDataFoundException extends \Exception
{
    public function __construct($message = "Data not found", $code = 404, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}