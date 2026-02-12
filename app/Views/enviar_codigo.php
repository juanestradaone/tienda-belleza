<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $email = trim($_POST['email'] ?? '');

    if ($action === 'send') {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Introduce un correo válido.';
        } else {
            // Crear tabla password_resets si no existe
            $conn->query("CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                code VARCHAR(10) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Generar código de 6 dígitos
            try {
                $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            } catch (Exception $e) {
                $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            }
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora

            // Eliminar códigos anteriores para ese email
            $del = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $del->bind_param('s', $email);
            $del->execute();

            // Insertar nuevo código
            $ins = $conn->prepare("INSERT INTO password_resets (email, code, expires_at) VALUES (?, ?, ?)");
            $ins->bind_param('sss', $email, $code, $expires);
            $ins->execute();

            // Construir enlace al formulario de verificación
            $host = $_SERVER['HTTP_HOST'];
            $base = rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
            $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $host . $base . '/enviar_codigo.php?email=' . urlencode($email);

            $subject = 'Recuperación de contraseña - Código de verificación';
            $body = "Has solicitado recuperar tu contraseña.\n\nCódigo de verificación: $code\n\nVisita el siguiente enlace para introducir el código:\n$link\n\nEste código expirará en 1 hora.";
            $headers = "From: no-reply@$host\r\n" .
                       "Reply-To: no-reply@$host\r\n" .
                       "Content-Type: text/plain; charset=utf-8\r\n";

            $sent = @mail($email, $subject, $body, $headers);

            if ($sent) {
                $message = 'Hemos enviado un correo con un código de verificación. Revisa tu bandeja (y la carpeta de spam).';
            } else {
                // En entornos locales sin SMTP mostrar el código y enlace para pruebas
                $message = "No se pudo enviar el correo desde este servidor.\nCódigo: $code\nEnlace: $link";
            }
        }

    } elseif ($action === 'verify') {
        $code = trim($_POST['code'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $code === '') {
            $message = 'Introduce el correo y el código que recibiste.';
        } else {
            $stmt = $conn->prepare("SELECT code, expires_at FROM password_resets WHERE email = ? LIMIT 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $res = $stmt->get_result();
            if (!$res || $res->num_rows === 0) {
                $message = 'No hay una solicitud de recuperación para este correo.';
            } else {
                $row = $res->fetch_assoc();
                if (strtotime($row['expires_at']) < time()) {
                    $message = 'El código ha expirado. Solicita uno nuevo.';
                } elseif (hash_equals($row['code'], $code)) {
                    // Código correcto: marcar sesión y redirigir a reset_password
                    $_SESSION['reset_verified_email'] = $email;
                    header('Location: reset_password.php?email=' . urlencode($email));
                    exit;
                } else {
                    $message = 'Código incorrecto.';
                }
            }
        }
    }
}

// Si viene por GET con email, prellenar el campo
$prefill_email = htmlspecialchars($_GET['email'] ?? '');

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enviar código de recuperación</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        .card { max-width:480px; margin:36px auto; background:#0b0b0b; padding:22px; border-radius:12px; border:1px solid rgba(255,20,147,0.06); }
        .card h2 { color:#ff69b4; margin-bottom:10px }
        .card label{ color:#ddd; display:block; margin-bottom:6px }
        .card input{ width:100%; padding:10px; margin-bottom:12px; border-radius:8px; border:1px solid #222; background:#090909; color:#fff }
        .card button{ background:linear-gradient(90deg,#ff0080,#ff69b4); color:#fff; border:none; padding:10px 14px; border-radius:8px; cursor:pointer }
        .card p.msg{ color:#fff; white-space:pre-line }
        .two-cols{ display:grid; grid-template-columns:1fr 1fr; gap:10px }
    </style>
</head>
<body>
    <div class="card">
        <h2>Solicitar código</h2>
        <?php if ($message): ?>
            <p class="msg"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST" style="margin-bottom:18px">
            <input type="hidden" name="action" value="send">
            <label>Correo electrónico</label>
            <input type="email" name="email" required value="<?php echo $prefill_email; ?>" placeholder="tu@correo.com">
            <button type="submit">Enviar código al correo</button>
        </form>

        <hr style="border-color:rgba(255,255,255,0.04); margin:16px 0">

        <h2>Ingresar código</h2>
        <form method="POST">
            <input type="hidden" name="action" value="verify">
            <label>Correo electrónico</label>
            <input type="email" name="email" required value="<?php echo $prefill_email; ?>" placeholder="tu@correo.com">
            <label>Código (6 dígitos)</label>
            <input type="text" name="code" required maxlength="6" pattern="\d{6}" placeholder="000000">
            <button type="submit">Verificar código</button>
        </form>
    </div>
</body>
</html>
