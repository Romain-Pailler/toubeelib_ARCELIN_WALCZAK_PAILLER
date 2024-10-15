<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function( \Slim\App $app):\Slim\App {

    $app->get('/', \toubeelib\application\actions\HomeAction::class);

    $app->get('/rdvs/{ID-RDV}',\toubeelib\application\actions\GetRendezVousAction::class);

    $app->patch('/rdvs/{ID-RDV}',\toubeelib\application\actions\ModifRendezVousAction::class);

    $app->post('/rdvs',\toubeelib\application\actions\CreateRendezVousAction::class);

    $app->delete('/rdvs/{ID-RDV}',\toubeelib\application\actions\DeleteRendezVousAction::class);

    $app->post('/praticiens',\toubeelib\application\actions\CreatePraticienAction::class);


    $app->options('/{routes:.+}',
        function( Request $rq,
                  Response $rs, array $args) : Response {
            return $rs;
        })->add(new \toubeelib\application\middlewares\CorsMiddleware());
    return $app;
};