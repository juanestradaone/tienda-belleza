<?php
require_once "../Config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST['email']) || empty($_POST['nueva_contrasena'])) {
        exit("Datos incompletos.");
    }

    $email = trim($_POST['email']);
    $nueva = trim($_POST['nueva_contrasena']);

    // Verificar que el usuario exista
    $verificar = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $verificar->bind_param("s", $email);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows !== 1) {
        exit("Usuario no válido.");
    }

    // Encriptar contraseña
    $hash = password_hash($nueva, PASSWORD_DEFAULT);

    $stmt = $conexion->prepare("
        UPDATE usuarios 
        SET contrasena = ?, 
            codigo_recuperacion = NULL,
            codigo_expira = NULL
        WHERE email = ?
    ");

    $stmt->bind_param("ss", $hash, $email);
    $stmt->execute();

    echo "Contraseña actualizada correctamente.";
}
?>