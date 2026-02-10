<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php?msg=⚠️ Debes iniciar sesión');
    exit;
}

$nombreUsuario = $_SESSION['nombre'] ?? 'usuario';
$nombreSeguro = htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido | Belleza y Glamour Angelita</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1b1b1b 50%, #2b2b2b 100%);
            color: #fff;
        }

        .welcome-card {
            width: min(92vw, 520px);
            text-align: center;
            padding: 2rem;
            border-radius: 18px;
            background: rgba(17, 17, 17, 0.9);
            border: 1px solid #ff69b4;
            box-shadow: 0 10px 28px rgba(255, 20, 147, 0.25);
            animation: fadeInUp 350ms ease-out;
        }

        .welcome-card h1 {
            margin: 0 0 .75rem 0;
            color: #ff69b4;
            text-shadow: 0 0 10px rgba(255, 105, 180, .45);
            font-size: clamp(1.5rem, 3vw, 2.15rem);
        }

        .welcome-card p {
            margin: 0;
            color: #f6d2e5;
            font-size: 1rem;
        }

        .loader {
            width: 54px;
            height: 54px;
            margin: 1.35rem auto 0;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.22);
            border-top-color: #ff1493;
            animation: spin .9s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <main class="welcome-card" role="status" aria-live="polite">
        <h1>¡Bienvenido, <?php echo $nombreSeguro; ?>!</h1>
        <p>Estamos preparando tu inicio...</p>
        <div class="loader" aria-hidden="true"></div>
    </main>

    <script>
        setTimeout(function () {
            window.location.href = 'inicio.php';
        }, 3000);
    </script>
</body>
</html>
