<?php

use gateway\application\actions\GatewayGetAllPraticiensAction;
use gateway\application\actions\GatewayGetPraticienByIdAction;
use GuzzleHttp\Client;
use gateway\middlewares\CorsMiddleware;
use Psr\Container\ContainerInterface;
use gateway\application\actions\CreatePraticienActionGateway;
use gateway\application\actions\CreateRendezVousActionGateway;
use gateway\application\actions\GetDisponibilitesPraticienActionGateway;
use gateway\application\actions\GetRendezVousActionGateway;
use gateway\application\actions\ModifRendezVousActionGateway;
use gateway\application\actions\DeleteRendezVousActionGateway;
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
    'rdvsClient' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => $c->get('api.rdvs'),
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
    CreatePraticienActionGateway::class => function(ContainerInterface $container){
        return new CreatePraticienActionGateway($container->get('praticienClient'));
    },

    // CHANGER TOUBEELIBCLIENT PAR RDVCLIENT
    CreateRendezVousActionGateway::class => function(ContainerInterface $container){
        return new CreateRendezVousActionGateway($container->get('rdvsClient'));
    },
    GetDisponibilitesPraticienActionGateway::class => function(ContainerInterface $container){
        return new GetDisponibilitesPraticienActionGateway($container->get('rdvsClient'));
    },
    GetRendezVousActionGateway::class => function(ContainerInterface $container){
        return new GetRendezVousActionGateway($container->get('rdvsClient'));
    },
    ModifRendezVousActionGateway::class => function(ContainerInterface $container){
        return new ModifRendezVousActionGateway($container->get('rdvsClient'));
    },
    DeleteRendezVousActionGateway::class => function(ContainerInterface $container){
        return new DeleteRendezVousActionGateway($container->get('rdvsClient'));
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
