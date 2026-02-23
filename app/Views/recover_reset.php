<?php
if (empty($_GET['email'])) {
    exit("Acceso no permitido.");
}

$email = htmlspecialchars($_GET['email']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nueva contraseña</title>
</head>
<body>

<h2>Cambiar contraseña</h2>

<form action="recover_update.php" method="POST">
    
    <input type="hidden" name="email" value="<?php echo $email; ?>">

    <label>Nueva contraseña:</label>
    <input type="password" name="nueva_contrasena" required>

    <br><br>

    <button type="submit">Actualizar contraseña</button>

</form>

</body>
</html>