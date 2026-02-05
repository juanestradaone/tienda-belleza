<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

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

    // 🔴 CLAVE PARA EVITAR EL ERROR
    $stmt->close();

    // Verificar contraseña
    if (!password_verify($passActual, $passHash)) {
        $error = "La contraseña actual no es correcta.";
    } else {

    // ❌ Evitar reutilizar la misma contraseña
    if (password_verify($passNueva, $passHash)) {
        $error = "La nueva contraseña no puede ser igual a la anterior.";
    } else {

        $nuevoHash = password_hash($passNueva, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE usuarios SET contrasena=? WHERE id_usuario=?");
        $update->bind_param("si", $nuevoHash, $id);
        $update->execute();
        $update->close();

        $success = "¡Contraseña actualizada correctamente!";
    }
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
.password-box {
    position: relative;
}

.password-box input {
    padding-right: 4px;
}

.toggle-eye {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
    opacity: 0.7;
}

.toggle-eye:hover {
    opacity: 1;
}



</style>
</head>

<body>

<div class="form">
    <h2>Cambiar Contraseña</h2>

    <?php if (isset($error)) echo "<p style='color:#ff4da6'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color:#00ff88'>$success</p>"; ?>

    <form method="POST">
        <label>Contraseña actual</label>
     <div class="password-box">
     <input type="password" id="passActual" name="password_actual" required>
     <span class="toggle-eye" onclick="togglePass('passActual', this)">👁️</span>
</div>

    <label>Nueva contraseña</label>
    <div class="password-box">
    <input type="password" id="passNueva" name="password_nueva" required>
    <span class="toggle-eye" onclick="togglePass('passNueva', this)">👁️</span>
</div>



        <button>Actualizar Contraseña</button>
    </form>
</div>
<script>
    function togglePassword() {
    const inputs = document.querySelectorAll('input[type="password"]');
    inputs.forEach(input => {
        input.type = input.type === "password" ? "text" : "password";
    });
}
</script>
<script>
    function togglePass(id, icon) {
    const input = document.getElementById(id);

    if (input.type === "password") {
        input.type = "text";
        icon.textContent = "🙈";
    } else {
        input.type = "password";
        icon.textContent = "👁️";
    }
}
</script>


</body>
</html>
