<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

$id_usuario = $_SESSION['usuario'] ?? null;
if (!$id_usuario) {
    header('Location: index.php');
    exit();
}

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual = $_POST['password_actual'] ?? '';
    $nueva = $_POST['nueva_password'] ?? '';
    $confirmar = $_POST['confirmar_password'] ?? '';

    if ($actual === '' || $nueva === '' || $confirmar === '') {
        $error = 'Completa todos los campos.';
    } elseif (strlen($nueva) < 8) {
        $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
    } elseif ($nueva !== $confirmar) {
        $error = 'La confirmación no coincide con la nueva contraseña.';
    } else {
        $stmt = $conn->prepare('SELECT contrasena FROM usuarios WHERE id_usuario = ? LIMIT 1');
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $stmt->bind_result($hash_actual);
        $stmt->fetch();
        $stmt->close();

        if (!$hash_actual || !password_verify($actual, $hash_actual)) {
            $error = 'La contraseña actual no es correcta.';
        } else {
            $nuevo_hash = password_hash($nueva, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('UPDATE usuarios SET contrasena = ? WHERE id_usuario = ?');
            $stmt->bind_param('si', $nuevo_hash, $id_usuario);

            if ($stmt->execute()) {
                $exito = 'Contraseña actualizada correctamente.';
            } else {
                $error = 'No se pudo actualizar la contraseña. Intenta nuevamente.';
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cambiar contraseña</title>
<style>
body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at top, #1f1f1f 0%, #111 60%, #0a0a0a 100%);
    color: #fff;
    font-family: 'Poppins', Arial, sans-serif;
    padding: 24px;
}
.card {
    width: min(520px, 100%);
    background: rgba(27, 27, 27, 0.96);
    border: 1px solid rgba(255, 0, 128, 0.35);
    border-radius: 18px;
    box-shadow: 0 18px 40px rgba(255, 0, 150, 0.2);
    padding: 26px;
}
h2 {
    margin: 0 0 8px;
    color: #ff74b8;
    text-align: center;
}
p {
    margin: 0 0 18px;
    color: #d8d8d8;
    text-align: center;
}
label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: #ff9acc;
    margin: 12px 0 6px;
}
input {
    width: 100%;
    box-sizing: border-box;
    padding: 11px 12px;
    border-radius: 11px;
    border: 1px solid rgba(255, 0, 128, 0.45);
    background: #121212;
    color: #fff;
}
.error,
.success {
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 12px;
}
.error {
    border: 1px solid rgba(255, 82, 82, 0.6);
    background: rgba(255, 82, 82, 0.1);
    color: #ffd0d0;
}
.success {
    border: 1px solid rgba(77, 255, 172, 0.45);
    background: rgba(77, 255, 172, 0.09);
    color: #ceffea;
}
.actions {
    margin-top: 16px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.btn {
    flex: 1;
    border: none;
    text-decoration: none;
    text-align: center;
    padding: 11px 16px;
    border-radius: 11px;
    font-weight: 600;
    color: #fff;
    cursor: pointer;
}
.btn-primary { background: linear-gradient(135deg, #ff0f8f, #ff62b8); }
.btn-secondary {
    background: #2a2a2a;
    border: 1px solid rgba(255, 255, 255, 0.2);
}
</style>
</head>
<body>
<div class="card">
    <h2>Cambiar contraseña</h2>
    <p>Actualiza tu contraseña desde tu perfil.</p>

    <?php if ($error !== ''): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($exito !== ''): ?>
        <div class="success"><?= htmlspecialchars($exito) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="password_actual">Contraseña actual</label>
        <input type="password" id="password_actual" name="password_actual" required>

        <label for="nueva_password">Nueva contraseña</label>
        <input type="password" id="nueva_password" name="nueva_password" minlength="8" required>

        <label for="confirmar_password">Confirmar nueva contraseña</label>
        <input type="password" id="confirmar_password" name="confirmar_password" minlength="8" required>

        <div class="actions">
            <button type="submit" class="btn btn-primary">Guardar contraseña</button>
            <a href="perfil.php" class="btn btn-secondary">Volver al perfil</a>
        </div>
    </form>
</div>
</body>
</html>
