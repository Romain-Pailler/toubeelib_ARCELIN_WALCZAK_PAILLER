<?php

use Psr\Container\ContainerInterface;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use \toubeelib\core\services\rdv\ServiceRendezVousInterface;
use \toubeelib\application\actions\GetRendezVousAction;
use \toubeelib\application\actions\ModifRendezVousAction;
use toubeelib\infrastructure\repositories\ArrayPraticienRepository;
use toubeelib\infrastructure\repositories\ArrayRdvRepository;

return [


    PraticienRepositoryInterface::class => function (ContainerInterface $c) {
        return new ArrayPraticienRepository();
    },

    RdvRepositoryInterface::class => function (ContainerInterface $c) {
        return new ArrayRdvRepository();
    },

    ServiceRendezVousInterface::class => function (ContainerInterface $c)
    {
        return new \toubeelib\core\services\rdv\ServiceRendezVous(
            $c->get(RdvRepositoryInterface::class),
            $c->get(PraticienRepositoryInterface::class));
    },

    GetRendezVousAction::class => function(ContainerInterface $c){
        return new GetRendezVousAction($c->get(ServiceRendezVousInterface::class));
    },
    ModifRendezVousAction::class => function(ContainerInterface $c){
        return new ModifRendezVousAction($c->get(ServiceRendezVousInterface::class));
    },

];