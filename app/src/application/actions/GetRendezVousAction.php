<?php
namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;

class GetRendezVousAction extends AbstractAction
{
    private RdvRepositoryInterface $rdvRepo;
    public function __construct(RdvRepositoryInterface $rdvRepo)
    {
        $this->$rdvRepo = $rdvRepo;
    }
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $idRdv = $args['id'];

        try {
            $rdv = $this->rdvRepo->getRendezvousById($idRdv);
            $res = ["Rendez-Vous" =>[
                "id" => ID,
                "id_praticient" => praticien,
                "id_patient" => patient,
                "specialite_praticien" => specialite_label,
                "horaire" => date,
                "statut" => statut,
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
            return response->withHeader('Content-Type','application/json');
        } catch (\Throwable $th) {
            //throw $th;
        }

    }
}
