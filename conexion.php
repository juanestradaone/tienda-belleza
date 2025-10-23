<?php
$host = "localhost";
$user = "root";      // en XAMPP por defecto es "root"
$pass = "";          // normalmente vacío
$db   = "belleza_y_glamur_angelita"; // tu base de datos

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}
 echo "✅ Conexión exitosa"
?>

