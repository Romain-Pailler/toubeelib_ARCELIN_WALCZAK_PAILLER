<?php

namespace toubeelib\core\repositoryInterfaces;

use toubeelib\core\domain\entities\user\User;

interface AuthRepositoryInterface
{

    public function findByEmail(string $email): User;
    public function save(User $user): string;
    public function getUserById(string $id): User;
}
