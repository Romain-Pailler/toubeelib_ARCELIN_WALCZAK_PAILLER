<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app): \Slim\App {

    $app->get('/', \praticiens\application\actions\HomeAction::class);

    $app->post('/praticiens', \praticiens\application\actions\CreatePraticienAction::class);

    $app->get('/praticiens', \praticiens\application\actions\GetPraticiensAction::class);
    $app->get('/praticiens/{ID-PRATICIEN}', \praticiens\application\actions\GetPraticienIDAction::class);

    $app->options(
        '/{routes:.+}',
        function (
            Request $rq,
            Response $rs,
            array $args
        ): Response {
            return $rs;
        }
    );
    return $app;
};
