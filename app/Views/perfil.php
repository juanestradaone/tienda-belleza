<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['usuario'];

$sql = "SELECT nombre, apellido, email, telefono, direccion, foto FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($nombre, $apellido, $email, $telefono, $direccion, $foto);
$stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Perfil</title>

<style>
body { background:#111; color:white; font-family:Arial; }
.perfil {
    width: 60%;
    margin: 40px auto;
    background:#1b1b1b;
    padding:30px;
    border-radius:15px;
    border:2px solid #ff0080;
    box-shadow:0 0 20px rgba(255,0,150,0.4);
}
.perfil label { color:#ff80c0; font-size:14px; }
.perfil input {
    width:100%;
    padding:10px;
    margin-bottom:15px;
    background:black;
    color:#fff;
    border:1px solid #ff0080;
    border-radius:10px;
}
.foto-perfil {
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #ff0080;
    margin-bottom:20px;
}
.boton {
    background:#ff0080;
    padding:10px 25px;
    border-radius:10px;
    border:none;
    color:#fff;
    cursor:pointer;
}
.boton:hover { background:#ff4da6; }
</style>

</head>
<body>

<div class="perfil">
    <h2>Mi Perfil</h2>

    <?php if ($foto): ?>
        <img src="imagenes/usuarios/<?= $foto ?>" class="foto-perfil">
    <?php else: ?>
        <img src="imagenes/usuarios/default.png" class="foto-perfil">
    <?php endif; ?>

    <label>Nombre:</label>
    <input value="<?= $nombre ?>" readonly>

    <label>Apellido:</label>
    <input value="<?= $apellido ?>" readonly>

    <label>Email:</label>
    <input value="<?= $email ?>" readonly>

    <label>Teléfono:</label>
    <input value="<?= $telefono ?>" readonly>

    <label>Dirección:</label>
    <input value="<?= $direccion ?>" readonly>

   <a href="editar_perfil.php" class="boton">Editar datos</a>
   <a href="cambiar_password.php" class="boton">Cambiar contraseña</a>
   <a href="inicio.php" class="boton">Volver a la Tienda</a>

</div>

</body>
</html>
