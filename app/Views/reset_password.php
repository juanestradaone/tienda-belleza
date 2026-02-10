<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$message = '';

if (!$token) {
    $message = 'Token inválido.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token) {
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password_confirm'] ?? '';

    if (strlen($pass) < 6) {
        $message = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($pass !== $pass2) {
        $message = 'Las contraseñas no coinciden.';
    } else {
        // Buscar token
        $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ? LIMIT 1");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $res = $stmt->get_result();

        if (!$res || $res->num_rows === 0) {
            $message = 'Token inválido o expirado.';
        } else {
            $row = $res->fetch_assoc();
            if (strtotime($row['expires_at']) < time()) {
                $message = 'El enlace ha expirado. Solicita uno nuevo.';
            } else {
                $email = $row['email'];
                $hash = password_hash($pass, PASSWORD_DEFAULT);

                // Actualizar contraseña del usuario
                $upd = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE email = ?");
                $upd->bind_param('ss', $hash, $email);
                $upd->execute();

                // Borrar tokens existentes para ese email
                $del = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
                $del->bind_param('s', $email);
                $del->execute();

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
        .rec-form { max-width:420px; margin:40px auto; background:#111; padding:24px; border-radius:12px; border:1px solid rgba(255,20,147,0.08); }
        .rec-form h2 { color:#ff69b4; margin-bottom:12px }
        .rec-form input { width:100%; padding:10px; margin-bottom:12px; border-radius:8px; border:1px solid #333; background:#0b0b0b; color:#fff }
        .rec-form button{ background:#ff0080; color:#fff; border:none; padding:10px 14px; border-radius:8px; cursor:pointer }
        .rec-form p.msg{ color:#fff; margin-top:12px; font-size:0.95rem }
    </style>
</head>
<body>
    <div class="rec-form">
        <h2>Elige una nueva contraseña</h2>
        <?php if ($message): ?>
            <p class="msg"><?php echo $message; ?></p>
            <?php if (strpos($message, 'actualizada correctamente') !== false): ?>
                <p><a href="login.php">Ir a iniciar sesión</a></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!$message || (isset($message) && strpos($message, 'actualizada correctamente') === false)): ?>
        <form method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <label>Nueva contraseña</label>
            <input type="password" name="password" required>
            <label>Confirmar contraseña</label>
            <input type="password" name="password_confirm" required>
            <button type="submit">Restablecer contraseña</button>
        </form>
        <?php endif; ?>

    </div>
</body>
</html>
