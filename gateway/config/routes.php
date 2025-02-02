<?php

declare(strict_types=1);

use gateway\application\actions\GatewayGetAllPraticiensAction;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use gateway\application\actions\CreatePraticienActionGateway;
use gateway\application\actions\CreateRendezVousActionGateway;
use gateway\application\actions\GatewayGetPraticienByIdAction;
use gateway\application\actions\GetDisponibilitesPraticienActionGateway;
use gateway\application\actions\GetRendezVousActionGateway;
use gateway\application\actions\ModifRendezVousActionGateway;
use gateway\application\actions\DeleteRendezVousActionGateway;
use gateway\application\actions\GatewaySignInAction;
use gateway\application\actions\GatewayValidateAction;
use gateway\middlewares\AuthMiddleware;
use gateway\middlewares\CorsMiddleware;
use gateway\application\actions\GatewayRefreshAction;
use gateway\application\actions\GatewayRegisterAction;

return function (\Slim\App $app): \Slim\App {

    $app->add(\gateway\middlewares\CorsMiddleware::class);
    $app->post('/praticiens', CreatePraticienActionGateway::class)
        ->add(AuthMiddleware::class);
    // $app->get('/praticiens/{id}/disponibilites', GetDisponibilitesPraticienActionGateway::class)
    //     ->add(AuthMiddleware::class);
//route a remettre pour les disponibilites
    $app->post('/rdvs', CreateRendezVousActionGateway::class)
        ->add(AuthMiddleware::class);
    $app->get('/rdvs/{id}', GetRendezVousActionGateway::class)
        ->add(AuthMiddleware::class);
    $app->patch('/rdvs/{id}', ModifRendezVousActionGateway::class)
        ->add(AuthMiddleware::class);
    $app->delete('/rdvs/{id}', DeleteRendezVousActionGateway::class)
        ->add(AuthMiddleware::class);

    // Ajouter les routes publiques (authentification)
    $app->post('/auth/signin', GatewaySignInAction::class);
    $app->post('/auth/validate', GatewayValidateAction::class);
    $app->post('/auth/register', GatewayRegisterAction::class);
    $app->post('/auth/refresh', GatewayRefreshAction::class);

    // Routes des praticiens nécessitant une authentification
    $app->get('/praticiens', GatewayGetAllPraticiensAction::class)
        ->add(AuthMiddleware::class);  // Ajout du middleware Auth ici pour sécuriser cette route
    $app->get('/praticiens/{id}', GatewayGetPraticienByIdAction::class)
        ->add(AuthMiddleware::class);  // Assurer que l'accès à ce praticien est aussi sécurisé
    // $app->get('/praticiens/{id}/disponibilites', GatewayGetPraticienByIdAction::class)
    //     ->add(AuthMiddleware::class);
// Route a enlever ci-dessus pour eviter duplication de route

        // PROBLEME CI DESSUS : DUPLICATION DE ROUTE POUR DISPONIBILITES praticiens/{id}/disponibilites

    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        return $response;
    })->add(new CorsMiddleware());

    return $app;
};;
