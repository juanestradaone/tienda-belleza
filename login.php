<?php
session_start();
include("conexion.php");

// Activar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Filtrar el correo
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Consulta segura con prepare()
    $sql = "SELECT id_usuario, nombre, contrasena, rol FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña cifrada
        if (password_verify($password, $user['contrasena'])) {

            // Guardar datos en sesión
            $_SESSION['usuario'] = $user['id_usuario'];
            $_SESSION['nombre']  = $user['nombre'];
            $_SESSION['rol']     = $user['rol'];

            // Redirigir a tienda
            header("Location: tienda.php");
            exit;
        } else {
            header("Location: index.php?msg=❌ Contraseña incorrecta");
            exit;
        }
    } else {
        header("Location: index.php?msg=❌ Usuario no encontrado");
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php?msg=⚠️ No se recibieron datos del formulario");
    exit;
}
?>
