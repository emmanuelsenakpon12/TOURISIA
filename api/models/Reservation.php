<?php
class Reservation {
    private $conn;
    private $table_name = "reservations";

    public $id;
    public $user_id;
    public $offer_id;
    public $date;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO $this->table_name 
                  (user_id, offer_id, date, status) 
                  VALUES (:user_id, :offer_id, :date, 'confirmed')";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':offer_id', $this->offer_id);
        $stmt->bindParam(':date', $this->date);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getByUser($user_id) {
    $query = "SELECT * FROM $this->table_name WHERE user_id = :user_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    foreach ($reservations as &$reservation) {
        if (!empty($reservation['date'])) {
            $dateObj = DateTime::createFromFormat('Y-m-d', $reservation['date']);
            if ($dateObj) {
                $reservation['date'] = $dateObj->format('d_m_Y');
            }
        }
    }
    return $reservations;
}

}
?>
