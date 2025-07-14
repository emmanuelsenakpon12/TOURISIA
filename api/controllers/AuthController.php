<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/AuthJWT.php';
require_once __DIR__ . '/../core/ApiResponse.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function register($data) {
        try {
            $this->user->name = $data['name'];
            $this->user->email = $data['email'];
            $this->user->password = $data['password'];
            $this->user->role = $data['role'] ?? 'client';

            if ($this->user->register()) {
                // Générer le token JWT
                $token = AuthJWT::generateToken([
                    'id' => $this->user->id,
                    'email' => $this->user->email,
                    'role' => $this->user->role
                ]);

                ApiResponse::success([
                    'message' => 'Inscription réussie',
                    'token' => $token
                ]);
            } else {
                ApiResponse::error("Erreur lors de l'inscription", 500);
            }
        } catch (Exception $e) {
            ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function login($data) {
        try {
            $this->user->email = $data['email'];
            $this->user->password = $data['password'];

            $user = $this->user->login();
            if ($user) {
                // Générer le token JWT
                $token = AuthJWT::generateToken([
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]);

                ApiResponse::success([
                    'message' => 'Connexion réussie',
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ]);
            } else {
                ApiResponse::error("Identifiants invalides", 401);
            }
        } catch (Exception $e) {
            ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
