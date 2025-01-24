<?php

use GuzzleHttp\Client;
use middlewares\CorsMiddleware;
use Psr\Container\ContainerInterface;
use gateway\application\actions\GenericAction;

return [

    'toubeelibClient' => function(ContainerInterface $c) {
        return new Client([
            'base_uri' => $c->get('api.toubeelib'),
            'timeout' => 2.0
        ]);
    },
    CorsMiddleware::class => function(){
        return new CorsMiddleware();
    },
    GenericAction::class => function(ContainerInterface $container){
        return new GenericAction($container->get('toubeelibClient'));
    }
];
