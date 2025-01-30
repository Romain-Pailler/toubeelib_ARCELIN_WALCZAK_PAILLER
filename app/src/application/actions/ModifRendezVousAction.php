<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRendezVousInterface;
use toubeelib\core\services\rdv\ServiceRendezVousNotDataFoundException;
use InvalidArgumentException;

class ModifRendezVousAction extends AbstractAction
{
    private ServiceRendezVousInterface $rdvService;

    // Constructeur avec injection de dépendance du service des rendez-vous
    public function __construct(ServiceRendezVousInterface $rdvService)
    {
        $this->rdvService = $rdvService;
    }

    // Méthode invoquée lors de la requête PATCH sur un rendez-vous
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Récupération de l'ID du rendez-vous depuis les arguments de la route
        $idRdv = $args['ID-RDV'];

        try {
            $body = $request->getParsedBody();
            $specialite = $body['specialite'] ?? null;
            $patient = $body['patient'] ?? null;


            if (isset($patient)) {
                $this->rdvService->changePatient($idRdv, $patient);
            }

            if (isset($specialite)) {
                $this->rdvService->changeSpecialite($idRdv, $specialite);
            }


            // Récupérer les informations mises à jour du rendez-vous après modifications
            $rdv = $this->rdvService->getRendezvousById($idRdv);

            // Construire la réponse en cas de succès, en ajoutant le lien HATEOAS pour la modification
            $res = [
                "Rendez-Vous" => [
                    "id" => $rdv->ID,
                    "id_praticien" => $rdv->praticien,
                    "id_patient" => $rdv->patient,
                    "specialite_praticien" => $rdv->specialite_label,
                    "horaire" => $rdv->date,
                    "statut" => $rdv->statut,
                ],
                "links" => [
                    "self" => [
                        "href" => "/rdvs/" . $rdv->ID
                    ],
                    "modifier" => [
                        "href" => "/rdvs/" . $rdv->ID,
                        "method" => "PATCH"
                    ]
                ]
            ];

            // Ecrire la réponse JSON et retourner un code 200
            $response->getBody()->write(json_encode($res));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (ServiceRendezVousNotDataFoundException $e) {
            // Si le rendez-vous n'existe pas, retourner une erreur 404
            $response->getBody()->write(json_encode(['error' => 'Rendez-vous non trouvé']));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        } catch (InvalidArgumentException $e) {
            // Gestion des erreurs liées à des arguments invalides
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(400); // Bad Request
        } catch (\Exception $e) {
            // Gestion des autres erreurs non anticipées
            $response->getBody()->write(json_encode(['error' => 'Erreur interne du serveur : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
