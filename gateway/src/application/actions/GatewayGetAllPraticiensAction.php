<?php

namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpInternalServerErrorException;

class GatewayGetAllPraticiensAction extends AbstractAction
{
    private ClientInterface $praticienClient;

    public function __construct(ClientInterface $client)
    {
        $this->praticienClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Récupérer les informations d'authentification du middleware (les données du token)
        $auth = $request->getAttribute('auth'); // Ici on suppose que les infos du token sont attachées à la requête

        // Vérification basique du token, sans validation de rôle dans la gateway
        if (empty($auth)) {
            $response->getBody()->write(json_encode(['error' => 'Token d\'authentification manquant ou invalide.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Ajouter les informations d'authentification dans les en-têtes pour le microservice
        $headers = [
            'Authorization' => 'Bearer ' . $auth['role'],  // On passe juste le rôle ici (ou d'autres données nécessaires)
            'X-User-Id' => $auth['id'],
            'X-User-Role' => $auth['role'],
        ];

        // Paramètres de la requête (si nécessaires)
        $queryParams = $request->getQueryParams();

        // Requête vers l'API des praticiens avec le token
        $apiRequestOptions = [
            'headers' => $headers,
        ];

        if (!empty($queryParams)) {
            $apiRequestOptions['query'] = $queryParams;
        }

        // Appel au microservice
        try {
            $apiResponse = $this->praticienClient->get("praticiens", $apiRequestOptions);

            // Vérifier si la réponse est valide avant de la retourner
            if ($apiResponse->getStatusCode() !== 200) {
                // Si la réponse n'est pas 200, on lève une exception pour le traitement
                throw new HttpInternalServerErrorException($request, "Erreur du serveur des praticiens : " . $apiResponse->getReasonPhrase());
            }

            // Renvoyer la réponse du microservice
            $response->getBody()->write($apiResponse->getBody()->getContents());
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (ClientException $e) {
            // Si le microservice renvoie une erreur (par exemple, 400, 401, 403)
            $errorMessage = 'Erreur client';
            if ($e->hasResponse()) {
                $errorMessage = (string) $e->getResponse()->getBody();
            }

            $response->getBody()->write(json_encode([
                'error' => 'Erreur client',
                'message' => $errorMessage
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } catch (ServerException $e) {
            // Si le microservice renvoie une erreur serveur (par exemple, 500)
            $errorMessage = 'Erreur serveur';
            if ($e->hasResponse()) {
                $errorMessage = (string) $e->getResponse()->getBody();
            }

            $response->getBody()->write(json_encode([
                'error' => 'Erreur serveur',
                'message' => $errorMessage
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        } catch (\Exception $e) {
            // Capture d'autres erreurs génériques
            $response->getBody()->write(json_encode(['error' => 'Erreur interne : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
