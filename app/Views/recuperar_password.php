<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Por favor ingresa un correo válido.';
    } else {
        // Verificar que el usuario exista
        $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            // Crear tabla password_resets si no existe
            $create = "CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                token VARCHAR(128) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $conn->query($create);

            // Generar token
            $token = bin2hex(random_bytes(24));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora

            // Insertar token
            $ins = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $ins->bind_param('sss', $email, $token, $expires);
            $ins->execute();

            // Construir URL de recuperación
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $base = dirname($_SERVER['REQUEST_URI']);
            $resetLink = $protocol . '://' . $host . $base . '/reset_password.php?token=' . $token;

            // Enviar correo (mail debe estar configurado en el servidor)
            $subject = 'Recuperar contraseña - Belleza y Glamour Angelita';
            $body = "Hola,\n\nRecibes este correo porque solicitaste recuperar la contraseña.\n\n" .
                    "Haz clic en el siguiente enlace para elegir una nueva contraseña (válido 1 hora):\n\n" . $resetLink . "\n\n" .
                    "Si no solicitaste esto, puedes ignorar este mensaje.\n\nSaludos,\nBelleza y Glamour Angelita";

            $headers = 'From: no-reply@' . $host . "\r\n" . 'Reply-To: no-reply@' . $host . "\r\n";

            $sent = false;
            // Intentar enviar correo; si falla, mostramos el enlace para desarrollo local
            if (function_exists('mail')) {
                $sent = mail($email, $subject, $body, $headers);
            }

            if ($sent) {
                $message = 'Se envió un enlace de recuperación a tu correo.';
            } else {
                $message = 'No se pudo enviar el correo desde este servidor. Copia y pega este enlace en el navegador para recuperar la contraseña:<br><br>' .
                           '<a href="' . htmlspecialchars($resetLink) . '">' . htmlspecialchars($resetLink) . '</a>';
            }

        } else {
            // No se revela si el email existe por seguridad
            $message = 'Si el correo existe en nuestro sistema recibirás un enlace para recuperar la contraseña.';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar contraseña</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        /* Base page styling to match inicio.php theme */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1b1b1b 50%, #2b2b2b 100%);
            color: #f5f5f5;
            -webkit-font-smoothing:antialiased;
            -moz-osx-font-smoothing:grayscale;
            min-height: 100vh;
            margin: 0;
        }

        a { color: #ff69b4; }

        .rec-form {
            max-width: 520px;
            margin: 40px auto;
            background: linear-gradient(180deg, #0f0f0f 0%, #151515 100%);
            padding: 28px;
            border-radius: 14px;
            border: 1px solid rgba(255,20,147,0.06);
            box-shadow: 0 12px 40px rgba(0,0,0,0.65), 0 0 40px rgba(255,20,147,0.03) inset;
        }

        .rec-form h2 { color:#ff69b4; margin-bottom:8px; font-size:1.4rem; }

        .rec-form p.lead { color:#d0c7d2; margin:0 0 18px 0; }

        .rec-form input {
            width:100%;
            padding:12px;
            margin-bottom:12px;
            border-radius:10px;
            border:1px solid rgba(255,20,147,0.06);
            background: rgba(11,11,11,0.6);
            color:#fff;
            transition: box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .rec-form input:focus {
            outline: none;
            box-shadow: 0 6px 20px rgba(255,20,147,0.08);
            border-color: rgba(255,20,147,0.2);
        }

        .rec-form button{
            background: linear-gradient(135deg,#ff1493,#ff69b4);
            color:#fff;
            border:none;
            padding:10px 16px;
            border-radius:10px;
            cursor:pointer;
            font-weight:700;
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }

        .rec-form button:hover{ transform: translateY(-3px); box-shadow: 0 10px 30px rgba(255,20,147,0.12); }

        .rec-form .msg{ color:#fff; margin-top:12px; font-size:0.95rem }

        /* Responsive tweaks */
        @media (max-width: 600px) {
            .rec-form { margin: 20px 16px; padding:18px; }
        }
    </style>
</head>
<body>
    <header style="background:linear-gradient(135deg,#111,#1b1b1b); padding:18px 0; border-bottom:2px solid #ff1493; box-shadow:0 4px 20px rgba(255,20,147,0.12);">
        <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;gap:12px;padding:0 16px;">
            <img src="/imagenes/logo.jpg" alt="Logo" style="width:56px;height:56px;border-radius:8px;object-fit:cover;border:2px solid rgba(255,20,147,0.08);">
            <h1 style="color:#fff;font-size:1.1rem;margin:0;text-transform:uppercase;letter-spacing:1px;">Belleza y Glamour Angelita</h1>
        </div>
    </header>

    <main style="padding:32px 16px;">
        <div style="max-width:680px;margin:0 auto;">
            <div class="rec-form" style="background:linear-gradient(180deg,#0f0f0f,#151515);padding:28px;border-radius:14px;border:1px solid rgba(255,20,147,0.06);box-shadow:0 10px 30px rgba(0,0,0,0.6);">
                <h2 style="color:#ff69b4;margin:0 0 8px 0;font-size:1.4rem;">Recuperar Contraseña</h2>
                <p style="color:#d0c7d2;margin:0 0 18px 0;">Introduce el correo asociado a tu cuenta y te enviaremos un enlace para restablecer tu contraseña.</p>

                <form method="POST" onsubmit="this.querySelector('button').disabled=true;">
                    <label for="email" style="color:#fff;display:block;margin-bottom:6px;font-weight:600;">Correo registrado</label>
                    <input id="email" name="email" type="email" required style="width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,20,147,0.08);background:#0b0b0b;color:#fff;margin-bottom:12px;" />
                    <div style="display:flex;gap:10px;align-items:center;">
                        <button type="submit" style="background:linear-gradient(135deg,#ff1493,#ff69b4);color:#fff;border:none;padding:10px 16px;border-radius:10px;cursor:pointer;font-weight:700;">Enviar enlace de recuperación</button>
                        <a href="login.php" style="color:#ff69b4;font-weight:600;text-decoration:none;margin-left:auto;">Volver a iniciar sesión</a>
                    </div>
                </form>

                <?php if ($message): ?>
                    <div style="margin-top:16px;padding:12px;border-radius:8px; background:rgba(255,255,255,0.03);border:1px solid rgba(255,20,147,0.04);">
                        <p class="msg" style="color:#fff;margin:0;line-height:1.4;">
                            <?php echo $message; ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div style="margin-top:18px;color:#bdb0bf;font-size:0.9rem;">
                    <strong>Consejo:</strong> Si estás en desarrollo local y el servidor no envía correos, copia el enlace que aparece y pégalo en la barra del navegador.
                </div>
            </div>
        </div>
    </main>

    <footer style="padding:24px 0;margin-top:28px;background:transparent;color:#fff;text-align:center;">
        <p style="margin:0;color:#c9b9c7;">© 2025 Belleza y Glamour Angelita</p>
    </footer>
</body>
</html>
