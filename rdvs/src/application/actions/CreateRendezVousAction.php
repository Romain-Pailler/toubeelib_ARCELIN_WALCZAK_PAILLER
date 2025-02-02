<?php

namespace rdvs\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rdvs\core\services\rdv\ServiceRendezVousInterface;
use rdvs\core\services\rdv\ServiceRendezVousNotDataFoundException;
use rdvs\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class CreateRendezVousAction extends AbstractAction
{
    private ServiceRendezVousInterface $rdvRepo;

    public function __construct(ServiceRendezVousInterface $rdvRepo)
    {
        $this->rdvRepo = $rdvRepo;
    }
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        try {
            $fieldsToUpdate = [];
            if (isset($parsedBody['specialite']) && is_string($parsedBody['specialite'])) {
                $fieldsToUpdate['specialite'] = $parsedBody['specialite'];
            }

            if (isset($parsedBody['patient']) && is_string($parsedBody['patient'])) {
                $fieldsToUpdate['patient'] = $parsedBody['patient'];
            }
            if (isset($parsedBody['praticien']) && is_string($parsedBody['praticien'])) {
                $fieldsToUpdate['praticien'] = $parsedBody['praticien'];
            }
            if (isset($parsedBody['date']) && strtotime($parsedBody['date'])) {
                $fieldsToUpdate['date'] = $parsedBody['date'];
            }
            if (empty($fieldsToUpdate)) {
                throw new \InvalidArgumentException("Aucune donnée valide à mettre à jour.");
            }
            $rdvdto = new \rdvs\core\dto\InputRendezVousDTO($fieldsToUpdate['praticien'], $fieldsToUpdate['patient'], $fieldsToUpdate['specialite'], $fieldsToUpdate['date']);

            $rdv = $this->rdvRepo->creerRendezvous($rdvdto);
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
                ->withStatus(201);
        } catch (ServiceRendezVousNotDataFoundException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(402);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur interne du serveur']));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }
}
