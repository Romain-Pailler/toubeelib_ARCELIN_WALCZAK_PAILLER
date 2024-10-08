<?php

namespace toubeelib\core\services\praticien;

use PhpParser\Node\Expr\PreDec;
use Respect\Validation\Exceptions\NestedValidationException;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\dto\InputPraticienDTO;
use toubeelib\core\dto\PraticienDTO;
use toubeelib\core\dto\SpecialiteDTO;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;

class ServicePraticien implements ServicePraticienInterface
{
    private PraticienRepositoryInterface $praticienRepository;

    public function __construct(PraticienRepositoryInterface $praticienRepository)
    {
        $this->praticienRepository = $praticienRepository;
    }

    public function createPraticien(InputPraticienDTO $praticien): PraticienDTO
    {
        try {
        // TODO : valider les données et créer l'entité
        $retour = new Praticien($praticien->nom,$praticien->prenom,$praticien->adresse,$praticien->tel); //new praticien


        $retour->setSpecialite($this->praticienRepository->getSpecialiteById($praticien->specialite)); //on doit set une specialite
        
        $this->praticienRepository->getPraticienById($this->praticienRepository->save($retour));
        return new PraticienDTO($retour);

    } catch(RepositoryEntityNotFoundException $e) {
        throw new ServicePraticienInvalidDataException('invalid Praticien ID');
    }

    }

    public function getPraticienById(string $id): PraticienDTO
    {
        try {
            $praticien = $this->praticienRepository->getPraticienById($id);
            return new PraticienDTO($praticien);
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServicePraticienInvalidDataException('invalid Praticien ID');
        }
    }


    public function getPraticiensBySpecialite(string $spe) : array{
            return $this->praticienRepository->getPraticiensBySpecialite($spe);
    }


    public function getPraticiensByCity(string $city) : array{
        return $this->praticienRepository->getPraticiensByCity($city);
}


    public function getSpecialiteById(string $id): SpecialiteDTO
    {
        try {
            $specialite = $this->praticienRepository->getSpecialiteById($id);
            return $specialite->toDTO();
        } catch(RepositoryEntityNotFoundException $e) {
            throw new ServicePraticienInvalidDataException('invalid Specialite ID');
        }
    }
}