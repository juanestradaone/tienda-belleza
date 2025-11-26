<?php
session_start();
include("conexion.php");

// Activar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $sql = "SELECT id_usuario, nombre, contrasena, rol FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['contrasena'])) {

            $_SESSION['usuario'] = $user['id_usuario'];
            $_SESSION['nombre']  = $user['nombre'];
            $_SESSION['rol']     = $user['rol'];

            header("Location: inicio.php"); 
            exit;

        } else {
            header("Location: index.php?msg=❌ Contraseña incorrecta");
            exit;
        }

    } else {
        header("Location: index.php?msg=❌ Usuario no encontrado");
        exit;
    }
}
?>
