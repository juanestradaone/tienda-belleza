<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda | Belleza y Glamour Angelita</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1b1b1b 50%, #2b2b2b 100%);
            color: #f5f5f5;
            min-height: 100vh;
            line-height: 1.6;
        }

        header {
            background: linear-gradient(135deg, #111, #222, #111);
            border-bottom: 2px solid #ff1493;
            padding: 1.2rem 1.8rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .topbar h1 {
            font-size: 1.35rem;
            background: linear-gradient(135deg, #ff1493, #ff69b4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .acciones a {
            text-decoration: none;
            color: #fff;
            background: linear-gradient(135deg, #ff1493, #ff69b4);
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            margin-left: 0.5rem;
            display: inline-block;
        }

        .contenedor {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1rem 2rem;
        }

        .intro {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 105, 180, 0.35);
            border-radius: 14px;
            padding: 1.4rem;
            margin-bottom: 1.5rem;
        }

        .intro h2 { color: #ff69b4; margin-bottom: 0.6rem; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }

        section.bloque {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 105, 180, 0.25);
            border-radius: 12px;
            padding: 1.2rem;
        }

        section.bloque h2 {
            font-size: 1.15rem;
            color: #ff69b4;
            margin-bottom: 0.8rem;
        }

        ul { list-style: none; }
        li { margin-bottom: 0.8rem; color: #ddd; }

        .nota {
            margin-top: 1.1rem;
            color: #f2c1de;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
<header>
    <div class="topbar">
        <h1>❓ Centro de Ayuda</h1>
        <div class="acciones">
            <a href="inicio.php">🏠 Inicio</a>
            <a href="tienda.php">🛍️ Tienda</a>
        </div>
    </div>
</header>

<main class="contenedor">
    <section class="intro">
        <h2>Estamos para ayudarte</h2>
        <p>En esta sección encontrarás respuestas rápidas, guías sencillas y todos los canales de soporte para resolver tus dudas sobre compras, envíos, devoluciones y pagos.</p>
    </section>

    <div class="grid">
        <section class="bloque" aria-labelledby="faq-title">
            <h2 id="faq-title">Preguntas frecuentes (FAQ)</h2>
            <ul>
                <li><strong>¿Cómo realizo una compra?</strong><br>Elige tus productos, agrégalos al carrito, confirma dirección y método de pago, y finaliza el pedido.</li>
                <li><strong>¿Puedo devolver un producto?</strong><br>Sí. Puedes solicitar devolución dentro de los primeros 5 días hábiles, con el producto en buen estado y empaque original.</li>
                <li><strong>¿Cuánto tarda el envío?</strong><br>El tiempo estimado es de 2 a 5 días hábiles, según tu ciudad.</li>
                <li><strong>¿Qué métodos de pago aceptan?</strong><br>Pago contra entrega, transferencia bancaria y opciones habilitadas durante checkout.</li>
            </ul>
        </section>

        <section class="bloque" aria-labelledby="canales-title">
            <h2 id="canales-title">Canales de soporte</h2>
            <ul>
                <li><strong>Chat en vivo:</strong> Lunes a sábado, de 9:00 a.m. a 7:00 p.m.</li>
                <li><strong>WhatsApp:</strong> +57 311 620 8892 para atención rápida.</li>
                <li><strong>Correo:</strong> soporte@bellezayglamourangelita.com</li>
                <li><strong>Instagram y Facebook:</strong> Atención por mensaje directo.</li>
            </ul>
        </section>

        <section class="bloque" aria-labelledby="reco-title">
            <h2 id="reco-title">Antes de contactarnos</h2>
            <ul>
                <li>Ten tu número de pedido para agilizar la atención.</li>
                <li>Indica nombre del producto y detalle de la consulta.</li>
                <li>Si reportas novedades con envío, incluye foto y fecha de recepción.</li>
            </ul>
            <p class="nota">✨ Queremos que tu experiencia de compra sea fácil, clara y confiable.</p>
        </section>
    </div>
</main>
</body>
</html>
