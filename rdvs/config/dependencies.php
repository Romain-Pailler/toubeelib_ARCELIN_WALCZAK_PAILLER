<?php

use Psr\Container\ContainerInterface;
use rdvs\core\repositoryInterfaces\RdvRepositoryInterface;
use \rdvs\core\services\rdv\ServiceRendezVousInterface;
use \rdvs\application\actions\GetRendezVousAction;
use \rdvs\application\actions\ModifRendezVousAction;
use \rdvs\application\actions\CreateRendezVousAction;
use \rdvs\application\actions\DeleteRendezVousAction;
use rdvs\infrastructure\PDO\PDORendezVous;
use rdvs\core\repositoryInterfaces\PraticienProviderInterface;
use rdvs\infrastructure\api\PraticienApiAdapter;

return [

    // Connexion principale à la base de données PostgreSQL
    'pdo.rdv' => function (): PDO {
        $host = 'rdvs.db';
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

    

    // Répertoires
    PraticienProviderInterface::class => function (): PraticienProviderInterface {
        return new PraticienApiAdapter();
    },
    

    RdvRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDORendezVous($c->get('pdo.rdv'));
    },

    // Services
    ServiceRendezVousInterface::class => function (ContainerInterface $c) {
        return new \rdvs\core\services\rdv\ServiceRendezVous(
            $c->get(RdvRepositoryInterface::class),
            $c->get(PraticienProviderInterface::class)
        );
    },

    // Actions Praticien
    GetRendezVousAction::class => function (ContainerInterface $c) {
        return new GetRendezVousAction(
            $c->get(ServiceRendezVousInterface::class),
            $c->get('pdo.rdv')
        );
    },

    // Actions Rdvs
    ModifRendezVousAction::class => function (ContainerInterface $c) {
        return new ModifRendezVousAction($c->get(ServiceRendezVousInterface::class));
    },
    CreateRendezVousAction::class => function (ContainerInterface $c) {
        return new CreateRendezVousAction($c->get(ServiceRendezVousInterface::class));
    },
    DeleteRendezVousAction::class => function (ContainerInterface $c) {
        return new DeleteRendezVousAction($c->get(ServiceRendezVousInterface::class));
    },

];
