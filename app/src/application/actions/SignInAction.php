<?php

namespace toubeelib\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\auth\AuthProvider;
use toubeelib\core\services\auth\ServiceAuthInvalidCredentialsException;

class SignInAction extends AbstractAction
{
    private AuthProvider $authProvider;

    public function __construct(AuthProvider $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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
                    ->withStatus(400);
            }

            // Authentification via le fournisseur d'authentification
            $tokens = $this->authProvider->signin($email, $password);

            // Réponse avec les tokens générés
            $response->getBody()->write(json_encode($tokens));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (Exception $e) {
            // Gestion des erreurs d'authentification
            $response->getBody()->write(json_encode(['error' => 'Identifiants invalides.']));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        } catch (\Exception $e) {
            // Gestion des erreurs imprévues
            $response->getBody()->write(json_encode(['error' => 'Erreur interne du serveur : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
