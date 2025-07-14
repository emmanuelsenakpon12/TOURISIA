<?php
class Payment {
    private $conn;
    private $table_name = "payments";

    public $id;
    public $user_id;
    public $amount;
    public $method;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO $this->table_name 
                  (user_id, amount, method, status, created_at) 
                  VALUES (:user_id, :amount, :method, :status, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':method', $this->method);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
}
?>
