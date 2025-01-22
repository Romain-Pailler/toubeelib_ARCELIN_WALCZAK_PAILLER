<?php

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use toubeelib\application\actions\GenericAction;
return [

    'toubeelibClient' => function() {
        return new Client([
            'base_uri' => 'http://localhost:80',
            'timeout' => 1000.0
        ]);
    },

    GenericAction::class => function(ContainerInterface $container){
        $toubeelibClient = $container->get('toubeelibClient');
        return new GenericAction($toubeelibClient);
    }
];
