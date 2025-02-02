<?php

namespace rdvs\core\dto;

use rdvs\core\domain\entities\user\User;
use rdvs\core\dto\DTO;

class AuthDTO extends DTO
{
    protected string $ID;
    protected string $email;
    protected string $role;

    public function __construct(User $u)
    {
        $this->ID = $u->getID();
        $this->email = $u->email;
        $this->role = $u->role;
    }
}
