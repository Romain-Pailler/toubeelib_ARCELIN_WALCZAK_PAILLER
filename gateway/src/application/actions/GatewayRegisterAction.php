<?php

namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GatewayRegisterAction extends AbstractAction
{
    private ClientInterface $authClient;

    public function __construct(ClientInterface $client)
    {
        $this->authClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Récupérer les données envoyées dans la requête POST
        $body = $request->getParsedBody();
        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;
        $role = $body['role'] ?? 'user';  // Role par défaut à "user"

        // Validation des données
        if (empty($email) || empty($password)) {
            $response->getBody()->write(json_encode(['error' => 'Email et mot de passe sont obligatoires.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            // Utiliser l'authClient pour appeler le microservice d'authentification pour l'enregistrement
            $registerResponse = $this->authClient->post('auth/register', [
                'json' => [
                    'email' => $email,
                    'password' => $password,
                    'role' => $role,
                ]
            ]);

            // Récupérer la réponse du microservice Auth (les tokens JWT)
            $tokens = json_decode($registerResponse->getBody()->getContents(), true);

            // Retourner les tokens à l'utilisateur
            $response->getBody()->write(json_encode($tokens));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);  // Code 201 pour la création
        } catch (ClientException $e) {
            // En cas d'erreur (par exemple, utilisateur déjà existant)
            $response->getBody()->write(json_encode(['error' => 'Erreur lors de l\'inscription.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);  // Code d'erreur 400
        } catch (\Exception $e) {
            // En cas d'erreur générale
            $response->getBody()->write(json_encode(['error' => 'Erreur interne du serveur : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);  // Code d'erreur serveur
        }
    }
}
