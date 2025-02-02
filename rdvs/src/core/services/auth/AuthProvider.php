<?php

namespace rdvs\core\services\auth;

use Firebase\JWT\JWT;
use rdvs\core\dto\AuthDTO;
use rdvs\core\repositoryInterfaces\AuthRepositoryInterface;
use rdvs\core\services\auth\ServiceAuthInterface;

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
}
