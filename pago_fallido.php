
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pago Fallido</title>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: linear-gradient(135deg, #0a0a0a 0%, #1b1b1b 50%, #2b2b2b 100%);
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
    }

    .card {
        background: #111;
        padding: 40px 30px;
        border-radius: 20px;
        text-align: center;
        width: 90%;
        max-width: 450px;
        border: 2px solid #ff1493;
        box-shadow: 0 0 25px rgba(255, 20, 147, 0.3);
        animation: fadeIn 0.8s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .icon {
        font-size: 60px;
        margin-bottom: 15px;
        color: #ff3366;
        text-shadow: 0 0 15px rgba(255, 51, 102, 0.7);
    }

    h1 {
        font-size: 2rem;
        margin-bottom: 15px;
        color: #ff69b4;
        text-shadow: 0 0 10px #ff69b4;
    }

    p {
        color: #ddd;
        font-size: 1rem;
        margin-bottom: 25px;
    }

    a.btn {
        display: block;
        margin: 10px auto;
        width: 80%;
        padding: 12px 20px;
        border-radius: 15px;
        text-decoration: none;
        font-weight: bold;
        background: linear-gradient(135deg, #ff1493, #ff69b4);
        color: white;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    a.btn:hover {
        background: transparent;
        border-color: #ff69b4;
        color: #ff69b4;
        box-shadow: 0 0 20px #ff69b4;
        transform: translateY(-3px);
    }
</style>

</head>
<body>

<div class="card">
    <div class="icon">❌</div>
    <h1>Pago Fallido</h1>
    <p>Tu pago no pudo procesarse. Intenta nuevamente o usa otro método de pago.</p>

    <a href="checkout.php" class="btn">Volver al Pago</a>
    <a href="index.php" class="btn">Volver a la Página Principal</a>
</div>

</body>
</html>
