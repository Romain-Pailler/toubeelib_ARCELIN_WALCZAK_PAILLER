<?php

namespace auth\core\services\auth;

use auth\core\dto\InputAuthDTO;
use auth\core\dto\AuthDTO;

interface ServiceAuthInterface
{

    public function checkCredentials(string $email, string $password): AuthDTO;
    public function createUser(InputAuthDTO $a): AuthDTO;
    public function checkCredentialsById(string $id): AuthDTO;
}
