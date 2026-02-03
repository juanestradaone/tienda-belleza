<?php
$host = "localhost";
$user = "root";      // En XAMPP por defecto es "root"
$pass = "";          // Normalmente vacío
$db   = "belleza_y_glamur_angelita"; // Nombre de tu base de datos

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Conexión correcta, no mostramos mensaje para evitar interferencias
// echo "✅ Conexión exitosa";
?>
