<?php
// ==================================================
// CONFIG.PHP - Envío de código de recuperación
// ==================================================

// Mostrar errores solo en desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cargar PHPMailer
require_once __DIR__ . "/../../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreo($email, $codigo)
{
    $mail = new PHPMailer(true);

    try {
        // -----------------------
        // CONFIGURACIÓN SMTP GMAIL
        // -----------------------
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'bellezayglamurangelita@gmail.com';
        $mail->Password   = 'ndozmxcniwiiviri'; // 🔥 App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // -----------------------
        // REMITENTE Y DESTINO
        // -----------------------
        $mail->setFrom('bellezayglamurangelita@gmail.com', 'Tienda Belleza');
        $mail->addAddress($email);

        // -----------------------
        // CONTENIDO
        // -----------------------
                $mail->isHTML(true);
                $mail->Subject = "Código de recuperación";
                $mail->Body    = <<<HTML
                <!doctype html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Recuperación de contraseña</title>
                    <style>
                        body{font-family: Arial, Helvetica, sans-serif;background:#f7f7f7;margin:0;padding:20px}
                        .email-container{max-width:600px;margin:0 auto;background:#ffffff;border-radius:8px;padding:20px;border:1px solid #eee}
                        .header{font-size:18px;color:#333;margin-bottom:12px}
                        .intro{color:#555;font-size:14px}
                        .code{display:inline-block;background:#fdeef3;color:#e91e63;font-size:28px;padding:10px 18px;border-radius:6px;margin:12px 0;font-weight:700;letter-spacing:2px}
                        .footer{font-size:12px;color:#888;margin-top:16px}
                        @media (max-width:480px){.email-container{padding:16px}.code{font-size:22px;padding:8px 14px}}
                    </style>
                </head>
                <body>
                    <div class="email-container">
                        <div class="header">Tienda Belleza - Recuperación de contraseña</div>
                        <p class="intro">Hola,</p>
                        <p class="intro">Usa el siguiente código para recuperar tu contraseña. Expira en 10 minutos.</p>
                        <div class="code">$codigo</div>
                        <p class="footer">Si no solicitaste este código, puedes ignorar este correo.</p>
                    </div>
                </body>
                </html>
                HTML;

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
        return false;
    }
}
?>
