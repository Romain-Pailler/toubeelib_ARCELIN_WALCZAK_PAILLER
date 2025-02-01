<?php

use gateway\application\actions\GatewayGetAllPraticiensAction;
use gateway\application\actions\GatewayGetPraticienByIdAction;
use GuzzleHttp\Client;
use gateway\middlewares\CorsMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use gateway\application\actions\GatewaySignInAction;
use gateway\application\actions\GatewayValidateAction;
use gateway\application\actions\GatewayRegisterAction;
use gateway\application\actions\GatewayRefreshAction;
use gateway\middlewares\AuthMiddleware;

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

    // Client spécifique pour l'API Auth
    'authClient' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => $c->get('api.auth'),
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
    },

    // Lier l'interface ClientInterface à Guzzle pour les appels au service d'authentification
    ClientInterface::class => function (ContainerInterface $container) {
        return $container->get('authClient');  // Utilise authClient pour les appels d'authentification
    },

    // Définir l'action GatewaySignInAction
    GatewaySignInAction::class => function (ContainerInterface $container) {
        return new GatewaySignInAction($container->get(ClientInterface::class));  // Injection du client HTTP
    },

    // Définir l'action GatewayValidateAction
    GatewayValidateAction::class => function (ContainerInterface $container) {
        return new GatewayValidateAction($container->get(ClientInterface::class));  // Injection du client HTTP
    },

    // Définir l'action GatewayRegisterAction
    GatewayRegisterAction::class => function (ContainerInterface $container) {
        return new GatewayRegisterAction($container->get(ClientInterface::class));  // Injection du client HTTP
    },

    // Définir l'action GatewayRefreshAction
    GatewayRefreshAction::class => function (ContainerInterface $container) {
        return new GatewayRefreshAction($container->get(ClientInterface::class));  // Injection du client HTTP
    },

    // AuthMiddleware avec la clé secrète
    AuthMiddleware::class => function (ContainerInterface $c) {
        return new AuthMiddleware('your-secret-key');
    },

];
