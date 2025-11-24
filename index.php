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

    <!-- Google Icons para logo -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
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

            <!-- BOTÓN GOOGLE LOGIN -->
         <div class="google-wrapper">
         <div class="g_id_signin"
         data-type="standard"
         data-shape="pill"
         data-theme="outline"
         data-text="signin_with"
         data-size="large"
         data-width="260">
          </div>
         </div>


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

    <style>
        /* Estilo del botón de Google */
        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            color: #444;
            font-weight: 600;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            text-decoration: none;
            border: 1px solid #ccc;
            transition: 0.3s;
        }

        .google-btn:hover {
            background: #f2f2f2;
        }

        .google-logo {
            width: 22px;
            margin-right: 10px;
        }

        .google-wrapper {
           margin-top: 15px;
           display: flex;
           justify-content: center;
        }

    </style>
    <script src="https://accounts.google.com/gsi/client" async defer></script>

<div id="g_id_onload"
     data-client_id="1037951440740-1kv55223vkl0ikag5999c0bsfdrrui6n.apps.googleusercontent.com"
     data-context="signin"
     data-ux_mode="redirect"
     data-login_uri="http://localhost/tienda-belleza/google_callback.php"
     data-auto_prompt="false">
</div>




</body>

</html>
