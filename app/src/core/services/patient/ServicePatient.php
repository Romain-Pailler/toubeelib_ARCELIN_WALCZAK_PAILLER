<?php

namespace toubeelib\core\services\patient;

use toubeelib\core\domain\entities\patient\Patient;
use toubeelib\core\dto\InputPatientDTO;
use toubeelib\core\dto\PatientDTO;
use toubeelib\core\services\patient\ServicePatientInterface;
use toubeelib\core\repositoryInterfaces\RepositoryEntityNotFoundException;
use toubeelib\core\repositoryInterfaces\PatientRepositoryInterface;


class ServicePatient implements ServicePatientInterface
{

    private PatientRepositoryInterface $patientRepository;

    public function __construct(PatientRepositoryInterface $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    public function createPatient(InputPatientDTO $p): PatientDTO
    {
        $patient = new Patient($p->nom, $p->prenom, $p->adresse, $p->dateNaissance, $p->tel);
        $this->patientRepository->getPatientById(($this->patientRepository->save($patient)));
        return new PatientDTO($patient);
    }

    public function getPatientById(string $id): PatientDTO
    {
        try {
            $patient = $this->patientRepository->getPatientById($id);
            return new PatientDTO($patient);
        } catch (RepositoryEntityNotFoundException $e) {
            throw new ServicePatientInvalidDataException('invalid Praticien ID');
        }
    }

    /*
    public function updatePatient(string $id, InputPatientDTO $p): PatientDTO
    {

        $patient = $this->getPatientById($id);
        $patient->setNom($p->nom);
        $patient->setPrenom($p->prenom);
        $patient->setAdresse($p->adresse);
        $patient->setDateNaissance($p->dateNaissance);
        $patient->setTel($p->tel);
        
        return new PatientDTO($patient);
    }
*/

    public function deletePatient(string $id): bool
    {
        return $this->patientRepository->deletePatient($id);
    }
}
