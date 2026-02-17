<?php
// ==================================================
// CONFIG.PHP - Función para enviar correo de recuperación
// ==================================================

require_once "vendor/autoload.php"; // PHPMailer instalado via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * enviarCorreo()
 * Envía un código de recuperación al correo del usuario
 *
 * @param string $email  Correo del usuario
 * @param string $codigo Código de 6 dígitos generado para recuperar contraseña
 */
function enviarCorreo($email, $codigo) {
    $mail = new PHPMailer(true);

    try {
        // -----------------------
        // Configuración SMTP
        // -----------------------
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';        // Servidor SMTP (Gmail)
        $mail->SMTPAuth   = true;
        $mail->Username   = 'correo@empresa.com';    // Correo de la empresa
        $mail->Password   = 'clave_empresa';         // Clave SMTP o App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // -----------------------
        // Remitente y destinatario
        // -----------------------
        $mail->setFrom('correo@empresa.com', 'Tienda Belleza'); // Mismo correo que Username
        $mail->addAddress($email); // Correo del usuario

        // -----------------------
        // Contenido del correo
        // -----------------------
        $mail->isHTML(true);
        $mail->Subject = "Código de recuperación";
        $mail->Body    = "
            <h3>Recuperación de contraseña</h3>
            <p>Tu código es:</p>
            <h2>$codigo</h2>
            <p>Este código expira en 10 minutos.</p>
        ";

        // -----------------------
        // Enviar correo
        // -----------------------
        $mail->send();

    } catch (Exception $e) {
        // Manejo de errores silencioso, opcional: guardar en log
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
    }
}
?>
