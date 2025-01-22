<?php

namespace toubeelib\core\services\auth;

use toubeelib\core\dto\AuthDTO;

interface ServiceAuthInterface
{

    public function checkCredentials(string $email, string $password): AuthDTO;
}
