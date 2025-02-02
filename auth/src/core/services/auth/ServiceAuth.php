<?php

namespace auth\core\services\auth;

use Exception;
use auth\core\dto\AuthDTO;
use auth\core\repositoryInterfaces\AuthRepositoryInterface;
use auth\core\dto\InputAuthDTO;
use auth\core\domain\entities\user\User;

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

    public function checkCredentialsById(string $id): AuthDTO
    {
        // Recherche de l'utilisateur par ID
        $user = $this->authRepository->getUserById($id);

        if (!$user) {
            throw new Exception("Utilisateur non trouvé");
        }

        // Créer et retourner le DTO
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
