<?php

namespace praticiens\application\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use praticiens\core\services\praticien\ServicePraticienInterface;
use praticiens\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class GetPraticienIDAction extends AbstractAction
{
    private ServicePraticienInterface $praticienService;

    public function __construct(ServicePraticienInterface $praticienService)
    {
        $this->praticienService = $praticienService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $idPraticien = $args['ID-PRATICIEN'];

        try {
            // Validation de l'ID du praticien
            if (empty($idPraticien)) {
                throw new Exception("L'ID du praticien est requis.");
            }

            // Appel du service pour récupérer le praticien
            $praticien = $this->praticienService->getPraticienById($idPraticien);

            // Transformation du praticien en tableau ou en DTO (si nécessaire)
            $result = [
                'id' => $praticien->ID,
                'nom' => $praticien->nom,
                'prenom' => $praticien->prenom,
                'specialite' => $praticien->specialite_label,
                'adresse' => $praticien->adresse,
                'telephone' => $praticien->tel,
            ];

            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (Exception $e) {
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
