<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

$id_usuario = $_SESSION['usuario'] ?? null;
if (!$id_usuario) {
    header('Location: index.php');
    exit();
}

$uploads_dir = __DIR__ . '/../../imagenes/usuarios';

$tiene_apodo = false;
$stmt = $conn->prepare("SHOW COLUMNS FROM usuarios LIKE 'apodo'");
if ($stmt) {
    $stmt->execute();
    $res_col = $stmt->get_result();
    $tiene_apodo = $res_col && $res_col->num_rows > 0;
    $stmt->close();
}

if (!$tiene_apodo) {
    $conn->query("ALTER TABLE usuarios ADD COLUMN apodo VARCHAR(100) DEFAULT NULL AFTER apellido");
}

if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0777, true);
}

$stmt = $conn->prepare('SELECT nombre, apellido, email, telefono, direccion, foto, apodo FROM usuarios WHERE id_usuario = ?');
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado ? $resultado->fetch_assoc() : null;
$stmt->close();

if (!$usuario) {
    header('Location: perfil.php');
    exit();
}

$nombre = $usuario['nombre'] ?? '';
$apellido = $usuario['apellido'] ?? '';
$email = $usuario['email'] ?? '';
$telefono = $usuario['telefono'] ?? '';
$direccion = $usuario['direccion'] ?? '';
$apodo = $usuario['apodo'] ?? '';
$foto_actual = $usuario['foto'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $apodo = trim($_POST['apodo'] ?? '');
    $nombre_archivo = $foto_actual;

    if ($nombre === '') {
        $error = 'El nombre es obligatorio.';
    }

    if ($error === '' && !empty($_FILES['foto']['name'])) {
        $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $permitidas, true)) {
            $error = 'Formato de imagen no permitido. Usa JPG, PNG o WEBP.';
        } elseif (!is_uploaded_file($_FILES['foto']['tmp_name'])) {
            $error = 'No se pudo procesar la imagen seleccionada.';
        } else {
            $nombre_archivo = time() . '_avatar_' . $id_usuario . '.' . $extension;
            $ruta_destino = $uploads_dir . '/' . $nombre_archivo;

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
                $error = 'Error al subir la imagen.';
            }
        }
    }

    if ($error === '') {
        $sql = 'UPDATE usuarios
                SET nombre = ?, apellido = ?, telefono = ?, direccion = ?, foto = ?, apodo = ?
                WHERE id_usuario = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssi', $nombre, $apellido, $telefono, $direccion, $nombre_archivo, $apodo, $id_usuario);

        if ($stmt->execute()) {
            $stmt->close();
            header('Location: perfil.php?edit=success');
            exit();
        }

        $error = 'Error al actualizar los datos del perfil.';
        $stmt->close();
    }
}

$foto_url = !empty($foto_actual) ? 'imagenes/usuarios/' . $foto_actual : 'imagenes/usuarios/default.png';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar perfil</title>
<style>
    body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at top, #1f1f1f 0%, #111 60%, #0a0a0a 100%);
        font-family: 'Poppins', Arial, sans-serif;
        color: #fff;
        padding: 24px;
    }

    .card {
        width: min(820px, 100%);
        background: rgba(25, 25, 25, 0.95);
        border: 1px solid rgba(255, 0, 128, 0.35);
        border-radius: 20px;
        box-shadow: 0 20px 45px rgba(255, 0, 128, 0.18);
        padding: 28px;
    }

    .header {
        text-align: center;
        margin-bottom: 24px;
    }

    .header h2 {
        margin: 0;
        font-size: 1.9rem;
        color: #ff74b8;
    }

    .header p {
        margin: 8px 0 0;
        color: #d8d8d8;
        font-size: 0.95rem;
    }

    .avatar-block {
        display: grid;
        place-items: center;
        gap: 10px;
        margin-bottom: 18px;
    }

    .foto-perfil {
        width: 132px;
        height: 132px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #ff2a98;
        box-shadow: 0 10px 22px rgba(255, 42, 152, 0.35);
        background: #0f0f0f;
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .field.full {
        grid-column: 1 / -1;
    }

    label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #ff9acc;
    }

    input {
        width: 100%;
        box-sizing: border-box;
        padding: 11px 12px;
        border-radius: 11px;
        border: 1px solid rgba(255, 0, 128, 0.45);
        background: #121212;
        color: #fff;
        outline: none;
    }

    input:focus {
        border-color: #ff5fb0;
        box-shadow: 0 0 0 3px rgba(255, 95, 176, 0.2);
    }

    .hint {
        font-size: 0.8rem;
        color: #bbbbbb;
        margin: -2px 0 2px;
    }

    .error {
        border: 1px solid rgba(255, 82, 82, 0.6);
        background: rgba(255, 82, 82, 0.1);
        color: #ffd0d0;
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 14px;
    }

    .actions {
        margin-top: 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .btn {
        border: none;
        text-decoration: none;
        padding: 11px 16px;
        border-radius: 11px;
        font-weight: 600;
        cursor: pointer;
        color: #fff;
    }

    .btn-primary {
        background: linear-gradient(135deg, #ff0f8f, #ff62b8);
    }

    .btn-secondary {
        background: #2a2a2a;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    @media (max-width: 720px) {
        .grid {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body>

<div class="card">
    <div class="header">
        <h2>Editar perfil</h2>
        <p>Actualiza tu foto, apodo y datos personales en un solo lugar.</p>
    </div>

    <?php if ($error !== ''): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="avatar-block">
            <img src="<?= htmlspecialchars($foto_url) ?>" class="foto-perfil" alt="Foto de perfil">
            <span class="hint">Correo de la cuenta: <?= htmlspecialchars($email) ?></span>
        </div>

        <div class="grid">
            <div class="field">
                <label for="nombre">Nombre</label>
                <input id="nombre" type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
            </div>

            <div class="field">
                <label for="apellido">Apellido</label>
                <input id="apellido" type="text" name="apellido" value="<?= htmlspecialchars($apellido) ?>">
            </div>

            <div class="field">
                <label for="apodo">Apodo</label>
                <input id="apodo" type="text" name="apodo" value="<?= htmlspecialchars($apodo) ?>" placeholder="Ej. Angie Beauty">
            </div>

            <div class="field">
                <label for="telefono">Teléfono</label>
                <input id="telefono" type="text" name="telefono" value="<?= htmlspecialchars($telefono) ?>">
            </div>

            <div class="field full">
                <label for="direccion">Dirección</label>
                <input id="direccion" type="text" name="direccion" value="<?= htmlspecialchars($direccion) ?>">
            </div>

            <div class="field full">
                <label for="foto">Foto de perfil</label>
                <input id="foto" type="file" name="foto" accept="image/png,image/jpeg,image/webp">
                <span class="hint">Formatos permitidos: JPG, PNG, WEBP.</span>
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="perfil.php" class="btn btn-secondary">Volver al perfil</a>
        </div>
    </form>
</div>

</body>
</html>
