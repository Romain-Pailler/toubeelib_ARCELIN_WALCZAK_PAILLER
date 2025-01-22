<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use toubeelib\application\actions\GenericAction;

return function (\Slim\App $app): \Slim\App {

    $app->map(['GET', 'POST', 'PATCH', 'DELETE', 'PUT'], '/{routes:.+}', GenericAction::class)->setName('genericRoute');
    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        return $response;
    });

    return $app;
};
