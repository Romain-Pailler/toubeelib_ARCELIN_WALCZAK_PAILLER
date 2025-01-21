<?php

namespace toubeelib\infrastructure\PDO;

use Exception;
use toubeelib\core\domain\entities\praticien\Specialite;
use toubeelib\core\domain\entities\praticien\Praticien;
use toubeelib\core\repositoryInterfaces\PraticienRepositoryInterface;
use PDO;
use Ramsey\Uuid\Uuid;

class PDOPraticien implements PraticienRepositoryInterface
{

    private PDO $pdo;

    // Le constructeur prend la connexion PDO comme paramètre
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getSpecialiteById(string $id): Specialite
    {
        // Vérifie que l'ID n'est pas vide
        if (empty($id)) {
            throw new Exception("L'ID de la spécialité ne peut pas être vide.");
        }

        // Prépare la requête SQL pour récupérer la spécialité par ID
        $sql = "SELECT * FROM specialite WHERE id = :id";

        // Exécute la requête et récupère le résultat
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR); // Utilise PDO::PARAM_STR pour un champ VARCHAR
        $stmt->execute();

        // Si aucune spécialité n'est trouvée
        if ($stmt->rowCount() === 0) {
            throw new Exception("Spécialité non trouvée pour l'ID : " . $id);
        }

        // Récupère le résultat sous forme d'objet
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Vérifie que les données attendues sont présentes avant de créer l'objet
        if (!isset($data['id'], $data['label'], $data['description'])) {
            throw new Exception("Données de la spécialité manquantes ou invalides.");
        }

        // Retourne un objet Specialite avec les données récupérées
        return new Specialite($data['id'], $data['label'], $data['description']);
    }



    public function save(Praticien $praticien): string
    {
        // Vérifie si le praticien existe déjà dans la base de données (par son ID)
        if (!empty($praticien->getId())) {
            // Vérification si l'ID existe déjà dans la base de données
            $sql = "SELECT COUNT(*) FROM praticien WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $praticien->getId());
            $stmt->execute();

            $count = $stmt->fetchColumn();

            if ($count > 0) {
                // Si l'ID existe, on effectue une mise à jour
                $sql = "UPDATE praticien SET nom = :nom, prenom = :prenom, adresse = :adresse, tel = :tel, 
            specialite_id = :specialite_id WHERE id = :id";

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':id', $praticien->getId());
                $stmt->bindParam(':nom', $praticien->getNom());
                $stmt->bindParam(':prenom', $praticien->getPrenom());
                $stmt->bindParam(':adresse', $praticien->getAdresse());
                $stmt->bindParam(':tel', $praticien->getTel());
                $stmt->bindParam(':specialite_id', $praticien->getSpecialite()->getId());

                $stmt->execute();

                return $praticien->getId();  // Retourne l'ID du praticien mis à jour
            }
        }

        // Si l'ID n'existe pas, on effectue une insertion
        $sql = "INSERT INTO praticien (id, nom, prenom, adresse, tel, specialite_id) 
            VALUES (:id, :nom, :prenom, :adresse, :tel, :specialite_id)";

        $uuid = Uuid::uuid4()->toString();
        $praticien->setID($uuid);
        $nom = $praticien->getNom();
        $prenom = $praticien->getPrenom();
        $adresse = $praticien->getAdresse();
        $tel = $praticien->getTel();
        $specialite_id = $praticien->getSpecialite()->getId();

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $uuid);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':tel', $tel);
        $stmt->bindParam(':specialite_id', $specialite_id);

        $stmt->execute();

        // Récupère l'ID généré pour le praticien
        return $uuid;
    }



    public function getPraticienById(string $id): Praticien
    {
        $sql = "SELECT * FROM praticien WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();

        // Si aucun praticien n'est trouvé
        if ($stmt->rowCount() === 0) {
            throw new Exception("Praticien non trouvé.");
        }

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        $sqlSpe = "SELECT * FROM specialite WHERE id = :id";
        $spe_id = $data['specialite_id'];
        $stmtSpe = $this->pdo->prepare($sqlSpe);
        $stmtSpe->bindParam(':id', $spe_id, \PDO::PARAM_STR);
        $stmtSpe->execute();

        $dataSpe = $stmtSpe->fetch(\PDO::FETCH_ASSOC);


        $prat = new Praticien($data['nom'], $data['prenom'], $data['adresse'], $data['tel']);
        $spe = new Specialite($data['specialite_id'], $dataSpe['label'], $dataSpe['description']);

        $prat->setSpecialite($spe);
        return $prat;
    }

    public function getPraticiensBySpecialite(string $specialite): array
    {
        // On récupère d'abord les praticiens en fonction de la spécialité
        $sql = "SELECT p.*, s.label AS specialite_label, s.description AS specialite_description 
            FROM praticien p 
            JOIN specialite s ON p.specialite_id = s.id 
            WHERE s.label = :specialite";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':specialite', $specialite, \PDO::PARAM_STR);
        $stmt->execute();

        $praticiens = [];

        // On crée les objets Praticien et Specialite avec toutes les données nécessaires
        while ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $specialite = new Specialite(
                $data['specialite_id'],
                $data['specialite_label'],
                $data['specialite_description']
            );

            $new_praticien = new Praticien(
                $data['nom'],
                $data['prenom'],
                $data['adresse'],
                $data['tel'],
            );

            $new_praticien->setID($data['id']);

            $new_praticien->setSpecialite($specialite);

            $praticiens[] = $new_praticien;
        }

        return $praticiens;
    }


    public function getPraticiensByCity(string $city): array
    {
        // Jointure pour récupérer les informations sur les spécialités associées
        $sql = "SELECT p.*, s.label AS specialite_label, s.description AS specialite_description 
                FROM praticien p
                JOIN specialite s ON p.specialite_id = s.id
                WHERE p.adresse LIKE :city";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':city', "%$city%", \PDO::PARAM_STR);
        $stmt->execute();

        $praticiens = [];

        // Création des objets Praticien et Specialite avec les données nécessaires
        while ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $specialite = new Specialite(
                $data['specialite_id'],
                $data['specialite_label'],
                $data['specialite_description']
            );

            $new_praticien = new Praticien(
                $data['nom'],
                $data['prenom'],
                $data['adresse'],
                $data['tel']
            );

            $new_praticien->setID($data['id']); // ID du praticien
            $new_praticien->setSpecialite($specialite); // Spécialité associée

            $praticiens[] = $new_praticien;
        }

        return $praticiens;
    }
}
