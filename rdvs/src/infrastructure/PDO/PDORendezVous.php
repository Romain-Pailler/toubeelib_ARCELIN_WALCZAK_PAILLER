<?php

namespace rdvs\infrastructure\PDO;

use Ramsey\Uuid\Uuid;
use PDO;
use rdvs\core\repositoryInterfaces\RdvRepositoryInterface;
use rdvs\core\domain\entities\rdv\RendezVous;

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
        $query = "SELECT * FROM rendezvous WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new \Exception("Rendezvous not found");
        }

        $new_rdv = new RendezVous(

            $result['praticien_id'],
            $result['patient_id'],
            $result['specialite_id'],
            new \DateTimeImmutable($result['date'])
        );

        $new_rdv->setID($result['id']);

        return $new_rdv;
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
        // Utiliser l'ID existant du rendez-vous s'il est défini, sinon générer un nouvel ID
        $rdv_id = $rdv->getID() ?? Uuid::uuid4()->toString();
        $praticien_id = $rdv->praticien;
        $patient_id = $rdv->patient;
        $specialite_id = $rdv->specialite;
        $statut = $rdv->statut;
        $date = $rdv->date->format('Y-m-d H:i:s');

        // Requête SQL pour insérer ou mettre à jour les données en cas de conflit
        $query = "
        INSERT INTO rendezvous (id, praticien_id, patient_id, specialite_id, statut, date) 
        VALUES (:id, :praticien_id, :patient_id, :specialite_id, :statut, :date)
        ON CONFLICT (id) 
        DO UPDATE SET 
            praticien_id = EXCLUDED.praticien_id, 
            patient_id = EXCLUDED.patient_id, 
            specialite_id = EXCLUDED.specialite_id, 
            statut = EXCLUDED.statut, 
            date = EXCLUDED.date
    ";

        // Préparation de la requête
        $stmt = $this->pdo->prepare($query);

        // Liaison des paramètres
        $stmt->bindParam(':id', $rdv_id, PDO::PARAM_STR);
        $stmt->bindParam(':praticien_id', $praticien_id, PDO::PARAM_STR);
        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_STR);
        $stmt->bindParam(':specialite_id', $specialite_id, PDO::PARAM_STR);
        $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);

        // Exécution de la requête
        if (!$stmt->execute()) {
            // Gestion des erreurs lors de l'exécution
            throw new \Exception("Failed to save or update Rendezvous");
        }

        return $rdv_id;
    }
}
