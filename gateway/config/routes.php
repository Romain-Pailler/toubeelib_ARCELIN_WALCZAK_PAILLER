<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (\Slim\App $app): \Slim\App {

    $app->get('/praticiens', \toubeelib\application\actions\GetPraticiensAction::class);
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
