<?php

namespace rdvs\core\services\auth;

use Exception;
use rdvs\core\dto\AuthDTO;
use rdvs\core\repositoryInterfaces\AuthRepositoryInterface;
use rdvs\core\dto\InputAuthDTO;
use rdvs\core\domain\entities\user\User;

class ServiceAuth implements ServiceAuthInterface
{
    private AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function checkCredentials(string $email, string $password): AuthDTO
    {
        // Rechercher l'utilisateur par email
        $user = $this->authRepository->findByEmail($email);

        if (!$user) {
            throw new Exception("Utilisateur non trouvé");
        }

        // Vérifier le mot de passe

        if (!password_verify($password, $user->password)) {
            throw new Exception("Mot de passe incorrect");
        }

        // Créer et retourner le DTO
        return new AuthDTO(
            $user
        );
    }


    public function createUser(InputAuthDTO $a): AuthDTO
    {
        $user = new User($a->email, $a->password, $a->role);
        $this->authRepository->getUserById(($this->authRepository->save($user)));
        return new AuthDTO($user);
    }

    public function canAccessPraticien(string $role): bool
    {

        switch ($role) {
            case 'admin':
                return true;
                break;
            case 'praticien':
                return true;
                break;
            default:
                return false;
                break;
        }
    }
}
