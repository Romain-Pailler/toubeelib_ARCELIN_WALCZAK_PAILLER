<?php

namespace auth\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ValidateAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Vérifier si les informations d'authentification sont présentes dans la requête
        $auth = $request->getAttribute('auth');

        if (empty($auth)) {
            return $this->jsonError($response, 'Token invalide ou expiré.', 401);
        }

        // Si le token est valide, renvoyer un message de succès
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'message' => 'Token valide.',
            'auth' => $auth // Retourner les informations de l'utilisateur si nécessaire
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    private function jsonError(ResponseInterface $response, string $message, int $status)
    {
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
