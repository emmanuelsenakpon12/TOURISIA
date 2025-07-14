<?php
require_once __DIR__ . '/config/config.php'; 
try {
    $database = new Database();
    $conn = $database->getConnection();
    if ($conn) {
        echo "Connexion PDO réussie !";
    } else {
        echo "La connexion PDO a échoué.";
    }
} catch (Exception $e) {
    echo "Erreur lors de la connexion : " . $e->getMessage();
}
?>
