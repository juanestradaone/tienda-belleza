<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['usuario'];

if ($_POST) {
    $passActual = $_POST["password_actual"];
    $passNueva = $_POST["password_nueva"];

    // Obtener contraseña actual del usuario
    $sql = "SELECT contrasena FROM usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($passHash);
    $stmt->fetch();

    // Verificar contraseña
    if (!password_verify($passActual, $passHash)) {
        $error = "La contraseña actual no es correcta.";
    } else {
        $nuevoHash = password_hash($passNueva, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE usuarios SET contrasena=? WHERE id_usuario=?");
        $update->bind_param("si", $nuevoHash, $id);
        $update->execute();
        $success = "¡Contraseña actualizada correctamente!";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cambiar Contraseña</title>

<style>
body { background:#111; color:#fff; font-family:Arial; }
.form {
    width: 50%;
    margin: 50px auto;
    background:#1b1b1b;
    padding:30px;
    border-radius:15px;
    border:2px solid #ff0080;
}
input {
    width:100%;
    padding:10px;
    margin-bottom:15px;
    border:1px solid #ff0080;
    background:black;
    color:white;
    border-radius:10px;
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
</style>
</head>

<body>

<div class="form">
    <h2>Cambiar Contraseña</h2>

    <?php if (isset($error)) echo "<p style='color:#ff4da6'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color:#00ff88'>$success</p>"; ?>

    <form method="POST">
        <label>Contraseña actual</label>
        <input type="password" name="password_actual" required>

        <label>Nueva contraseña</label>
        <input type="password" name="password_nueva" required>

        <button>Actualizar Contraseña</button>
    </form>
</div>

</body>
</html>
