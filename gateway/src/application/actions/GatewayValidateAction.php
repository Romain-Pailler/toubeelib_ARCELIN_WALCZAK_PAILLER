<?php

namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GatewayValidateAction extends AbstractAction
{
    private ClientInterface $authClient;

    public function __construct(ClientInterface $client)
    {
        $this->authClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Récupérer le token depuis l'en-tête Authorization
        $authHeader = $request->getHeaderLine('Authorization');
        $token = null;

        if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        }

        // Si aucun token n'est trouvé, retourner une erreur
        if (empty($token)) {
            return $this->jsonError($response, 'Token manquant.', 400);
        }

        try {
            // Envoyer le token au microservice d'authentification
            $authResponse = $this->authClient->post('auth/validate', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            // Retourner la réponse du microservice
            $responseData = json_decode($authResponse->getBody()->getContents(), true);
            $response->getBody()->write(json_encode($responseData));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (ClientException $e) {
            // Capture les détails de la réponse d'erreur
            $responseBody = $e->getResponse()->getBody()->getContents();
            error_log("Erreur dans la réponse du serveur d'authentification: " . $responseBody);

            return $this->jsonError($response, 'Token invalide. Détails: ' . $e->getMessage(), 401);
        } catch (\Exception $e) {
            // Capture d'une erreur générique
            error_log("Erreur interne: " . $e->getMessage());
            return $this->jsonError($response, 'Erreur interne du serveur : ' . $e->getMessage(), 500);
        }
    }


    private function jsonError(ResponseInterface $response, string $message, int $status)
    {
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
