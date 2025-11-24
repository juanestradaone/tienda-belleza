<?php
session_start();

// Si el usuario ya inició sesión, lo redirigimos a la tienda
if (isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit;
}

// Capturar mensajes de login o registro (opcionales)
$mensaje = "";
if (isset($_GET['msg'])) {
    $mensaje = htmlspecialchars($_GET['msg']);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Belleza y Glamour Angelita</title>
    <link rel="stylesheet" href="login.css" />
</head>

<body>
    <div class="container">
        <?php if (!empty($mensaje)): ?>
            <p class="mensaje"><?= $mensaje ?></p>
        <?php endif; ?>

        <!-- LOGIN -->
        <form method="POST" action="login.php" class="formulario active" id="login-form">
            <img src="imagenes/logo.jpg" alt="Logo de la tienda" class="logo">
            <h2>Iniciar Sesión</h2>
            <input type="email" name="email" placeholder="Correo electrónico" required />
            <input type="password" name="password" placeholder="Contraseña" required />
            <button type="submit">Ingresar</button>
            <p>¿No tienes cuenta? <a href="#registro">Registrarse</a></p>
        </form>

        <!-- REGISTRO -->
        <form method="POST" action="registro.php" class="formulario" id="registro">
            <img src="imagenes/logo.jpg" alt="Logo de la tienda" class="logo">
            <h2>Crear Cuenta</h2>
            <input type="text" name="nombre" placeholder="Nombre" required />
            <input type="text" name="apellido" placeholder="Apellido" required />
            <input type="text" name="direccion" placeholder="Dirección" />
            <input type="email" name="email" placeholder="Correo electrónico" required />
            <input type="text" name="telefono" placeholder="Teléfono" />
            <input type="password" name="password" placeholder="Contraseña" required />
            <button type="submit">Registrarse</button>
            <p>¿Ya tienes cuenta? <a href="#login-form">Iniciar Sesión</a></p>
        </form>

        <!-- RECUPERAR -->
        <form class="formulario" id="recuperar">
            <img src="imagenes/logo.jpg" alt="Logo de la tienda" class="logo">
            <h2>Recuperar Contraseña</h2>
            <input type="email" placeholder="Correo registrado" required />
            <button type="submit">Enviar enlace de recuperación</button>
            <p>Volver a <a href="#login-form">iniciar sesión</a></p>
        </form>
    </div>

    <script src="script.js"></script>
</body>

</html>
