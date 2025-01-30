<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\core\services\praticien\ServicePraticienInvalidDataException;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class GetPraticiensAction extends AbstractAction
{
    private ServicePraticienInterface $praticienService;

    public function __construct(ServicePraticienInterface $praticienService)
    {
        $this->praticienService = $praticienService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Récupérer les paramètres de la requête (specialite ou ville)
        $queryParams = $request->getQueryParams();
        $specialite = $queryParams['specialite'] ?? null;
        $ville = $queryParams['ville'] ?? null;

        try {
            // Vérification des paramètres et appel du service
            if (!empty($specialite)) {
                $praticiens = $this->praticienService->getPraticiensBySpecialite($specialite);
            } elseif (!empty($ville)) {
                $praticiens = $this->praticienService->getPraticiensByCity($ville);
            } else {
                $praticiens = $this->praticienService->getPraticien();
            }

            // Transformation des données en tableau
            $res = array_map(function ($praticien) {
                return [
                    'id' => $praticien->ID,
                    'nom' => $praticien->nom,
                    'prenom' => $praticien->prenom,
                    'specialite' => $praticien->specialite->label,
                    'adresse' => $praticien->adresse,
                    'telephone' => $praticien->tel,
                ];
            }, $praticiens);

            // Réponse de succès avec les praticiens trouvés
            $response->getBody()->write(json_encode($res));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (ServicePraticienInvalidDataException $e) {
            // Gestion des erreurs liées aux données invalides
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        } catch (RepositoryEntityNotFoundException $e) {
            // Gestion des erreurs liées à des entités non trouvées
            $response->getBody()->write(json_encode(['error' => 'Ressource non trouvée : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        } catch (\Exception $e) {
            // Gestion des autres erreurs non anticipées
            $response->getBody()->write(json_encode(['error' => 'Erreur interne du serveur : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
