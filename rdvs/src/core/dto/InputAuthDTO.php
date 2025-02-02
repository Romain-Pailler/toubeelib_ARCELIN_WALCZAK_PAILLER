<?php

namespace rdvs\core\dto;

class InputAuthDTO extends DTO
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
