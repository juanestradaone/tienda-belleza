<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

$message = '';
$email = $_GET['email'] ?? ($_POST['email'] ?? '');

// Paso 1: el usuario llega desde el correo con ?email=usuario@dominio
// Mostrar form para introducir código.
// Paso 2: POST action=verify_code -> verificar código y marcar sesión verificada
// Paso 3: POST action=reset_password -> cambiar contraseña (requiere verificación previa)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'verify_code') {
        $code = trim($_POST['code'] ?? '');
        if (!$email || !$code) {
            $message = 'Faltan datos.';
        } else {
            $stmt = $conn->prepare("SELECT code, expires_at FROM password_resets WHERE email = ? LIMIT 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $res = $stmt->get_result();
            if (!$res || $res->num_rows === 0) {
                $message = 'No se encontró una solicitud de recuperación para este correo.';
            } else {
                $row = $res->fetch_assoc();
                if (strtotime($row['expires_at']) < time()) {
                    $message = 'El código ha expirado. Solicita uno nuevo.';
                } elseif (hash_equals($row['code'], $code)) {
                    // Código válido: marcar sesión para permitir cambiar contraseña
                    $_SESSION['reset_verified_email'] = $email;
                    $message = 'Código verificado. Ahora puedes elegir una nueva contraseña.';
                } else {
                    $message = 'Código inválido.';
                }
            }
        }
    } elseif ($action === 'reset_password') {
        // Asegurarse que el email fue verificado en esta sesión
        $verified = $_SESSION['reset_verified_email'] ?? '';
        if (!$verified || $verified !== $email) {
            $message = 'No estás autorizado para restablecer esta contraseña. Verifica primero el código enviado al correo.';
        } else {
            $pass = $_POST['password'] ?? '';
            $pass2 = $_POST['password_confirm'] ?? '';

            if (strlen($pass) < 6) {
                $message = 'La contraseña debe tener al menos 6 caracteres.';
            } elseif ($pass !== $pass2) {
                $message = 'Las contraseñas no coinciden.';
            } else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE email = ?");
                $upd->bind_param('ss', $hash, $email);
                $upd->execute();

                // Borrar tokens/códigos existentes para ese email
                $del = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
                $del->bind_param('s', $email);
                $del->execute();

                // Limpiar verificación de sesión
                unset($_SESSION['reset_verified_email']);

                $message = 'Contraseña actualizada correctamente. Puedes iniciar sesión con tu nueva contraseña.';
            }
        }
    }
}

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer contraseña</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        .rec-form { max-width:480px; margin:40px auto; background:#111; padding:24px; border-radius:12px; border:1px solid rgba(255,20,147,0.08); }
        .rec-form h2 { color:#ff69b4; margin-bottom:12px }
        .rec-form input { width:100%; padding:10px; margin-bottom:12px; border-radius:8px; border:1px solid #333; background:#0b0b0b; color:#fff }
        .rec-form button{ background:#ff0080; color:#fff; border:none; padding:10px 14px; border-radius:8px; cursor:pointer }
        .rec-form p.msg{ color:#fff; margin-top:12px; font-size:0.95rem }
    </style>
</head>
<body>
    <div class="rec-form">
        <h2>Restablecer contraseña</h2>

        <?php if ($message): ?>
            <p class="msg"><?php echo htmlspecialchars($message); ?></p>
            <?php if (strpos($message, 'actualizada correctamente') !== false): ?>
                <p><a href="login.php">Ir a iniciar sesión</a></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($email) && empty($_SESSION['reset_verified_email'])): ?>
            <!-- Formulario para introducir el código enviado por correo -->
            <form method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <label>Introduce el código que recibiste por correo</label>
                <input type="text" name="code" required placeholder="000000" maxlength="6">
                <input type="hidden" name="action" value="verify_code">
                <button type="submit">Verificar código</button>
            </form>

        <?php elseif (!empty($email) && !empty($_SESSION['reset_verified_email']) && $_SESSION['reset_verified_email'] === $email): ?>
            <!-- Formulario para establecer nueva contraseña -->
            <form method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="action" value="reset_password">
                <label>Nueva contraseña</label>
                <input type="password" name="password" required>
                <label>Confirmar contraseña</label>
                <input type="password" name="password_confirm" required>
                <button type="submit">Restablecer contraseña</button>
            </form>

        <?php else: ?>
            <p class="msg">Accede desde el enlace que recibiste por correo (debe incluir el parámetro <code>email</code>).</p>
        <?php endif; ?>

    </div>
</body>
</html>
