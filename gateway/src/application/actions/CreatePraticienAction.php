<?php

namespace toubeelib\application\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubeelib\core\services\praticien\ServicePraticienInterface;
use toubeelib\core\services\praticien\ServicePraticienInvalidDataException;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib\core\dto\InputPraticienDTO;

class CreatePraticienAction extends AbstractAction
{
    private ServicePraticienInterface $praticienRepo;

    public function __construct(ServicePraticienInterface $praticienRepo)
    {
        $this->praticienRepo = $praticienRepo;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        try {
            $fieldsToUpdate = [];

            // Validation des champs
            if (isset($parsedBody['nom']) && is_string($parsedBody['nom'])) {
                $fieldsToUpdate['nom'] = $parsedBody['nom'];
            } else {
                throw new ServicePraticienInvalidDataException("Le nom est invalide ou manquant.");
            }

            if (isset($parsedBody['prenom']) && is_string($parsedBody['prenom'])) {
                $fieldsToUpdate['prenom'] = $parsedBody['prenom'];
            } else {
                throw new ServicePraticienInvalidDataException("Le prénom est invalide ou manquant.");
            }

            if (isset($parsedBody['adresse']) && is_string($parsedBody['adresse'])) {
                $fieldsToUpdate['adresse'] = $parsedBody['adresse'];
            } else {
                throw new ServicePraticienInvalidDataException("L'adresse est invalide ou manquante.");
            }

            if (isset($parsedBody['tel']) && preg_match('/^\d{10}$/', $parsedBody['tel'])) {
                $fieldsToUpdate['tel'] = $parsedBody['tel'];
            } else {
                throw new ServicePraticienInvalidDataException("Le numéro de téléphone est invalide.");
            }

            if (isset($parsedBody['specialite']) && is_string($parsedBody['specialite'])) {
                $fieldsToUpdate['specialite'] = $parsedBody['specialite'];
            } else {
                throw new ServicePraticienInvalidDataException("La spécialité est invalide ou manquante.");
            }

            // Vérifier si tous les champs requis sont fournis
            if (empty($fieldsToUpdate)) {
                throw new \InvalidArgumentException("Aucune donnée valide à créer.");
            }

            // Créer le DTO pour le praticien
            $praticienDto = new InputPraticienDTO(
                $fieldsToUpdate['nom'],
                $fieldsToUpdate['prenom'],
                $fieldsToUpdate['adresse'],
                $fieldsToUpdate['tel'],
                $fieldsToUpdate['specialite']
            );

            // Créer le praticien via le service
            $praticien = $this->praticienRepo->createPraticien($praticienDto);

            // Réponse de succès
            $res = [
                "Praticien" => [
                    "id" => $praticien->ID,
                    "nom_praticien" => $praticien->nom,
                    "prenom_praticien" => $praticien->prenom,
                    "adresse" => $praticien->adresse,
                    "telephone" => $praticien->tel,
                    "specialite" => $praticien->specialite_label,
                ],
                "links" => [
                    "self" => [
                        "href" => "/praticiens/" . $praticien->ID
                    ]
                ]
            ];

            $response->getBody()->write(json_encode($res));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        } catch (ServicePraticienInvalidDataException $e) {
            // Gestion des erreurs liées aux données invalides
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(401); // Unprocessable Entity pour les erreurs de validation

        } catch (RepositoryEntityNotFoundException $e) {
            // Gestion des erreurs liées à des entités non trouvées
            $response->getBody()->write(json_encode(['error' => 'Ressource non trouvée : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        } catch (\InvalidArgumentException $e) {
            // Gestion des erreurs d'argument
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(400); // Bad Request pour des données invalides

        } catch (\Exception $e) {
            // Gestion des autres erreurs non anticipées
            $response->getBody()->write(json_encode(['error' => 'Erreur interne du serveur : ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
