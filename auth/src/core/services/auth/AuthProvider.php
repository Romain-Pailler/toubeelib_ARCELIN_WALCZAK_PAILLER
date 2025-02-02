<?php

namespace auth\core\services\auth;

use Firebase\JWT\JWT;
use auth\core\dto\AuthDTO;
use auth\core\repositoryInterfaces\AuthRepositoryInterface;
use auth\core\services\auth\ServiceAuthInterface;
use Exception;
use Firebase\JWT\Key;

class AuthProvider
{
    private ServiceAuthInterface $authService;
    private string $secretKey;

    public function __construct(ServiceAuthInterface $authService, string $secretKey)
    {
        $this->authService = $authService;
        $this->secretKey = $secretKey;
    }

    public function signin(string $email, string $password): array
    {
        // Vérification des credentials via le service
        $authDTO = $this->authService->checkCredentials($email, $password);

        // Création des tokens JWT
        $accessToken = $this->generateAccessToken($authDTO);
        $refreshToken = $this->generateRefreshToken($authDTO);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ];
    }

    private function generateAccessToken(AuthDTO $authDTO): string
    {
        $payload = [
            'id' => $authDTO->ID,
            'email' => $authDTO->email,
            'role' => $authDTO->role,
            'exp' => time() + 3600 // Expiration du token dans 1 heure
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    private function generateRefreshToken(AuthDTO $authDTO): string
    {
        $payload = [
            'id' => $authDTO->ID,
            'exp' => time() + 1209600 // Expiration du refresh token dans 14 jours
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    // Ajoute cette méthode dans AuthProvider pour gérer le rafraîchissement des tokens
    public function refreshToken(string $refreshToken): array
    {
        try {
            // Décode le refresh token et valide l'expiration
            $decoded = JWT::decode($refreshToken, new Key($this->secretKey, 'HS256'));

            // Générer un nouveau access token et refresh token
            $authDTO = $this->authService->checkCredentialsById($decoded->id);  // On suppose que tu as une méthode qui vérifie un utilisateur par ID

            $accessToken = $this->generateAccessToken($authDTO);
            $newRefreshToken = $this->generateRefreshToken($authDTO);

            return [
                'access_token' => $accessToken,
                'refresh_token' => $newRefreshToken
            ];
        } catch (\Exception $e) {
            throw new Exception('Refresh token invalide ou expiré');
        }
    }
}
