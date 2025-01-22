<?php

namespace toubeelib\infrastructure\PDO;

use PDO;
use Ramsey\Uuid\Uuid;
use toubeelib\core\repositoryInterfaces\PatientRepositoryInterface;
use toubeelib\core\domain\entities\patient\Patient;

class PDOPatient implements PatientRepositoryInterface
{
    private PDO $pdo;

    // Le constructeur prend la connexion PDO comme paramètre
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Sauvegarde un patient dans la base de données
     *
     * @param Patient $patient
     * @return string L'ID du patient enregistré
     */
    public function save(Patient $patient): string
    {
        try {
            // Prépare la requête SQL pour insérer un patient
            $stmt = $this->pdo->prepare("INSERT INTO patients (id, nom, prenom, adresse, tel, date_naissance) VALUES (:id, :nom, :prenom, :adresse, :tel, :date_naissance)");

            // Génère un UUID pour le patient
            $uuid = Uuid::uuid4()->toString();

            // Assigne l'UUID généré au patient
            $patient->setID($uuid); // Utilisez l'UUID ici

            // Récupère les autres informations du patient
            $nom = $patient->nom;
            $prenom = $patient->prenom;
            $adresse = $patient->adresse;
            $date_naissance = $patient->dateNaissance;
            $tel = $patient->tel;

            // Exécute la requête en liant les valeurs
            $stmt->execute([
                ':id' => $uuid,  // Utilisation de l'UUID généré
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':adresse' => $adresse,
                ':tel' => $tel,
                ':date_naissance' => $date_naissance
            ]);

            return $patient->getId();  // Retourne l'ID du patient enregistré (UUID)
        } catch (\PDOException $e) {
            // Capture l'exception et affiche l'erreur
            echo 'Error: ' . $e->getMessage();
            return '';  // Ou une valeur indicative d'erreur, comme null ou une exception spécifique
        }
    }



    /**
     * Récupère un patient par son ID
     *
     * @param string $id
     * @return Patient
     */
    public function getPatientById(string $id): Patient
    {
        // Prépare la requête SQL pour récupérer un patient
        $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new Patient(
                $data['id'],
                $data['nom'],
                $data['prenom'],
                $data['adresse'],
                $data['tel'],
                $data['date_naissance']

            );
        }

        throw new \Exception("Patient not found");
    }

    /**
     * Met à jour les informations d'un patient
     *
     * @param string $id
     * @param Patient $patient
     * @return Patient
     */
    public function updatePatient(string $id, Patient $patient): Patient
    {
        // Prépare la requête SQL pour mettre à jour un patient
        $stmt = $this->pdo->prepare("UPDATE patients SET nom = :nom, prenom = :prenom, tel = :tel, date_naissance = :date_naissance WHERE id = :id");

        $uuid = $patient->getID();
        $nom = $patient->nom;
        $prenom = $patient->prenom;
        $adresse = $patient->adresse;
        $date_naissance = $patient->dateNaissance;
        $tel = $patient->tel;



        // Exécute la requête en liant les valeurs
        $stmt->execute([
            ':id' => $uuid,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':adresse' => $adresse,
            ':tel' => $tel,
            ':date_naissance' => $date_naissance
        ]);


        return $patient;  // Retourne le patient mis à jour
    }

    /**
     * Supprime un patient de la base de données
     *
     * @param string $id
     * @return bool
     */
    public function deletePatient(string $id): bool
    {
        // Prépare la requête SQL pour supprimer un patient
        $stmt = $this->pdo->prepare("DELETE FROM patients WHERE id = :id");

        // Exécute la requête et retourne le résultat
        return $stmt->execute([':id' => $id]);
    }
}
