<?php

namespace toubeelib\infrastructure\PDO;

use Ramsey\Uuid\Uuid;
use PDO;
use toubeelib\core\repositoryInterfaces\RdvRepositoryInterface;
use toubeelib\core\domain\entities\rdv\RendezVous;

class PDORendezVous implements RdvRepositoryInterface
{
    private PDO $pdo;

    // Le constructeur prend la connexion PDO comme paramètre
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getRendezvousById(string $id): RendezVous
    {
        $query = "SELECT * FROM rdv WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new \Exception("Rendezvous not found");
        }

        return new RendezVous(

            $result['praticien_id'],
            $result['patient_id'],
            $result['specialite_id'],
            new \DateTimeImmutable($result['date'])
        );
    }

    public function getRendezvousByPraticienId(string $id_prat): array
    {
        $query = "SELECT * FROM rendezvous WHERE praticien_id = :id_prat";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id_prat', $id_prat, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $rendezvousList = [];
        foreach ($results as $result) {

            $rendezvousList[] = new RendezVous(
                $result['praticien_id'],
                $result['patient_id'],
                $result['specialite_id'],
                new \DateTimeImmutable($result['date'])
            );
        }

        return $rendezvousList;
    }


    public function save(RendezVous $rdv): string
    {
        // Assign values to variables to avoid "Only variables should be passed by reference"
        $rdv_id = Uuid::uuid4()->toString();
        $praticien_id = $rdv->praticien;
        $patient_id = $rdv->patient;
        $specialite_id = $rdv->specialite;
        $statut = $rdv->statut;
        $date = $rdv->date->format('Y-m-d H:i:s');

        // Adjust the query for PostgreSQL (or other DBs that don't support MySQL's ON DUPLICATE KEY)
        $query = "INSERT INTO rendezvous (id, praticien_id, patient_id, specialite_id, statut, date) 
              VALUES (:id, :praticien_id, :patient_id, :specialite_id, :statut, :date)
              ON CONFLICT(id) 
              DO UPDATE SET praticien_id = :praticien_id, patient_id = :patient_id, specialite_id = :specialite_id, statut = :statut, date = :date";

        $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':id', $rdv_id, PDO::PARAM_STR);
        $stmt->bindParam(':praticien_id', $praticien_id, PDO::PARAM_STR);
        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_STR);
        $stmt->bindParam(':specialite_id', $specialite_id, PDO::PARAM_STR);
        $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new \Exception("Failed to save Rendezvous");
        }

        return  $rdv_id;
    }
}
