<?php
require_once "config/conexion.php";
require_once "config/config.php";

$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Correo no válido");
}

// Verificar si el correo existe
$stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    // 🔥 Generar código de 6 dígitos
    $codigo = rand(100000, 999999);

    // 🔥 Fecha de expiración (10 minutos)
    $expiracion = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    // Guardar código en la BD
    $update = $conexion->prepare("
        UPDATE usuarios 
        SET codigo_recuperacion = ?, 
            codigo_expira = ? 
        WHERE email = ?
    ");
    $update->bind_param("sss", $codigo, $expiracion, $email);
    $update->execute();

    // Enviar correo
    enviarCorreo($email, $codigo);
}

// Mensaje neutro (seguridad)
echo "Si el correo está registrado, recibirás un código.";
?>