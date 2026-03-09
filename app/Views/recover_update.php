<?php
require_once "../Config/conexion.php";
session_start();

if (!isset($_SESSION['email_recuperacion'])) {
    header("Location: /tienda-belleza/index.php");
    exit;
}

$password = trim($_POST['password'] ?? '');

if (empty($password)) {
    header("Location: /tienda-belleza/index.php");
    exit;
}

$email = $_SESSION['email_recuperacion'];

$hash = password_hash($password, PASSWORD_DEFAULT);

$update = $conn->prepare("
    UPDATE usuarios 
    SET contrasena = ?, 
        codigo_recuperacion = NULL,
        codigo_expira = NULL
    WHERE email = ?
");
$update->bind_param("ss", $hash, $email);
$update->execute();

unset($_SESSION['email_recuperacion']);
session_regenerate_id(true);

// 🔥 Redirección sin mensaje
header("Location: /tienda-belleza/index.php");
exit;
?>