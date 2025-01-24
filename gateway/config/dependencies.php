<?php

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use toubeelib\application\actions\GenericAction;

return [

    'toubeelibClient' => function(ContainerInterface $c) {
        return new Client([
            'base_uri' => $c->get('api.toubeelib'),
            'timeout' => 2.0
        ]);
    },



    GenericAction::class => function(ContainerInterface $container){
        return new GenericAction($container->get('toubeelibClient'));
    }
];
