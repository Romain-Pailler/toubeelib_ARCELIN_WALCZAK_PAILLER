<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\rdv\ServiceRendezVousInterface;
use toubeelib\core\services\rdv\ServiceRendezVousIncorrectDataException;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class GetDisponibilitesPraticienAction extends AbstractAction
{
    private ServiceRendezVousInterface $rdvService;

    public function __construct(ServiceRendezVousInterface $rdvService)
    {
        $this->rdvService = $rdvService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Récupération des query params
        $queryParams = $request->getQueryParams();
        $id_prat = $args['ID-PRATICIEN'] ?? null;
        $date_debut = $queryParams['date_debut'] ?? null;
        $date_fin = $queryParams['date_fin'] ?? null;

        try {
            // Validation des paramètres requis
            if (empty($id_prat)) {
                throw new ServiceRendezVousIncorrectDataException("L'ID du praticien est requis.");
            }

            if (empty($date_debut) || empty($date_fin)) {
                throw new ServiceRendezVousIncorrectDataException("Les dates de début et de fin sont requises.");
            }

            // Vérification du format des dates (YYYY-MM-DD)
            $dateDebutValid = \DateTimeImmutable::createFromFormat('Y-m-d', $date_debut) !== false;
            $dateFinValid = \DateTimeImmutable::createFromFormat('Y-m-d', $date_fin) !== false;

            if (!$dateDebutValid || !$dateFinValid) {
                throw new ServiceRendezVousIncorrectDataException("Les dates doivent être au format YYYY-MM-DD.");
            }
            // Vérification que la date de fin est postérieure ou égale à la date de début
            if ($date_fin < $date_debut) {
                throw new ServiceRendezVousIncorrectDataException("La date de fin doit être après la date de début.");
            }

            // Appel du service pour récupérer les disponibilités
            $dispos = $this->rdvService->listeDisposPraticienIndividuel($id_prat, $date_debut, $date_fin);

            // Transformation des disponibilités en format lisible (par exemple, 'Y-m-d H:i')
            $res = array_map(function ($dispo) {
                return $dispo->format('Y-m-d H:i');
            }, $dispos);

            $response->getBody()->write(json_encode($res));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);

        } catch (ServiceRendezVousIncorrectDataException $e) {
            // Gestion des erreurs de validation
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(400);

        } catch (RepositoryEntityNotFoundException $e) {
            // Si le praticien n'est pas trouvé
            $response->getBody()->write(json_encode(['error' => 'Praticien non trouvé']));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(404);

        } catch (\Exception $e) {
            // Gestion des erreurs génériques
            $response->getBody()->write(json_encode(['error' => 'Erreur interne du serveur : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(500);
        }
    }
}
