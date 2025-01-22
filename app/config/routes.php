<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use toubeelib\application\middlewares\AuthMiddleware;
use toubeelib\application\middlewares\AuthzPraticienMiddleware;

return function (\Slim\App $app): \Slim\App {

    // Routes publiques
    $app->get('/', \toubeelib\application\actions\HomeAction::class);

    $app->post('/auth/signin', \toubeelib\application\actions\SignInAction::class);

    // Routes protégées (nécessitent un token JWT valide)
    $app->group('/secure', function (\Slim\Routing\RouteCollectorProxy $group) {
        // Actions liées aux rendez-vous
        $group->get('/rdvs/{ID-RDV}', \toubeelib\application\actions\GetRendezVousAction::class);
        $group->patch('/rdvs/{ID-RDV}', \toubeelib\application\actions\ModifRendezVousAction::class);
        $group->post('/rdvs', \toubeelib\application\actions\CreateRendezVousAction::class);
        $group->delete('/rdvs/{ID-RDV}', \toubeelib\application\actions\DeleteRendezVousAction::class);

        // Actions liées aux praticiens avec autorisation spécifique
        $group->post('/praticiens', \toubeelib\application\actions\CreatePraticienAction::class);
        $group->get('/praticiens', \toubeelib\application\actions\GetPraticiensAction::class)
            ->add(AuthzPraticienMiddleware::class); // Middleware spécifique pour la route des praticiens
        $group->get('/praticiens/{ID-PRATICIEN}/disponibilites', \toubeelib\application\actions\GetDisponibilitesPraticienAction::class);
    })->add(AuthMiddleware::class); // Middleware global d'authentification pour tout le groupe /secure

    // Options route for CORS
    $app->options(
        '/{routes:.+}',
        function (
            Request $rq,
            Response $rs,
            array $args
        ): Response {
            return $rs;
        }
    )->add(new \toubeelib\application\middlewares\CorsMiddleware());

    return $app;
};
