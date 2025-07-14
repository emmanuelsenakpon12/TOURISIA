<?php
require_once __DIR__ . '/../models/Reservation.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/AuthJWT.php';
require_once __DIR__ . '/../core/ApiResponse.php';

class ReservationController {
    private $db;
    private $reservation;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->reservation = new Reservation($this->db);
    }

    /**
     * Créer une réservation pour l'utilisateur connecté
     */
    public function create($data) {
        try {
            // Authentification JWT
            $headers = getallheaders();
            if (empty($headers['Authorization'])) {
                throw new Exception("Token d'authentification manquant", 401);
            }
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $userData = AuthJWT::validateToken($token);

            // Validation des données d'entrée
            if (empty($data['offer_id']) || !is_numeric($data['offer_id'])) {
                throw new Exception("ID d'offre manquant ou invalide", 400);
            }
            if (empty($data['date'])) {
                throw new Exception("Date de réservation requise", 400);
            }
            // Optionnel : valider le format de la date
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date'])) {
                throw new Exception("Format de date invalide (attendu : YYYY-MM-DD)", 400);
            }

            // Création de la réservation
            $this->reservation->user_id = $userData['id'];
            $this->reservation->offer_id = $data['offer_id'];
            $this->reservation->date = $data['date'];

            if ($this->reservation->create()) {
                ApiResponse::success([
                    'message' => 'Réservation confirmée',
                    'reservation_id' => $this->reservation->id
                ]);
            } else {
                ApiResponse::error("Échec de la réservation", 500);
            }
        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;
            ApiResponse::error($e->getMessage(), $code);
        }
    }

    /**
     * Récupérer les réservations de l'utilisateur connecté
     */
    public function getUserReservations() {
        try {
            // Authentification JWT
            $headers = getallheaders();
            if (empty($headers['Authorization'])) {
                throw new Exception("Token d'authentification manquant", 401);
            }
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $userData = AuthJWT::validateToken($token);

            $reservations = $this->reservation->getByUser($userData['id']);
            ApiResponse::success($reservations);
        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;
            ApiResponse::error($e->getMessage(), $code);
        }
    }
}
?>
