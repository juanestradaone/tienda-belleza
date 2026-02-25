<?php
require_once "config/conexion.php";

$email = $_POST['email'] ?? '';
$nueva = $_POST['nueva_password'] ?? '';

if (!$email || !$nueva) {
    exit("Datos inválidos");
}

$hash = password_hash($nueva, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("
    UPDATE usuarios 
    SET password = ?, 
        codigo_recuperacion = NULL,
        codigo_expira = NULL
    WHERE email = ?
");
$stmt->bind_param("ss", $hash, $email);
$stmt->execute();

echo "Contraseña actualizada correctamente.";
?>