<?php

use Faker\Factory;

require_once 'vendor/autoload.php';

$host = 'users.db';
$port = 5432;
$db = 'users';
$user = 'root';
$password = 'root';

while (!@pg_connect("host=$host port=$port dbname=$db user=$user password=$password")) {
    echo "Waiting for database...\n";
    sleep(2);
}

echo "Database is ready!\n";


$faker = Factory::create('fr_FR');

// Inclure le fichier de dépendances qui retourne le conteneur
$container = require_once 'config/dependencies.php';

// Récupérer la Closure qui crée l'objet PDO et l'exécuter pour obtenir l'instance de PDO
$pdoUsersClosure = $container['pdo.users'];
$pdoUsers = $pdoUsersClosure();

// Créer le repository PDOPraticien en lui passant l'objet PDO
$pdoUsersRepository = new \auth\infrastructure\PDO\PDOAuth($pdoUsers);

// Créer le service
$service = new \auth\core\services\auth\ServiceAuth($pdoUsersRepository);

//print_r($service->checkCredentials('nino.arcelin@gmail.com', 'root'));


$pdto1 = new \auth\core\dto\InputAuthDTO('nino.arcelin@gmail.com', 'root', 'admin');
$pe1 = $service->createUser($pdto1);

$pdto1 = new \auth\core\dto\InputAuthDTO('romain.pallier@gmail.com', 'root', 'admin');
$pe1 = $service->createUser($pdto1);

$pdto1 = new \auth\core\dto\InputAuthDTO('dimitri.wvm@gmail.com', 'root', 'admin');
$pe1 = $service->createUser($pdto1);

$pdto1 = new \auth\core\dto\InputAuthDTO('admin.admin@gmail.com', 'admin', 'admin');
$pe1 = $service->createUser($pdto1);

$pdto1 = new \auth\core\dto\InputAuthDTO('patient.patient@gmail.com', 'patient', 'patient');
$pe1 = $service->createUser($pdto1);

$pdto1 = new \auth\core\dto\InputAuthDTO('praticien.praticien@gmail.com', 'praticien', 'praticien');
$pe1 = $service->createUser($pdto1);
