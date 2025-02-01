<?php

namespace praticiens\application\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpForbiddenException;
use praticiens\core\services\authorization\ServiceAuthorization;

class AuthzMiddleware
{
    private ServiceAuthorization $serviceAuthorization;

    public function __construct(ServiceAuthorization $serviceAuthorization)
    {
        $this->serviceAuthorization = $serviceAuthorization;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Récupérer les informations d'authentification depuis les en-têtes
        $auth = [
            'id' => $request->getHeaderLine('X-User-Id'),
            'role' => $request->getHeaderLine('X-User-Role'),
        ];

        // Vérifier si les informations d'authentification existent
        if (empty($auth['role'])) {
            error_log('Aucune information d\'authentification trouvée dans la requête.');

            // Lancer une erreur 403 avec un message plus détaillé
            return $this->createErrorResponse($request, 'Accès refusé, utilisateur non authentifié.', 403);
        }

        // Utilisation de ServiceAuthorization pour vérifier si l'utilisateur est autorisé
        $role = $auth['role'];
        $resource = $request->getUri()->getPath();  // Par exemple, la ressource demandée (URL)

        // Appeler la méthode isGranted du service d'autorisation
        if (!$this->serviceAuthorization->isGranted($role, $resource)) {
            error_log('Utilisateur sans rôle autorisé : ' . $role . ' pour la ressource ' . $resource);

            // Lancer une erreur 403 avec un message plus détaillé
            return $this->createErrorResponse($request, 'Accès refusé, rôle insuffisant pour cette ressource.', 403);
        }

        // Si l'utilisateur est autorisé, poursuivre la requête
        error_log('Utilisateur autorisé avec le rôle : ' . $role . ' pour la ressource ' . $resource);

        return $handler->handle($request);
    }

    /**
     * Méthode pour créer une réponse d'erreur structurée.
     *
     * @param Request $request
     * @param string $message
     * @param int $statusCode
     * @return Response
     */
    private function createErrorResponse(Request $request, string $message, int $statusCode): Response
    {
        // Vérifier si la réponse est présente dans l'attribut de la requête
        $response = $request->getAttribute('response');

        // Si la réponse n'est pas présente, créer une nouvelle instance de Response
        if ($response === null) {
            $response = new \Slim\Psr7\Response(); // Crée une nouvelle réponse
        }


        // Écrire directement le tableau sans l'encoder une nouvelle fois
        $response->getBody()->write($message);

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
}
