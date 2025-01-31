<?php

use gateway\application\actions\GatewayGetAllPraticiensAction;
use gateway\application\actions\GatewayGetPraticienByIdAction;
use GuzzleHttp\Client;
use gateway\middlewares\CorsMiddleware;
use Psr\Container\ContainerInterface;

return [

    'toubeelibClient' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => $c->get('api.toubeelib'),
            'timeout' => 2.0
        ]);
    },
    'praticienClient' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => $c->get('api.praticien'),
            'timeout' => 2.0
        ]);
    },

    CorsMiddleware::class => function () {
        return new CorsMiddleware();
    },
    GatewayGetAllPraticiensAction::class => function (ContainerInterface $container) {
        return new GatewayGetAllPraticiensAction($container->get('praticienClient'));
    },
    GatewayGetPraticienByIdAction::class => function (ContainerInterface $container) {
        return new GatewayGetPraticienByIdAction($container->get('praticienClient'));
    }
];
