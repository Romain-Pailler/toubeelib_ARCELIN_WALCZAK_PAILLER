<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use gateway\application\actions\GenericAction;


return function (\Slim\App $app): \Slim\App {
    $app->add(\gateway\middlewares\CorsMiddleware::class);
    $app->get('/praticiens', GenericAction::class);
    $app->get('/praticiens/{id}/disponibilites', gateway\application\actions\GatewayGetPraticienByIdAction::class);
    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        return $response;
    });

    return $app;
};
