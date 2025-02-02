<?php

namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GatewayRefreshAction extends AbstractAction
{
    private ClientInterface $authClient;

    public function __construct(ClientInterface $client)
    {
        $this->authClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Récupérer le refresh token depuis l'en-tête Authorization
        $authHeader = $request->getHeaderLine('Authorization');
        $refreshToken = null;

        if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $refreshToken = $matches[1];
        }

        // Si aucun refresh token n'est trouvé, retourner une erreur
        if (empty($refreshToken)) {
            return $this->jsonError($response, 'Refresh token manquant.', 400);
        }

        try {
            // Envoyer le refresh token au microservice d'authentification pour obtenir de nouveaux tokens
            $refreshResponse = $this->authClient->post('auth/refresh', [
                'json' => ['refresh_token' => $refreshToken]
            ]);

            // Récupérer la réponse du microservice d'authentification (nouveaux tokens)
            $tokens = json_decode($refreshResponse->getBody()->getContents(), true);

            // Retourner les tokens au client
            $response->getBody()->write(json_encode($tokens));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (ClientException $e) {
            // Capture l'erreur de l'API d'authentification (refresh token invalide ou expiré)
            return $this->jsonError($response, 'Refresh token invalide ou expiré.' . $refreshToken, 401);
        } catch (\Exception $e) {
            // Capture d'une erreur interne
            return $this->jsonError($response, 'Erreur interne du serveur : ' . $e->getMessage(), 500);
        }
    }

    private function jsonError(ResponseInterface $response, string $message, int $status)
    {
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
