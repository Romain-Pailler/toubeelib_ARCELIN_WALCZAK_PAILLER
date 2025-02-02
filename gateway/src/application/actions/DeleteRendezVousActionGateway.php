<?php

namespace gateway\application\actions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpInternalServerErrorException;

class DeleteRendezVousActionGateway extends AbstractAction
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

        // Vérification de l'ID du rendez-vous
        $id = $args['id'] ?? null;

        if (empty($id)) {
            $response->getBody()->write(json_encode(['error' => "L'ID du rendez-vous est requis."]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            // Vérification du champ 'annulerPar'
            $parsedBody = $request->getParsedBody();
            if (!isset($parsedBody['annulerPar']) || !is_string($parsedBody['annulerPar'])) {
                $response->getBody()->write(json_encode(['error' => "Le champ 'annulerPar' est requis et doit être une chaîne valide."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            // Envoi de la requête DELETE avec authentification
            $apiResponse = $this->toubeelibClient->delete("rdvs/$id", [
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
            // Gestion des erreurs génériques
            $response->getBody()->write(json_encode(['error' => 'Erreur interne : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
