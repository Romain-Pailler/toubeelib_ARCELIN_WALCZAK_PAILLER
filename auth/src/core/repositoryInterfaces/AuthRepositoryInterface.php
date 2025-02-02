<?php

namespace auth\core\repositoryInterfaces;

use auth\core\domain\entities\user\User;

interface AuthRepositoryInterface
{

    public function findByEmail(string $email): User;
    public function save(User $user): string;
    public function getUserById(string $id): User;
}
