<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

$id_usuario = $_SESSION['usuario'] ?? null;
if (!$id_usuario) {
    header("Location: index.php");
    exit();
}

// Crear carpeta si no existe
if (!is_dir("imagenes/usuarios")) {
    mkdir("imagenes/usuarios", 0777, true);
}

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$telefono = $_POST['telefono'];
$direccion = $_POST['direccion'];
$foto_actual = $_POST['foto_actual'];  // Oculto en el formulario

$nombre_archivo = $foto_actual;

// Si subió una nueva imagen
if (!empty($_FILES['foto']['name'])) {

    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

    $nombre_archivo = time() . "_avatar." . $ext;
    $ruta_destino = "imagenes/usuarios/" . $nombre_archivo;

    // Mover imagen
    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
        die("Error al subir la imagen");
    }
}

// Actualizar datos
$sql = "UPDATE usuarios 
        SET nombre=?, apellido=?, telefono=?, direccion=?, foto=? 
        WHERE id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $nombre, $apellido, $telefono, $direccion, $nombre_archivo, $id_usuario);

if ($stmt->execute()) {
    header("Location: perfil.php?edit=success");
} else {
    echo "Error al actualizar.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Perfil</title>

<style>
body { background:#111; color:white; font-family:Arial; }

.form {
    width: 60%;
    margin: 50px auto;
    background:#1b1b1b;
    padding:30px;
    border-radius:15px;
    border:2px solid #ff0080;
    box-shadow:0 0 20px rgba(255,0,150,0.4);
}

label { color:#ff80c0; font-size:14px; }

input {
    width:100%;
    padding:10px;
    margin-bottom:15px;
    border:1px solid #ff0080;
    background:black;
    color:white;
    border-radius:10px;
}

.foto-perfil {
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #ff0080;
    margin-bottom:10px;
}

button {
    background:#ff0080;
    border:none;
    padding:12px 25px;
    border-radius:10px;
    color:#fff;
    cursor:pointer;
}
button:hover { background:#ff4da6; }

.boton-volver {
    display:inline-block;
    margin-top:10px;
    color:#ff80c0;
    text-decoration:none;
}
</style>

</head>

<body>

<div class="form">
    <h2>Editar Datos Personales</h2>

    <?php if (isset($success)) echo "<p style='color:#00ff88'>$success</p>"; ?>

    <!-- Imagen -->
    <img src="imagenes/usuarios/<?= $foto ?: 'default.png' ?>" class="foto-perfil">

    <form method="POST" enctype="multipart/form-data">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= $nombre ?>" required>

        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?= $apellido ?>" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono" value="<?= $telefono ?>">

        <label>Dirección:</label>
        <input type="text" name="direccion" value="<?= $direccion ?>">

        <label>Cambiar foto:</label>
        <input type="file" name="foto" accept="image/*">

        <button type="submit">Guardar cambios</button>
    </form>

    <a href="perfil.php" class="boton-volver">⬅ Volver al perfil</a>
</div>

</body>
</html>
