<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use auth\application\actions\SignInAction;
use auth\application\actions\ValidateAction;
use auth\application\middlewares\CorsMiddleware;
use auth\application\actions\RegisterAction;
use auth\application\actions\RefreshAction;


return function (\Slim\App $app): \Slim\App {

    // Route pour l'inscription (register)
    $app->post('/auth/register', RegisterAction::class);

    // Route pour la connexion (signin)
    $app->post('/auth/signin', SignInAction::class);

    $app->post('/auth/validate', ValidateAction::class)
        ->add(\auth\application\middlewares\AuthMiddleware::class);

    // Route pour rafraîchir le token JWT
    $app->post('/auth/refresh', RefreshAction::class);

    // Route pour gérer les erreurs (optionnel)
    $app->options(
        '/{routes:.+}',
        function (Request $request, Response $response, array $args): Response {
            return $response;
        }
    );
    $app->add(new CorsMiddleware());


    return $app;
};
