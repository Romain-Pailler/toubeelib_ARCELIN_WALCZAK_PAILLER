<?php

require_once __DIR__ . '/../vendor/autoload.php';

$service = new toubeelib\core\services\rdv\ServiceRendezVous(new \toubeelib\infrastructure\repositories\ArrayRdvRepository(),
                                                             new \toubeelib\infrastructure\repositories\ArrayPraticienRepository());

$rdvdto = new \toubeelib\core\dto\InputRendezVousDTO('p1', 'pa1', 'A', '2024-09-02T09:00');

print_r('Test 1 - Creation Rendez Vous');

try {
    $rdv1 = $service->creerRendezvous($rdvdto);
} catch (\toubeelib\core\services\rdv\ServiceRendezVousIncorrectDataException $e){
    echo 'exception dans la récupération d\'un praticien :' . PHP_EOL;
    echo $e->getMessage(). PHP_EOL;
}


print_r($rdv1);


print_r('Test 2 - Annulation Rendez Vous');

try {


    print_r('Avant annulation');
    
    print_r($service->getRendezvousById('r1'));

    print_r('Après annulation');

    $service->annulerRendezvous('r1');

    print_r($service->getRendezvousById('r1'));


} catch (\toubeelib\core\services\rdv\ServiceRendezVousIncorrectDataException $e){
    echo 'exception dans la récupération d\'un praticien :' . PHP_EOL;
    echo $e->getMessage(). PHP_EOL;
}
