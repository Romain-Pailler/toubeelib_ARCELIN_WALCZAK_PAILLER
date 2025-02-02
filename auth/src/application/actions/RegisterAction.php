<?php

namespace auth\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use auth\core\services\auth\ServiceAuthInterface;
use auth\core\dto\InputAuthDTO;

class RegisterAction
{
    private ServiceAuthInterface $authService;

    public function __construct(ServiceAuthInterface $authService)
    {
        $this->authService = $authService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            // Récupérer les données du corps de la requête
            $body = $request->getParsedBody();
            $email = $body['email'] ?? null;
            $password = $body['password'] ?? null;
            $role = $body['role'] ?? null;

            // Validation des données
            if (empty($email) || empty($password) || empty($role)) {
                $response->getBody()->write(json_encode(['error' => 'Email, mot de passe et rôle sont obligatoires.']));
                return $response->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);  // Code d'erreur pour une requête mal formée
            }

            // Créer un DTO d'entrée pour l'inscription
            $inputAuthDTO = new InputAuthDTO($email, $password, $role);

            // Créer l'utilisateur via le service d'authentification
            $authDTO = $this->authService->createUser($inputAuthDTO);

            // Réponse avec les détails de l'utilisateur créé
            $response->getBody()->write(json_encode([
                'message' => 'Utilisateur créé avec succès',
                'data' => [
                    'email' => $authDTO->email,
                    'role' => $authDTO->role
                ]
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(201);  // Code de succès pour la création
        } catch (Exception $e) {
            // Gestion des erreurs de création d'utilisateur
            $response->getBody()->write(json_encode(['error' => 'Erreur lors de la création de l\'utilisateur : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(500);  // Code d'erreur pour une erreur serveur
        }
    }
}
