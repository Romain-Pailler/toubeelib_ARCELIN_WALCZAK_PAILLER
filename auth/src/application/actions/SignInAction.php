<?php

namespace auth\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use auth\core\services\auth\AuthProvider;

class SignInAction
{
    private AuthProvider $authProvider;

    public function __construct(AuthProvider $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            // Récupérer les données du corps de la requête
            $body = $request->getParsedBody();
            $email = $body['email'] ?? null;
            $password = $body['password'] ?? null;

            // Validation des données
            if (empty($email) || empty($password)) {
                $response->getBody()->write(json_encode(['error' => 'Email et mot de passe sont obligatoires.']));
                return $response->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);  // Code d'erreur pour une requête mal formée
            }

            // Authentification via le fournisseur d'authentification
            $tokens = $this->authProvider->signin($email, $password);

            // Réponse avec les tokens générés
            $response->getBody()->write(json_encode([
                'message' => 'Authentification réussie',
                'data' => [
                    'access_token' => $tokens['access_token'],
                    'refresh_token' => $tokens['refresh_token']
                ]
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(200);  // Code de succès
        } catch (Exception $e) {
            // Gestion des erreurs d'authentification
            $response->getBody()->write(json_encode(['error' => 'Identifiants invalides.']));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(401);  // Code d'erreur pour des identifiants invalides
        } catch (Exception $e) {
            // Gestion des erreurs imprévues
            $response->getBody()->write(json_encode(['error' => 'Erreur interne du serveur : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(500);  // Code d'erreur pour une erreur serveur
        }
    }
}
