<?php
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/AuthJWT.php';
require_once __DIR__ . '/../core/ApiResponse.php';

class PaymentController {
    private $db;
    private $payment;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();

        $this->payment = new Payment($this->db);
    }

    public function process($data) {
        try {
            // Authentification JWT
            $headers = getallheaders();
            $token = $headers['Authorization'] ?? '';
            $token = str_replace('Bearer ', '', $token);
            $userData = AuthJWT::validateToken($token);

            // Validation des données
            if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
                throw new Exception("Montant invalide", 400);
            }
            if (empty($data['method'])) {
                throw new Exception("Méthode de paiement requise", 400);
            }

            // Traitement du paiement
            $this->payment->user_id = $userData['id'];
            $this->payment->amount = $data['amount'];
            $this->payment->method = $data['method'];
            $this->payment->status = 'pending';

            if ($this->payment->create()) {
                ApiResponse::success([
                    'message' => 'Paiement initié',
                    'payment_id' => $this->payment->id
                ]);
            } else {
                ApiResponse::error("Échec du traitement", 500);
            }
        } catch (Exception $e) {
            ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
?>
