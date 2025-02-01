<?php

namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GatewaySignInAction extends AbstractAction
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

        // Valider les données (email et mot de passe doivent être présents)
        if (empty($email) || empty($password)) {
            $response->getBody()->write(json_encode(['error' => 'Email et mot de passe sont obligatoires.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            // Utiliser l'authClient pour appeler le microservice d'authentification
            $authResponse = $this->authClient->post('auth/signin', [
                'json' => [
                    'email' => $email,
                    'password' => $password
                ]
            ]);


            // Récupérer la réponse du microservice Auth (les tokens JWT)
            $tokens = json_decode($authResponse->getBody()->getContents(), true);

            // Retourner les tokens à l'utilisateur
            $response->getBody()->write(json_encode($tokens));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (ClientException $e) {
            // En cas d'erreur (par exemple, identifiants invalides)
            $response->getBody()->write(json_encode(['error' => 'Identifiants invalides.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        } catch (\Exception $e) {
            // En cas d'erreur générale
            $response->getBody()->write(json_encode(['error' => 'Erreur interne du serveur : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
