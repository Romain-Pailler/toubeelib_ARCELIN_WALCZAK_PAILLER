<?php 
namespace rdvs\core\repositoryInterfaces;

interface PraticienProviderInterface {
    public function getPraticienById(string $id): array;
}
