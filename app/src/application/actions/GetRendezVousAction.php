<?php
namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRendezVousInterface;
use toubeelib\core\services\rdv\ServiceRendezVousNotDataFoundException;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class GetRendezVousAction extends AbstractAction
{
    private ServiceRendezVousInterface $rdvRepo;

    public function __construct(ServiceRendezVousInterface $rdvRepo)
    {
        $this->rdvRepo = $rdvRepo;
    }
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $idRdv = $args['ID-RDV'];

        try {
            $rdv = $this->rdvRepo->getRendezvousById($idRdv);
            $res = ["Rendez-Vous" =>[
                "id" => $rdv->ID,
                "id_praticient" => $rdv->praticien,
                "id_patient" => $rdv->patient,
                "specialite_praticien" => $rdv->specialite_label,
                "horaire" => $rdv->date,
                "statut" => $rdv->statut,
            ],"links" => [
                "self"=> [
                    "href"=> "/rdvs/" . $rdv->ID
                ],
                "modifier"=>[
                    "href"=> "/rdvs/" . $rdv->ID
                ],
                "annuler"=>[
                    "href"=> "/rdvs/" . $rdv->ID
                ],
                "praticien"=>[
                    "href"=> "/praticiens/" . $rdv->praticien
                ],
                "patient"=>[
                    "href"=> "/patients/" . $rdv->patient
                ],
                ]];
            $response->getBody()->write(json_encode($res));
            return $response->withHeader('Content-Type','application/json')
            ->withStatus(200);
        } catch (ServiceRendezVousNotDataFoundException $e) {
            $response->getBody()->write(json_encode(['error' => 'Rendez-vous non trouvé']));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(404);

    }
}
}