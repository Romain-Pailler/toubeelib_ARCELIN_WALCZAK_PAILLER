<?php
namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRendezVousInterface;
use toubeelib\core\services\rdv\ServiceRendezVousNotDataFoundException;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use InvalidArgumentException;
use DomainException;

class DeleteRendezVousAction extends AbstractAction
{
    private ServiceRendezVousInterface $rdvRepo;

    public function __construct(ServiceRendezVousInterface $rdvRepo)
    {
        $this->rdvRepo = $rdvRepo;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $idRdv = $args['ID-RDV'];
        $parsedBody = $request->getParsedBody();

        try {
            // Vérifier si 'annulerPar' est fourni dans la requête et est une chaîne valide
            if (!isset($parsedBody['annulerPar']) || !is_string($parsedBody['annulerPar'])) {
                throw new InvalidArgumentException('Le champ annulerPar est requis et doit être une chaîne de caractères valide');
            }

            $this->rdvRepo->annulerRendezvous($idRdv, $parsedBody['annulerPar']);

            // Récupérer les informations du rendez-vous après l'annulation
            $rdv = $this->rdvRepo->getRendezvousById($idRdv);

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
                        "href" => "/rdvs/" . $rdv->ID
                    ],
                    "annuler" => [
                        "href" => "/rdvs/" . $rdv->ID
                    ],
                    "praticien" => [
                        "href" => "/praticiens/" . $rdv->praticien
                    ],
                    "patient" => [
                        "href" => "/patients/" . $rdv->patient
                    ],
                ]
            ];

            $response->getBody()->write(json_encode($res));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);

        } catch (ServiceRendezVousNotDataFoundException $e) {
            // Gestion de l'exception pour un rendez-vous inexistant
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus($e->getCode());

        } catch (\InvalidArgumentException $e) {
            // Gestion des erreurs d'arguments invalides
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(400);

        } catch (DomainException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(400);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur interne du serveur']));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(500);
        }
    }
}
