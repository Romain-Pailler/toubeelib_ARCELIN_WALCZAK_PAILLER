<?php

namespace toubeelib\infrastructure\PDO;

use PDO;
use Ramsey\Uuid\Uuid;
use toubeelib\core\domain\entities\user\User;
use toubeelib\core\repositoryInterfaces\AuthRepositoryInterface;

class PDOAuth implements AuthRepositoryInterface
{
    private PDO $pdo;

    // Le constructeur prend la connexion PDO comme paramètre
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): User
    {

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");

        $stmt->execute([':email' => $email]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($data) {

            $user = new User(
                $data['email'],
                $data['password'],
                $data['role']
            );

            $user->setID($data['id']);

            return $user;
        }

        throw new \Exception("User not found");
    }

    public function getUserById(string $id): User
    {
        // Prépare la requête SQL pour récupérer un patient
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new User(
                $data['email'],
                $data['password'],
                $data['role'],
            );
        }

        throw new \Exception("User not found");
    }


    public function save(User $user): string
    {
        // Hachage du mot de passe avant de le sauvegarder
        $hashedPassword = password_hash($user->password, PASSWORD_DEFAULT);

        if ($user->getId()) {
            // Si l'utilisateur a un ID, on effectue une mise à jour
            $stmt = $this->pdo->prepare("
            UPDATE users 
            SET email = :email, password = :password, role = :role 
            WHERE id = :id
        ");
            $stmt->execute([
                ':id' => $user->getId(),
                ':email' => $user->email,
                ':password' => $hashedPassword, // Utilisation du mot de passe haché
                ':role' => $user->role
            ]);
            return $user->getId();
        } else {
            // Sinon, on insère un nouvel utilisateur
            $userId = Uuid::uuid4()->toString(); // Génère un UUID unique
            $stmt = $this->pdo->prepare("
            INSERT INTO users (id, email, password, role) 
            VALUES (:id, :email, :password, :role)
        ");
            $stmt->execute([
                ':id' => $userId,
                ':email' => $user->email,
                ':password' => $hashedPassword, // Utilisation du mot de passe haché
                ':role' => $user->role
            ]);
            $user->setID($userId); // Assigner l'ID généré à l'objet utilisateur
            return $userId;
        }
    }
}
