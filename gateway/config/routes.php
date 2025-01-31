<?php

declare(strict_types=1);

use gateway\application\actions\GatewayGetAllPraticiensAction;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app): \Slim\App {
    $app->add(\gateway\middlewares\CorsMiddleware::class);
    $app->get('/praticiens', GatewayGetAllPraticiensAction::class);
    $app->get('/praticiens/{id}', gateway\application\actions\GatewayGetPraticienByIdAction::class);
    $app->get('/praticiens/{id}/disponibilites', gateway\application\actions\GatewayGetPraticienByIdAction::class);
    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        return $response;
    });

    return $app;
};
