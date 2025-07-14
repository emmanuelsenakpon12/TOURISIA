<?php
require_once __DIR__ . '/../core/Validator.php';

class Offer {
    private $conn;
    private $table_name = "offers";

    public $id;
    public $title;
    public $description;
    public $price;
    public $user_id;

    public function __construct($db){
        $this->conn = $db;
    }

    public function create() {
        // Validation
        Validator::validateOfferData([
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price
        ]);

        $query = "INSERT INTO " . $this->table_name . " (title, description, price, user_id) VALUES (:title, :description, :price, :user_id)";
        $stmt = $this->conn->prepare($query);

        $this->title = Validator::sanitizeInput($this->title);
        $this->description = Validator::sanitizeInput($this->description);
        $this->price = Validator::sanitizeInput($this->price);
        $this->user_id = Validator::sanitizeInput($this->user_id);

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
