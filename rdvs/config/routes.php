<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \rdvs\application\actions\HomeAction::class);

    $app->get('/rdvs/{ID-RDV}', \rdvs\application\actions\GetRendezVousAction::class);

    $app->patch('/rdvs/{ID-RDV}', \rdvs\application\actions\ModifRendezVousAction::class);

    $app->post('/rdvs', \rdvs\application\actions\CreateRendezVousAction::class);

    $app->delete('/rdvs/{ID-RDV}', \rdvs\application\actions\DeleteRendezVousAction::class);

    $app->options(
        '/{routes:.+}',
        function (
            Request $rq,
            Response $rs,
            array $args
        ): Response {
            return $rs;
        }
    )->add(new \rdvs\application\middlewares\CorsMiddleware());
    return $app;
};
