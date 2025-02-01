<?php

declare(strict_types=1);

use praticiens\application\middlewares\AuthzMiddleware;
use praticiens\core\services\authorization\ServiceAuthorization;

return function (\Slim\App $app): \Slim\App {

    // Définir un groupe de routes pour les praticiens
    $app->group('/praticiens', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->get('', \praticiens\application\actions\GetPraticiensAction::class); // Liste des praticiens
        $group->post('', \praticiens\application\actions\CreatePraticienAction::class); // Création d'un praticien
        $group->get('/{ID-PRATICIEN}', \praticiens\application\actions\GetPraticienIDAction::class); // Praticien par ID
    })
        ->add(new AuthzMiddleware($app->getContainer()->get(ServiceAuthorization::class)));

    // Route non protégée
    $app->get('/', \praticiens\application\actions\HomeAction::class);

    return $app;
};
