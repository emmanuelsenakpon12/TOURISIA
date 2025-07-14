<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/AuthJWT.php';
require_once __DIR__ . '/../core/ApiResponse.php';

class AdminController {
    private $db;
    private $user;

    public function __construct() {
        // Correction : instanciation de la connexion PDO avec getConnection()
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    /**
     * Liste tous les utilisateurs (accès réservé aux admins)
     */
    public function users() {
        try {
            // Vérification du token et du rôle admin
            $headers = getallheaders();
            if (empty($headers['Authorization'])) {
                throw new Exception("Token d'authentification manquant", 401);
            }
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $userData = AuthJWT::validateToken($token);

            if (empty($userData['role']) || $userData['role'] !== 'admin') {
                throw new Exception("Accès refusé", 403);
            }

            // Récupération des utilisateurs
            $users = $this->user->getAll();
            ApiResponse::success($users);
        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;
            ApiResponse::error($e->getMessage(), $code);
        }
    }

    /**
     * Active/désactive un utilisateur (accès réservé aux admins)
     * @param array $data : doit contenir la clé 'user_id'
     */
    public function toggleUserStatus($data) {
        try {
            // Vérification admin
            $headers = getallheaders();
            if (empty($headers['Authorization'])) {
                throw new Exception("Token d'authentification manquant", 401);
            }
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $userData = AuthJWT::validateToken($token);

            if (empty($userData['role']) || $userData['role'] !== 'admin') {
                throw new Exception("Accès refusé", 403);
            }

            // Validation de l'entrée
            if (empty($data['user_id']) || !is_numeric($data['user_id'])) {
                throw new Exception("ID utilisateur manquant ou invalide", 400);
            }

            // Activation/désactivation
            if ($this->user->toggleStatus($data['user_id'])) {
                ApiResponse::success(['message' => 'Statut utilisateur mis à jour']);
            } else {
                ApiResponse::error("Échec de la mise à jour", 500);
            }
        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;
            ApiResponse::error($e->getMessage(), $code);
        }
    }
}
?>
