<?php

use auth\application\actions\RefreshAction;
use auth\application\actions\RegisterAction;
use Psr\Container\ContainerInterface;
use auth\core\repositoryInterfaces\AuthRepositoryInterface;
use auth\infrastructure\PDO\PDOAuth;
use auth\core\services\auth\ServiceAuth;
use auth\core\services\auth\AuthProvider;
use auth\application\middlewares\AuthMiddleware;
use auth\application\actions\SignInAction;
use auth\application\actions\ValidateAction;

return [
    'settings' => [
        'displayErrorDetails' => true,
        'logErrors' => true,
        'logErrorDetails' => true,
        'jwt_secret' => 'your-secret-key',
    ],

    // Connexion à la base de données des utilisateurs
    'pdo.users' => function (): PDO {
        $host = 'users.db';
        $port = '5432';
        $dbname = 'users';
        $user = 'root';
        $password = 'root';

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        try {
            return new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur de connexion à la base de données des utilisateurs : ' . $e->getMessage());
        }
    },

    // Repository pour l'authentification
    AuthRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOAuth($c->get('pdo.users'));
    },

    // Service d'authentification
    ServiceAuth::class => function (ContainerInterface $c) {
        return new ServiceAuth($c->get(AuthRepositoryInterface::class));
    },

    // AuthProvider pour la gestion des JWT
    AuthProvider::class => function (ContainerInterface $c) {
        return new AuthProvider(
            $c->get(ServiceAuth::class),
            'your-secret-key'
        );
    },

    // Middleware pour l'authentification
    AuthMiddleware::class => function (ContainerInterface $container) {
        return new AuthMiddleware('your-secret-key');
    },

    // Action pour le SignIn
    SignInAction::class => function (ContainerInterface $c) {
        return new SignInAction($c->get(AuthProvider::class));
    },

    ValidateAction::class => function (ContainerInterface $c) {
        return new ValidateAction($c->get(AuthProvider::class));
    },

    RegisterAction::class => function (ContainerInterface $c) {
        return new RegisterAction($c->get(ServiceAuth::class));
    },



    RefreshAction::class => function (ContainerInterface $c) {
        return new RefreshAction($c->get(AuthProvider::class));
    },
];
