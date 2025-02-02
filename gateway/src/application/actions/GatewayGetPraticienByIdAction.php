<?php

namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpInternalServerErrorException;

class GatewayGetPraticienByIdAction extends AbstractAction
{
    private ClientInterface $praticienClient;

    public function __construct(ClientInterface $client)
    {
        $this->praticienClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Récupérer l'ID du praticien depuis les arguments de la requête
        $id = $args['id'] ?? null;

        // Vérifier si l'ID est manquant ou vide
        if (empty($id)) {
            $response->getBody()->write(json_encode(['error' => "L'ID du praticien est requis."]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(400);  // Bad Request
        }

        // Extraire les paramètres de la requête
        $queryParams = $request->getQueryParams();

        // Récupérer les informations d'authentification depuis le middleware
        $auth = $request->getAttribute('auth'); // Assure-toi que le middleware ajoute ces données
        if (empty($auth)) {
            $response->getBody()->write(json_encode(['error' => 'Informations d\'authentification manquantes.']));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(401);  // Unauthorized
        }

        // Ajouter les informations d'authentification dans les en-têtes pour le microservice
        $headers = [
            'Authorization' => 'Bearer ' . $auth['role'],  // Utilisation du rôle (ou autre donnée) pour l'autorisation
            'X-User-Id' => $auth['id'],
            'X-User-Role' => $auth['role'],
        ];

        // Préparer l'URL pour la requête à l'API des praticiens
        $url = "praticiens/$id";

        // Paramètres de la requête (si nécessaires)
        $apiRequestOptions = [
            'headers' => $headers,  // Ajout des en-têtes
        ];

        if (!empty($queryParams)) {
            $apiRequestOptions['query'] = $queryParams;
        }

        try {
            // Appeler l'API du microservice pour récupérer les données du praticien
            $apiResponse = $this->praticienClient->get($url, $apiRequestOptions);

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
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);  // Bad Request

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
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);  // Internal Server Error

        } catch (\Exception $e) {
            // Capture d'autres erreurs génériques
            $response->getBody()->write(json_encode(['error' => 'Erreur interne : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);  // Internal Server Error
        }
    }
}
