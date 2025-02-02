<?php

use Psr\Container\ContainerInterface;
use praticiens\core\repositoryInterfaces\PraticienRepositoryInterface;
use praticiens\core\services\praticien\ServicePraticienInterface;
use praticiens\application\actions\GetPraticiensAction;
use praticiens\application\actions\CreatePraticienAction;
use praticiens\infrastructure\PDO\PDOPraticien;
use praticiens\application\actions\GetPraticienIDAction;
use praticiens\core\services\authorization\ServiceAuthorization;
use praticiens\application\middlewares\AuthzMiddleware;

return [

    // Connexion PDO
    'pdo.praticien' => function (): PDO {
        $host = 'praticien.db';
        $port = '5432';
        $dbname = 'praticien';
        $user = 'root';
        $password = 'root';

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        try {
            return new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur de connexion à la base secondaire : ' . $e->getMessage());
        }
    },

    // Répertoires
    PraticienRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOPraticien($c->get('pdo.praticien'));
    },

    // Services
    ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new \praticiens\core\services\praticien\ServicePraticien(
            $c->get(PraticienRepositoryInterface::class)
        );
    },

    ServiceAuthorization::class => function (ContainerInterface $c) {
        return new ServiceAuthorization();
    },

    // Actions
    GetPraticiensAction::class => function (ContainerInterface $container) {
        return new GetPraticiensAction(
            $container->get(ServicePraticienInterface::class),
            $container->get('pdo.praticien')
        );
    },

    GetPraticienIDAction::class => function (ContainerInterface $container) {
        return new GetPraticienIDAction(
            $container->get(ServicePraticienInterface::class),
            $container->get('pdo.praticien')
        );
    },

    CreatePraticienAction::class => function (ContainerInterface $c) {
        return new CreatePraticienAction($c->get(ServicePraticienInterface::class));
    },

    // AuthzMiddleware prend un tableau de rôles autorisés
    AuthzMiddleware::class => function (ContainerInterface $container) {
        return new AuthzMiddleware($container->get(ServiceAuthorization::class), ['admin', 'user']);
    },

];
