<?php

namespace auth\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use auth\core\services\auth\AuthProvider;

class RefreshAction
{
    private AuthProvider $authProvider;

    public function __construct(AuthProvider $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            // Récupérer le refresh token depuis l'en-tête Authorization
            $refreshToken = $request->getHeaderLine('Authorization');

            if (empty($refreshToken)) {
                $response->getBody()->write(json_encode(['error' => 'Refresh token manquant dans l\'en-tête Authorization']));
                return $response->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);  // Code d'erreur pour une requête mal formée
            }

            $refreshToken = str_replace('Bearer ', '', $refreshToken);

            // Rafraîchir les tokens via le fournisseur d'authentification
            $tokens = $this->authProvider->refreshToken($refreshToken);

            // Réponse avec les nouveaux tokens
            $response->getBody()->write(json_encode([
                'message' => 'Tokens rafraîchis avec succès',
                'data' => [
                    'access_token' => $tokens['access_token'],
                    'refresh_token' => $tokens['refresh_token']
                ]
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(200);  // Code de succès
        } catch (Exception $e) {
            // Gestion des erreurs lors du rafraîchissement du token
            $response->getBody()->write(json_encode(['error' => 'Refresh token invalide ou expiré : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(401);  // Code d'erreur pour un token invalide
        }
    }
}
