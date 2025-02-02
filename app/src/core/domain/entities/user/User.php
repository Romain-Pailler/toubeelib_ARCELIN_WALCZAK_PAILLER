<?php

namespace toubeelib\core\domain\entities\user;

use toubeelib\core\domain\entities\Entity;

class User extends Entity
{
    protected string $email;
    protected string $password;
    protected string $role;


    public function __construct(string $email, string $password, string $role)
    {
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }
}
