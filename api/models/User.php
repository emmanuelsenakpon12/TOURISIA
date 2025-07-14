<?php
require_once __DIR__ . '/../core/Validator.php';
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $status;

    public function __construct($db = null) {
        // Permet l'injection de connexion PDO ou l'instanciation autonome
        if ($db) {
            $this->conn = $db;
        } else {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    }

    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register() {
        Validator::validateEmail($this->email);
        Validator::validatePassword($this->password);

        if ($this->emailExists($this->email)) {
            throw new Exception("L'email est déjà utilisé", 409);
        }

        $query = "INSERT INTO " . $this->table_name . " (name, email, password, role, status) VALUES (:name, :email, :password, :role, 'active')";
        $stmt = $this->conn->prepare($query);

        $this->name = Validator::sanitizeInput($this->name);
        $this->email = Validator::sanitizeInput($this->email);
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->role = Validator::sanitizeInput($this->role);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':role', $this->role);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        throw new Exception("Erreur lors de l'inscription", 500);
    }

    /**
     * Connexion utilisateur
     */
    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($this->password, $user['password'])) {
            unset($user['password']); // Ne jamais retourner le hash
            return $user;
        }
        return false;
    }

    /**
     * Vérifie si un email existe déjà
     */
    private function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Récupère tous les utilisateurs (pour l'administration)
     */
    public function getAll() {
        $query = "SELECT id, name, email, role, status FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Active/désactive un utilisateur (pour l'administration)
     */
    public function toggleStatus($userId) {
        // Récupérer le statut actuel
        $query = "SELECT status FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }
        $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';

        // Mettre à jour le statut
        $updateQuery = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bindParam(':status', $newStatus);
        $updateStmt->bindParam(':id', $userId);
        return $updateStmt->execute();
    }
}
?>
