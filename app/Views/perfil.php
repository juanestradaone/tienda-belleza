<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['usuario'];

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

$sql = 'SELECT nombre, apellido, apodo, email, telefono, direccion, foto FROM usuarios WHERE id_usuario = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$stmt->bind_result($nombre, $apellido, $apodo, $email, $telefono, $direccion, $foto);
$stmt->fetch();
$stmt->close();

$foto_url = 'imagenes/usuarios/default.png';

if (!empty($foto)) {
    if (filter_var($foto, FILTER_VALIDATE_URL)) {
        $foto_url = $foto;
    } elseif (is_file(__DIR__ . '/../../imagenes/usuarios/' . $foto)) {
        $foto_url = 'imagenes/usuarios/' . $foto;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mi Perfil</title>
<style>
body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at top, #1f1f1f 0%, #111 60%, #0a0a0a 100%);
    color: white;
    font-family: 'Poppins', Arial, sans-serif;
    padding: 24px;
}
.perfil {
    width: min(780px, 100%);
    background: rgba(27, 27, 27, 0.96);
    padding: 28px;
    border-radius: 18px;
    border: 1px solid rgba(255, 0, 128, 0.35);
    box-shadow: 0 18px 40px rgba(255, 0, 150, 0.2);
}
.titulo {
    text-align: center;
    margin-bottom: 18px;
}
.titulo h2 { margin: 0; color: #ff74b8; }
.foto-wrapper { display: grid; place-items: center; margin-bottom: 16px; }
.avatar-fallback {
    width: 132px;
    height: 132px;
    border-radius: 50%;
    border: 3px solid #ff2a98;
    box-shadow: 0 10px 22px rgba(255, 42, 152, 0.35);
    background: radial-gradient(circle at 30% 20%, #ff74b8, #ff0f8f);
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
}
.foto-perfil {
    width: 132px;
    height: 132px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #ff2a98;
    box-shadow: 0 10px 22px rgba(255, 42, 152, 0.35);
}
.grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}
.campo {
    background: #111;
    border: 1px solid rgba(255, 0, 128, 0.25);
    border-radius: 12px;
    padding: 10px 12px;
}
.campo strong { color: #ff9acc; display: block; margin-bottom: 5px; font-size: 0.88rem; }
.campo span { color: #eee; word-break: break-word; }
.campo.full { grid-column: 1 / -1; }
.botones {
    margin-top: 18px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.boton {
    background: linear-gradient(135deg, #ff0f8f, #ff62b8);
    padding: 10px 14px;
    border-radius: 10px;
    text-decoration: none;
    color: #fff;
    font-weight: 600;
}
@media (max-width: 700px) {
    .grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<div class="perfil">
    <div class="titulo">
        <h2>Mi Perfil</h2>
    </div>

    <div class="foto-wrapper">
        <img src="<?= htmlspecialchars($foto_url) ?>" class="foto-perfil" alt="Foto de perfil" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="avatar-fallback" aria-hidden="true">👤</div>
    </div>

    <div class="grid">
        <div class="campo">
            <strong>Nombre</strong>
            <span><?= htmlspecialchars($nombre ?: '-') ?></span>
        </div>
        <div class="campo">
            <strong>Apellido</strong>
            <span><?= htmlspecialchars($apellido ?: '-') ?></span>
        </div>
        <div class="campo">
            <strong>Apodo</strong>
            <span><?= htmlspecialchars($apodo ?: 'Sin apodo') ?></span>
        </div>
        <div class="campo">
            <strong>Email</strong>
            <span><?= htmlspecialchars($email ?: '-') ?></span>
        </div>
        <div class="campo">
            <strong>Teléfono</strong>
            <span><?= htmlspecialchars($telefono ?: '-') ?></span>
        </div>
        <div class="campo full">
            <strong>Dirección</strong>
            <span><?= htmlspecialchars($direccion ?: '-') ?></span>
        </div>
    </div>

    <div class="botones">
       <a href="editar_perfil.php" class="boton">Editar datos</a>
       <a href="cambiar_password.php" class="boton">Cambiar contraseña</a>
       <a href="inicio.php" class="boton">Volver a la Tienda</a>
    </div>
</div>

</body>
</html>
