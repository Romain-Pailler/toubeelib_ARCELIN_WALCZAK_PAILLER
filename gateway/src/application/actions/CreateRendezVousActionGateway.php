<?php

namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpInternalServerErrorException;

class CreateRendezVousActionGateway extends AbstractAction
{
    private ClientInterface $toubeelibClient;

    public function __construct(ClientInterface $client) {
        $this->toubeelibClient = $client;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        // Récupérer les informations d'authentification depuis le middleware
        $auth = $request->getAttribute('auth');

        // Vérification du token
        if (empty($auth)) {
            $response->getBody()->write(json_encode(['error' => 'Token d\'authentification manquant ou invalide.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Ajouter les informations d'authentification dans les en-têtes
        $headers = [
            'Authorization' => 'Bearer ' . $auth['role'], // On passe le rôle ici
            'X-User-Id' => $auth['id'],
            'X-User-Role' => $auth['role'],
        ];

        try {
            // Récupérer et vérifier les données du rendez-vous
            $parsedBody = $request->getParsedBody();
            if (empty($parsedBody) || !is_array($parsedBody)) {
                $response->getBody()->write(json_encode(['error' => "Le corps de la requête est vide ou mal formaté."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            // Vérification des champs requis
            $requiredFields = ['praticien', 'patient', 'specialite', 'date'];
            foreach ($requiredFields as $field) {
                if (empty($parsedBody[$field])) {
                    $response->getBody()->write(json_encode(['error' => "Le champ '$field' est requis."]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

            // Envoi de la requête POST vers le backend avec authentification
            $apiResponse = $this->toubeelibClient->post("rdvs", [
                'headers' => $headers,
                'json' => $parsedBody
            ]);

            // Retourner la réponse du backend
            $response->getBody()->write($apiResponse->getBody()->getContents());
            return $response->withHeader('Content-Type', 'application/json')->withStatus($apiResponse->getStatusCode());
        } catch (ClientException $e) {
            // Gestion des erreurs 4xx du backend
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
            // Gestion des erreurs 5xx du backend
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
            // Gestion des erreurs internes
            $response->getBody()->write(json_encode(['error' => 'Erreur interne : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
