<?php

namespace rdvs\core\services\auth;

use rdvs\core\dto\AuthDTO;

interface ServiceAuthInterface
{

    public function checkCredentials(string $email, string $password): AuthDTO;
}
