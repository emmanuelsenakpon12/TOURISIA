<?php
$host = "localhost";
$user = "root";
$password = ""; 
$database = "tourisia"; 

// Connexion
$conn = new mysqli($host, $user, $password);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Créer la base si elle n'existe pas
$conn->query("CREATE DATABASE IF NOT EXISTS $database");
$conn->select_db($database);

// Lire et exécuter le fichier SQL
$sql = file_get_contents('migration.sql');
if ($conn->multi_query($sql)) {
    echo "Importation réussie !";
} else {
    echo "Erreur : " . $conn->error;
}

$conn->close();
?>
