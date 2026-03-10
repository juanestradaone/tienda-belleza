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

            <p class="forgot-password-link"><a href="#recuperar">¿Olvidaste tu contraseña?</a></p>

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
<div class="formulario" id="recuperar">

    <!-- PASO 1: INGRESAR CORREO -->
    <form id="formEmail">
        <img src="imagenes/logo.jpg" alt="Logo de la tienda" class="logo">
        <h2>Recuperar Contraseña</h2>

        <input type="email"
               name="email"
               placeholder="Correo registrado"
               required>

        <button type="submit">
            Enviar Código
        </button>
    </form>


    <!-- PASO 2: INGRESAR CÓDIGO -->
    <form id="formCodigo" style="display:none;">
        <h2>Ingresa el código</h2>

        <div class="codigo-container">
            <input type="text" maxlength="1" class="codigo">
            <input type="text" maxlength="1" class="codigo">
            <input type="text" maxlength="1" class="codigo">
            <input type="text" maxlength="1" class="codigo">
            <input type="text" maxlength="1" class="codigo">
            <input type="text" maxlength="1" class="codigo">
        </div>

        <input type="hidden" id="codigoCompleto">

        <button type="button" id="btnVerificar">
            Confirmar Código
        </button>
    </form>


    <!-- PASO 3: NUEVA CONTRASEÑA -->
    <form id="formNuevaPassword"
          method="POST"
          action="app/Views/recover_update.php"
          style="display:none;">

        <input type="hidden" name="codigo" id="codigoFinal">

        <input type="password"
               name="password"
               placeholder="Nueva contraseña"
               required>

        <button type="submit">
            Cambiar contraseña
        </button>
    </form>

</div>
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

        .forgot-password-link {
            margin-top: 18px;
            text-align: center;
        }

        .forgot-password-link a {
            color: #ff69b4;
            font-weight: 600;
        }
        /* Animación suave */
.formulario form {
    transition: all 0.4s ease;
}

/* Contenedor código */
.codigo-container {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 20px 0;
}

.codigo {
    width: 48px;
    height: 55px;
    text-align: center;
    font-size: 22px;
    border-radius: 10px;
    border: 1px solid #ddd;
    transition: 0.3s;
    background: #fff;
}

.codigo:focus {
    border-color: #ff69b4;
    outline: none;
    box-shadow: 0 0 8px rgba(255,105,180,0.4);
    transform: scale(1.05);
}

/* Botones elegantes */
.formulario button {
    background: linear-gradient(135deg, #ff69b4, #ff1493);
    border: none;
    padding: 12px;
    border-radius: 8px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

.formulario button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255,20,147,0.3);
}

/* Animación de entrada */
.fade-in {
    animation: fadeIn 0.4s ease forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
    </style>
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <script>
const formEmail = document.getElementById("formEmail");
const formCodigo = document.getElementById("formCodigo");
const formNuevaPassword = document.getElementById("formNuevaPassword");

const inputs = document.querySelectorAll(".codigo");
const codigoCompleto = document.getElementById("codigoCompleto");
const codigoFinal = document.getElementById("codigoFinal");
const btnVerificar = document.getElementById("btnVerificar");

// ===============================
// PASO 1 - Enviar correo (AJAX)
// ===============================
formEmail.addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(formEmail);

    fetch("app/Views/recover_send.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(() => {
        cambiarPaso(formEmail, formCodigo);
    })
    .catch(() => {
        alert("Error al enviar el código");
    });
});

// ===============================
// Auto salto entre cuadros
// ===============================
inputs.forEach((input, index) => {

    input.addEventListener("input", () => {
        input.value = input.value.replace(/[^0-9]/g, '');

        if (input.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }

        actualizarCodigo();
    });

    input.addEventListener("keydown", (e) => {
        if (e.key === "Backspace" && index > 0 && input.value === "") {
            inputs[index - 1].focus();
        }
    });
});

function actualizarCodigo() {
    let codigo = "";
    inputs.forEach(input => codigo += input.value);
    codigoCompleto.value = codigo;
}

// ===============================
// PASO 2 - Verificar código
// ===============================
btnVerificar.addEventListener("click", function() {

    const codigo = codigoCompleto.value;

    if (codigo.length !== 6) {
        alert("Ingresa los 6 dígitos");
        return;
    }

    fetch("app/Views/recover_verify.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "codigo=" + codigo
    })
    .then(res => res.text())
    .then(data => {
        if (data.trim() === "OK") {
            codigoFinal.value = codigo;
            cambiarPaso(formCodigo, formNuevaPassword);
        } else {
            alert("Código incorrecto o expirado");
        }
    });
});

// ===============================
// Animación entre pasos
// ===============================
function cambiarPaso(actual, siguiente) {
    actual.style.display = "none";
    siguiente.style.display = "block";
    siguiente.classList.add("fade-in");
}
</script>
<div id="g_id_onload"
     data-client_id="1037951440740-1kv55223vkl0ikag5999c0bsfdrrui6n.apps.googleusercontent.com"
     data-context="signin"
     data-ux_mode="redirect"
     data-login_uri="http://localhost/tienda-belleza/google_callback.php"
     data-auto_prompt="false">
</div>




</body>

</html>
