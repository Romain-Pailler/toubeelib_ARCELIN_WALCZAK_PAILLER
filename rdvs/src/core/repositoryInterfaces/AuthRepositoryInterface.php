<?php

namespace rdvs\core\repositoryInterfaces;

use rdvs\core\domain\entities\user\User;

interface AuthRepositoryInterface
{

    public function findByEmail(string $email): User;
    public function save(User $user): string;
    public function getUserById(string $id): User;
}
