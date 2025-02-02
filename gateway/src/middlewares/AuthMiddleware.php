<?php

namespace gateway\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    private string $jwtSecret;

    public function __construct(string $jwtSecret)
    {
        $this->jwtSecret = $jwtSecret;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Extraire l'en-tête "Authorization"
        $authHeader = $request->getHeaderLine('Authorization');

        // Vérifier si l'en-tête "Authorization" existe et commence par "Bearer "
        if (empty($authHeader) || strpos($authHeader, 'Bearer ') !== 0) {
            // Créer une réponse avec un message d'erreur et un statut 401
            $response = new \Slim\Psr7\Response();
            $body = json_encode(['error' => 'Token absent ou mal formé']);
            $response->getBody()->write($body);
            return $response->withStatus(401)
                ->withHeader('Content-Type', 'application/json');
        }

        // Extraire le token JWT de l'en-tête
        $token = substr($authHeader, 7); // Retirer "Bearer " du début du token

        try {
            // Décoder le token
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));

            // Ajouter les informations d'authentification dans la requête
            $request = $request->withAttribute('auth', [
                'id' => $decoded->id ?? null,
                'email' => $decoded->email ?? null,
                'role' => $decoded->role ?? null,
            ]);
        } catch (\Exception $e) {
            // Créer une réponse avec un message d'erreur et un statut 401
            $response = new \Slim\Psr7\Response();
            $body = json_encode(['error' => 'Token invalide : ' . $e->getMessage()]);
            $response->getBody()->write($body);
            return $response->withStatus(401)
                ->withHeader('Content-Type', 'application/json');
        }

        // Passer la requête à l'action suivante
        return $handler->handle($request);
    }
}
