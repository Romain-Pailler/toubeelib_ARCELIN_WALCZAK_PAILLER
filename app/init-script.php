<?php

use Faker\Factory;

print_r('Bonjour je suis le script php');

require_once 'vendor/autoload.php';

$host = 'toubeelib.db';  // Nom du service DB
$port = 5432;
$db = 'users';
$user = 'root';
$password = 'root';

while (!@pg_connect("host=$host port=$port dbname=$db user=$user password=$password")) {
    echo "Waiting for database...\n";
    sleep(2);  // Attente de 2 secondes avant de réessayer
}

echo "Database is ready!\n";


$faker = Factory::create('fr_FR');

// Inclure le fichier de dépendances qui retourne le conteneur
$container = require_once 'config/dependencies.php';

// Récupérer la Closure qui crée l'objet PDO et l'exécuter pour obtenir l'instance de PDO
$pdoUsersClosure = $container['pdo.users'];
$pdoUsers = $pdoUsersClosure();

// Créer le repository PDOPraticien en lui passant l'objet PDO
$pdoUsersRepository = new \toubeelib\infrastructure\PDO\PDOAuth($pdoUsers);

// Créer le service
$service = new \toubeelib\core\services\auth\ServiceAuth($pdoUsersRepository);

//print_r($service->checkCredentials('nino.arcelin@gmail.com', 'root'));


$pdto1 = new \toubeelib\core\dto\InputAuthDTO('nino.arcelin@gmail.com', 'root', 'admin');
$pe1 = $service->createUser($pdto1);

$pdto1 = new \toubeelib\core\dto\InputAuthDTO('romain.pallier@gmail.com', 'root', 'admin');
$pe1 = $service->createUser($pdto1);

$pdto1 = new \toubeelib\core\dto\InputAuthDTO('dimitri.wvm@gmail.com', 'root', 'admin');
$pe1 = $service->createUser($pdto1);

$pdto1 = new \toubeelib\core\dto\InputAuthDTO('admin.admin@gmail.com', 'admin', 'admin');
$pe1 = $service->createUser($pdto1);

$pdto1 = new \toubeelib\core\dto\InputAuthDTO('patient.patient@gmail.com', 'patient', 'patient');
$pe1 = $service->createUser($pdto1);

$pdto1 = new \toubeelib\core\dto\InputAuthDTO('praticien.praticien@gmail.com', 'praticien', 'praticien');
$pe1 = $service->createUser($pdto1);



// ========================
// Création des praticiens
// ========================
$pdoPraticienClosure = $container['pdo.praticien'];
$pdoPraticien = $pdoPraticienClosure();
$pdoPraticienRepository = new \toubeelib\infrastructure\PDO\PDOPraticien($pdoPraticien);
$servicePraticien = new \toubeelib\core\services\praticien\ServicePraticien($pdoPraticienRepository);

// Création des praticiens
$praticienSpecialites = [];  // Tableau clé-valeur pour ID et spécialité
for ($i = 0; $i < 10; $i++) {
    // Générer des informations sur le praticien, y compris la spécialité
    $specialite = $faker->randomElement(['A', 'B', 'C']);
    $pdto = new \toubeelib\core\dto\InputPraticienDTO(
        $faker->lastName,
        $faker->firstName,
        $faker->address,
        $faker->phoneNumber,
        $specialite
    );

    // Créer le praticien
    $praticien = $servicePraticien->createPraticien($pdto);

    // Stocker l'ID du praticien et sa spécialité dans le tableau associatif
    $praticienSpecialites[$praticien->ID] = $specialite;  // Utilisation de l'ID comme clé
}

// =====================
// Création des patients
// =====================
$pdoPatientClosure = $container['pdo.patient'];
$pdoPatient = $pdoPatientClosure();
$pdoPatientRepository = new \toubeelib\infrastructure\PDO\PDOPatient($pdoPatient);
$servicePatient = new \toubeelib\core\services\patient\ServicePatient($pdoPatientRepository);

$patientIds = [];
for ($i = 0; $i < 10; $i++) {
    $pdto = new \toubeelib\core\dto\InputPatientDTO(
        $faker->lastName,
        $faker->firstName,
        $faker->address,
        $faker->phoneNumber,
        $faker->date($format = 'Y-m-d', $max = '2010/01/01')
    );

    $patient = $servicePatient->createPatient($pdto);
    if ($patient) {
        $patientIds[] = $patient->ID;  // Assurez-vous que $patient->ID n'est pas null
    } else {
        echo "Erreur lors de la création du patient pour " . $pdto->prenom . "\n";
    }
    $patientIds[] = $patient->ID; // Stocker l'ID du patient
}

// =====================
// Création des RDV
// =====================
$pdoRDVClosure = $container['pdo.rdv'];
$pdoRDV = $pdoRDVClosure();
$pdoRendezVousRepository = new \toubeelib\infrastructure\PDO\PDORendezVous($pdoRDV);
$serviceRDV = new \toubeelib\core\services\rdv\ServiceRendezVous($pdoRendezVousRepository, $pdoPraticienRepository);

// Création des RDV avec plage de dates limitée à 2000 - 2025
for ($i = 0; $i < 10; $i++) {
    // Sélectionner des IDs valides pour praticien et patient
    $praticienId = $faker->randomElement(array_keys($praticienSpecialites));  // Utilisation des clés du tableau associatif
    $patientId = $faker->randomElement($patientIds);

    // Récupérer la spécialité du praticien à partir du tableau associatif
    $specialitePraticien = $praticienSpecialites[$praticienId];

    // La spécialité du RDV doit être la même que celle du praticien
    $specialiteRdv = $specialitePraticien;

    // Limitez la date générée à un intervalle acceptable
    $dateRdv = $faker->dateTimeBetween('2025-02-01', '2025-12-31');

    // Afficher la date générée pour vérifier
    echo "Date générée: " . $dateRdv->format('Y-m-d H:i') . "\n";

    // Vérifiez si la date est valide avant de procéder
    if ($dateRdv >= new DateTime('2025-02-01') && $dateRdv <= new DateTime('2025-12-31')) {
        // Ajouter les secondes manquantes pour que la date soit valide
        $dateRdvString = $dateRdv->format('Y-m-d H:i'); // Ajoute les secondes "00"

        var_dump($dateRdvString);

        // Créer le rendez-vous
        $rdvdto = new \toubeelib\core\dto\InputRendezVousDTO(
            $praticienId,
            $patientId,
            $specialiteRdv,
            '2024-10-02 17:30'
        );

        var_dump($rdvdto);

        // Créer le rendez-vous si la spécialité correspond
        try {
            $serviceRDV->creerRendezvous($rdvdto);
        } catch (Exception $e) {
            // Gérer l'exception si nécessaire
            echo "Erreur lors de la création du rendez-vous : " . $e->getMessage() . "\n";
        }
    } else {
        echo "La date générée n'est pas dans la plage valide.\n";
    }
}
