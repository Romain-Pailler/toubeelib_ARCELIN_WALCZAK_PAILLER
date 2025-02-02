<?php

namespace rdvs\application\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as SlimResponse;
use rdvs\core\services\auth\ServiceAuth;

class AuthzPraticienMiddleware
{
    private ServiceAuth $authzService;

    public function __construct(ServiceAuth $authzService)
    {
        $this->authzService = $authzService;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Récupérer le DTO d'authentification
        $auth = $request->getAttribute('auth');

        if (!$auth) {
            return $this->responseError('Utilisateur non authentifié', 401);
        }

        // Vérifier les droits d'accès via le service Authz
        $role = $auth['role'] ?? null;

        if (!$this->authzService->canAccessPraticien($role)) {
            return $this->responseError('Accès refusé : vous ne pouvez pas accéder à ce praticien', 403);
        }

        // Passer à l'action suivante
        return $handler->handle($request);
    }

    private function responseError(string $message, int $statusCode): Response
    {
        $response = new SlimResponse();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withStatus($statusCode)->withHeader('Content-Type', 'application/json');
    }
}
