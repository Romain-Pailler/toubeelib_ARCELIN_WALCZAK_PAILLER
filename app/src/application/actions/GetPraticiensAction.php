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
                throw new ServicePraticienInvalidDataException('Vous devez spécifier une ville ou une spécialité.');
            }

            // Transformation des données en tableau
            $res = array_map(function ($praticien) {
                return [
                    'id' => $praticien->getId(),
                    'nom' => $praticien->getNom(),
                    'prenom' => $praticien->getPrenom(),
                    'specialite' => $praticien->getSpecialite() ? [
                        'id' => $praticien->getSpecialite()->getId(),
                        'label' => $praticien->getSpecialite()->label,
                        'description' => $praticien->getSpecialite()->description,
                    ] : null,
                    'adresse' => $praticien->getAdresse(),
                    'telephone' => $praticien->getTel(),
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
