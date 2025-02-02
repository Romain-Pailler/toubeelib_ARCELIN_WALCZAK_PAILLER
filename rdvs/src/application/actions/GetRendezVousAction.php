<?php

namespace rdvs\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rdvs\core\services\rdv\ServiceRendezVousInterface;
use rdvs\core\services\rdv\ServiceRendezVousNotDataFoundException;
use rdvs\core\repositoryInterfaces\RepositoryEntityNotFoundException;

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
            $res = ["Rendez-Vous" => [
                "id" => $rdv->ID,
                "id_praticien" => $rdv->praticien,
                "id_patient" => $rdv->patient,
                "specialite_praticien" => $rdv->specialite_label,
                "horaire" => $rdv->date,
                "statut" => $rdv->statut,
            ], "links" => [
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
            ]];
            $response->getBody()->write(json_encode($res));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (ServiceRendezVousNotDataFoundException $e) {
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();

            // Construire la réponse avec le message et le code d'erreur
            $response->getBody()->write(json_encode(['error' => $errorMessage]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus($errorCode);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Problème serveur',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
