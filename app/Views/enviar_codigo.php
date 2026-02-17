<?php
session_start();
require __DIR__ . '/../Config/conexion.php';
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Verificar si el correo existe
    $sql = "SELECT id_usuario FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        // Generar código de 6 dígitos
        $codigo = rand(100000, 999999);

        // Guardar en sesión
        $_SESSION['codigo_recuperacion'] = $codigo;
        $_SESSION['email_recuperacion'] = $email;

        // (Por ahora solo lo mostramos en pantalla)
        echo "
        <h2>Código de recuperación generado</h2>
        <p>Tu código es: <strong>$codigo</strong></p>
        <a href='enviar_codigo.php'>Continuar</a>
        ";

    } else {

        echo "
        <h2>Error</h2>
        <p>El correo no está registrado.</p>
        <a href='login.php'>Volver</a>
        ";
    }
}
?>
