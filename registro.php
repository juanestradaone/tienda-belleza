<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


include("conexion.php");

// Recibir datos del formulario
$nombre    = $_POST['nombre'];
$apellido  = $_POST['apellido'];
$direccion = $_POST['direccion'];
$email     = $_POST['email'];
$telefono  = $_POST['telefono'];
$password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Insertar en la tabla usuarios
$sql = "INSERT INTO usuarios (nombre, apellido, direccion, email, telefono, contrasena)
        VALUES ('$nombre', '$apellido', '$direccion', '$email', '$telefono', '$password')";

if ($conn->query($sql) === TRUE) {
    echo "✅ Usuario registrado con éxito.";
    header("refresh:2; url=index.html"); // vuelve al login
} else {
    echo "❌ Error: " . $conn->error;
}
?>
