<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Inclure le fichier de dépendances qui retourne le conteneur
$container = require_once __DIR__ . '/../config/dependencies.php';

// Récupérer la Closure qui crée l'objet PDO et l'exécuter pour obtenir l'instance de PDO
$pdoUsersClosure = $container['pdo.users'];
$pdoUsers = $pdoUsersClosure();

// Créer le repository PDOPraticien en lui passant l'objet PDO
$pdoUsersRepository = new \auth\infrastructure\PDO\PDOAuth($pdoUsers);

// Créer le service
$service = new \auth\core\services\auth\ServiceAuth($pdoUsersRepository);

//print_r($service->checkCredentials('nino.arcelin@gmail.com', 'root'));


$pdto1 = new \auth\core\dto\InputAuthDTO('a.a@gmail.com', 'root', 'admin');
$pe1 = $service->createUser($pdto1);
