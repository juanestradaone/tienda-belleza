<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Iniciar Sesión - Tienda Belleza</title>

<!-- Google Sign-In -->
<script src="https://accounts.google.com/gsi/client" async defer></script>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #0a0a0a, #1b1b1b, #2b2b2b);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Contenedor principal */
.login-container {
    background: #111;
    padding: 40px 30px;
    width: 90%;
    max-width: 420px;
    border-radius: 20px;
    border: 2px solid #ff1493;
    box-shadow: 0 0 25px rgba(255, 20, 147, 0.3);
    animation: fadeIn 0.8s ease-out;
    text-align: center;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

h2 {
    color: #ff69b4;
    text-shadow: 0 0 10px #ff69b4;
    margin-bottom: 20px;
}

/* Inputs */
.input-box {
    margin-bottom: 20px;
    text-align: left;
    color: #fff;
}

.input-box label {
    font-size: 1rem;
    font-weight: bold;
    color: #ff69b4;
}

.input-box input {
    width: 100%;
    padding: 12px;
    border-radius: 12px;
    margin-top: 8px;
    border: 2px solid #ff69b4;
    background: #1b1b1b;
    color: white;
    font-size: 1rem;
}

.input-box input:focus {
    outline: none;
    box-shadow: 0 0 15px #ff69b4;
}

/* Botón iniciar sesión */
.btn-login {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #ff1493, #ff69b4);
    border: none;
    border-radius: 12px;
    font-size: 1.2rem;
    font-weight: bold;
    color: white;
    cursor: pointer;
    transition: 0.3s;
}

.btn-login:hover {
    background: transparent;
    border: 2px solid #ff69b4;
    color: #ff69b4;
    box-shadow: 0 0 20px #ff69b4;
}

/* BOTÓN GOOGLE PERSONALIZADO */
.google-custom-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;

    background: linear-gradient(135deg, #ff1493, #ff69b4);
    padding: 14px 25px;
    border-radius: 12px;

    color: white;
    font-weight: bold;
    font-size: 1.1rem;
    cursor: pointer;
    text-decoration: none;

    border: 2px solid transparent;
    box-shadow: 0 0 18px rgba(255, 20, 147, 0.4);
    transition: all 0.3s ease;

    width: 100%;
    margin-top: 15px;
}

.google-custom-btn:hover {
    background: transparent;
    color: #ff69b4;
    border-color: #ff69b4;
    box-shadow: 0 0 25px #ff69b4;
    transform: translateY(-3px);
}

.google-custom-btn img {
    width: 25px;
    height: 25px;
    background: white;
    padding: 3px;
    border-radius: 50%;
}
</style>
</head>
<body>

<div class="login-container">

    <h2>Iniciar Sesión</h2>

    <!-- Formulario normal -->
    <form action="login.php" method="POST">
        <div class="input-box">
            <label for="email">Correo:</label>
            <input type="email" name="email" required>
        </div>

        <div class="input-box">
            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>
        </div>

        <button class="btn-login" type="submit">Ingresar</button>
    </form>

    <br><hr style="border-color:#ff69b4;"><br>

    <!-- Google config -->
    <div id="g_id_onload"
         data-client_id="1037951440740-1kv55223vkl0ikag5999c0bsfdrrui6n.apps.googleusercontent.com"
         data-context="signin"
         data-ux_mode="redirect"
         data-login_uri="http://localhost/tienda-belleza/google_callback.php"
         data-auto_prompt="false">
    </div>

    <!-- BOTÓN GOOGLE PERSONALIZADO -->
    <div onclick="google.accounts.id.prompt();" class="google-custom-btn">
        <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google">
        Iniciar con Google
    </div>

</div>

</body>
</html>
