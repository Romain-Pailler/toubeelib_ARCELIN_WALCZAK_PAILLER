<?php

namespace praticiens\core\repositoryInterfaces;

class RepositoryEntityNotFoundException extends \Exception
{
    public function __construct($message = "Entity not found", $code = 404, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
