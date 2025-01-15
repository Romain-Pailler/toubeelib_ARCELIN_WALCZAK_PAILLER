<?php

use Psr\Container\ContainerInterface;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use \toubeelib\core\services\rdv\ServiceRendezVousInterface;
use \toubeelib\core\services\praticien\ServicePraticienInterface;
use \toubeelib\application\actions\GetRendezVousAction;
use \toubeelib\application\actions\GetPraticiensAction;
use \toubeelib\application\actions\GetDisponibilitesPraticienAction;
use \toubeelib\application\actions\ModifRendezVousAction;
use \toubeelib\application\actions\CreateRendezVousAction;
use \toubeelib\application\actions\DeleteRendezVousAction;
use \toubeelib\application\actions\CreatePraticienAction;
use toubeelib\infrastructure\repositories\ArrayPraticienRepository;
use toubeelib\infrastructure\repositories\ArrayRdvRepository;

return [

    // Connexion principale à la base de données PostgreSQL
    'pdo.rdv' => function (): PDO {
        $host = 'toubeelib.db';
        $port = '5432';
        $dbname = 'rdv';
        $user = 'root';
        $password = 'root';

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        try {
            return new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur de connexion à la base de données principale : ' . $e->getMessage());
        }
    },

    // Deuxième connexion PDO
    'pdo.praticien' => function (): PDO {
        $host = 'toubeelib.db';
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

    // Troisième connexion PDO
    'pdo.patient' => function (): PDO {
        $host = 'toubeelib.db';
        $port = '5432';
        $dbname = 'patient';
        $user = 'root';
        $password = 'root';

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        try {
            return new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur de connexion à la base tertiaire : ' . $e->getMessage());
        }
    },


    // Répertoires
    PraticienRepositoryInterface::class => function (ContainerInterface $c) {
        return new ArrayPraticienRepository();
    },

    RdvRepositoryInterface::class => function (ContainerInterface $c) {
        return new ArrayRdvRepository();
    },

    // Services
    ServiceRendezVousInterface::class => function (ContainerInterface $c) {
        return new \toubeelib\core\services\rdv\ServiceRendezVous(
            $c->get(RdvRepositoryInterface::class),
            $c->get(PraticienRepositoryInterface::class)
        );
    },

    ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new \toubeelib\core\services\praticien\ServicePraticien(
            $c->get(PraticienRepositoryInterface::class)
        );
    },

    // Actions
    GetRendezVousAction::class => function (ContainerInterface $c) {
        return new GetRendezVousAction($c->get(ServiceRendezVousInterface::class));
    },
    GetPraticiensAction::class => function (ContainerInterface $c) {
        return new GetPraticiensAction($c->get(ServicePraticienInterface::class), $c->get(PDO::class));
    },
    GetDisponibilitesPraticienAction::class => function (ContainerInterface $c) {
        return new GetDisponibilitesPraticienAction($c->get(ServiceRendezVousInterface::class));
    },
    ModifRendezVousAction::class => function (ContainerInterface $c) {
        return new ModifRendezVousAction($c->get(ServiceRendezVousInterface::class));
    },
    CreateRendezVousAction::class => function (ContainerInterface $c) {
        return new CreateRendezVousAction($c->get(ServiceRendezVousInterface::class));
    },
    DeleteRendezVousAction::class => function (ContainerInterface $c) {
        return new DeleteRendezVousAction($c->get(ServiceRendezVousInterface::class));
    },
    CreatePraticienAction::class => function (ContainerInterface $c) {
        return new CreatePraticienAction($c->get(ServicePraticienInterface::class));
    },
];
