<?php
require_once __DIR__ . '/../models/Offer.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/AuthJWT.php';
require_once __DIR__ . '/../core/ApiResponse.php';

class OfferController {
    private $db;
    private $offer;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->offer = new Offer($this->db);
    }

    public function create($data) {
        try {
            // Vérifier le token JWT
            $headers = getallheaders();
            $token = $headers['Authorization'] ?? '';
            $token = str_replace('Bearer ', '', $token);
            
            $userData = AuthJWT::validateToken($token);
            
            // Vérifier le rôle (seuls les prestataires peuvent créer des offres)
            if ($userData['role'] !== 'prestataire') {
                throw new Exception("Action non autorisée", 403);
            }

            $this->offer->title = $data['title'];
            $this->offer->description = $data['description'];
            $this->offer->price = $data['price'];
            $this->offer->user_id = $userData['id']; // ID de l'utilisateur connecté

            if ($this->offer->create()) {
                ApiResponse::success(["message" => "Offre créée"]);
            } else {
                ApiResponse::error("Erreur lors de la création", 500);
            }
        } catch (Exception $e) {
            ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function getAll() {
        try {
            $offers = $this->offer->getAll();
            ApiResponse::success($offers);
        } catch (Exception $e) {
            ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
