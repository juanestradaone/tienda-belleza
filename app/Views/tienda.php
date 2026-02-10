<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

// Verificar si el usuario ha iniciado sesión
//if (!isset($_SESSION['usuario'])) {
    //header("Location: index.php");
    //exit();
//}

// Consultar productos activos
$sql = "SELECT * FROM productos WHERE activo = 1 ORDER BY id_producto DESC";
$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta SQL: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda | Belleza y Glamour Angelita</title>
    <link rel="stylesheet" href="style.css">
   <style>
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #0a0a0a 0%, #1b1b1b 50%, #2b2b2b 100%);
        color: #f5f5f5;
        min-height: 100vh;
        font-size: 18px;
    }

    /* HEADER OSCURO */
    header {
        background: linear-gradient(135deg, #111, #222, #111);
        color: #fff;
        padding: 1.5rem 2rem;
        box-shadow: 0 4px 20px rgba(255, 20, 147, 0.25);
        position: sticky;
        top: 0;
        z-index: 1000;
        backdrop-filter: blur(10px);
        border-bottom: 2px solid #ff1493;
    }

    /* TÍTULO DEL HEADER */
.logo h1 {
    font-size: 2rem;
    letter-spacing: 2px;
    background: linear-gradient(135deg, #ff1493, #ff69b4, #ff1493);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 0 20px rgba(255, 20, 147, 0.6), 
                 0 0 40px rgba(255, 105, 180, 0.4),
                 0 0 60px rgba(255, 20, 147, 0.2);
    animation: glow 3s infinite alternate;
    font-weight: 900;
    text-transform: uppercase;
    filter: drop-shadow(0 0 15px #ff1493);
}

@keyframes glow {
    from { 
        filter: drop-shadow(0 0 10px #ff1493) drop-shadow(0 0 20px #ff69b4);
    }
    to { 
        filter: drop-shadow(0 0 25px #ff69b4) drop-shadow(0 0 40px #ff1493);
    }
}
    .menu {
        display: flex;
        gap: 1.2rem;
        margin-top: 1rem;
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .menu a {
        color: #fff;
        text-decoration: none;
        padding: 0.8rem 1.5rem;
        background: linear-gradient(135deg, #ff1493, #ff69b4);
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        height: 3.2rem;
    }

    .menu a:hover {
        background: transparent;
        border-color: #ff69b4;
        color: #ff69b4;
        transform: translateY(-3px);
        box-shadow: 0 0 15px #ff69b4;
    }

    .contador-carrito {
        background: rgba(255, 255, 255, 0.4);
        padding: 0.1rem 0.4rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: bold;
        min-width: 18px;
        text-align: center;
        line-height: 1;
    }

    /* HERO */
    .hero {
        text-align: center;
        padding: 3rem 2rem;
        background: rgba(25, 25, 25, 0.9);
        border-radius: 20px;
        margin: 2rem;
        box-shadow: 0 0 25px rgba(255, 20, 147, 0.2);
    }

    .hero h2 {
        font-size: 2.5rem;
        color: #ff69b4;
        margin-bottom: 1rem;
        text-shadow: 0 0 10px #ff69b4;
    }

    .hero p {
        font-size: 1.1rem;
        color: #ccc;
    }

    /* FILTROS */
    .categorias-filtro {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 2rem;
    }

    .filtro-btn {
        padding: 0.9rem 1.8rem;
        border: 2px solid #ff69b4;
        background: transparent;
        color: #ff69b4;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .filtro-btn:hover, .filtro-btn.active {
        background: linear-gradient(135deg, #ff1493, #ff69b4);
        color: #fff;
        box-shadow: 0 0 20px #ff69b4;
        transform: scale(1.05);
    }
  
/* Contenedor del buscador */
.search-container {
    position: relative;
    width: 260px;
    transition: width 0.3s ease;
}

/* Animación al enfocarse */
.search-container.active {
    width: 350px;
}

/* Input del buscador */
.search-input {
    width: 100%;
    padding: 10px 40px 10px 15px;
    border: 2px solid #ff0099;
    background: #0d0d0d;
    border-radius: 30px;
    color: #fff;
    font-size: 15px;
    outline: none;
    box-shadow: 0 0 10px #ff0099aa;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}

/* Glow al activar */
.search-container.active .search-input {
    transform: scale(1.05);
    box-shadow: 0 0 15px #ff0099ee;
}

/* Botón limpiar ❌ */
.clear-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    font-size: 18px;
    color: #ff66c4;
    cursor: pointer;
    display: none;
}

/* Result Box */
.no-results {
    margin-top: 10px;
    color: #ff66c4;
    font-size: 14px;
    display: none;
    text-shadow: 0 0 5px #ff0099;
}
.highlight {
    background-color: #ff0099;
    color: #fff;
    padding: 2px 4px;
    border-radius: 4px;
}



    /* PRODUCTOS */
    .contenedor-productos {
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .productos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2.5rem;
    }

    .producto-card {
        background: #111;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 0 25px rgba(255, 20, 147, 0.1);
        transition: all 0.4s ease;
    }

    .producto-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 0 35px rgba(255, 20, 147, 0.3);
    }

    .producto-imagen img {
        width: auto;
        height: 280px;
        object-fit: cover;
        transition: all 0.5s ease;
    }

    .producto-card:hover .producto-imagen img {
        transform: scale(1.1);
    }

    .producto-info {
        padding: 1.5rem;
    }

    .producto-info h3 {
        color: #fff;
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
    }

    .producto-descripcion {
        color: #bbb;
        font-size: 0.95rem;
        margin-bottom: 1.2rem;
    }

    .precio-actual {
        color: #ff69b4;
        font-size: 1.8rem;
        font-weight: bold;
        text-shadow: 0 0 10px rgba(255, 105, 180, 0.4);
    }

    .btn-agregar {
        width: 100%;
        padding: 0.9rem;
        background: linear-gradient(135deg, #ff1493, #ff69b4);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-agregar:hover {
        background: linear-gradient(135deg, #ff69b4, #ff1493);
        box-shadow: 0 0 20px #ff69b4;
        transform: translateY(-3px);
    }



    /* SECCIÓN DE AYUDA */
    .seccion-ayuda {
        max-width: 1200px;
        margin: 1rem auto 3rem;
        padding: 2rem;
        background: rgba(17, 17, 17, 0.95);
        border: 1px solid rgba(255, 105, 180, 0.4);
        border-radius: 18px;
        box-shadow: 0 0 20px rgba(255, 20, 147, 0.2);
    }

    .ayuda-header h2 {
        color: #ff69b4;
        margin-bottom: 0.8rem;
        text-shadow: 0 0 10px rgba(255, 105, 180, 0.5);
    }

    .ayuda-header p {
        color: #ddd;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .ayuda-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
    }

    .ayuda-bloque {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 105, 180, 0.2);
        border-radius: 12px;
        padding: 1.2rem;
    }

    .ayuda-bloque h3 {
        color: #fff;
        margin-bottom: 0.8rem;
    }

    .ayuda-bloque ul {
        list-style: none;
        padding: 0;
    }

    .ayuda-bloque li {
        color: #cfcfcf;
        margin-bottom: 0.9rem;
        line-height: 1.5;
    }

    .ayuda-enlace {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 1rem;
        color: #fff;
        text-decoration: none;
        padding: 0.7rem 1.2rem;
        background: linear-gradient(135deg, #ff1493, #ff69b4);
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .ayuda-enlace:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 15px rgba(255, 105, 180, 0.7);
    }

    /* FOOTER */
    footer {
        background: #111;
        color: #fff;
        text-align: center;
        padding: 3rem 2rem;
        border-top: 2px solid #ff1493;
        box-shadow: 0 -4px 20px rgba(255, 20, 147, 0.3);
    }

    /* TÍTULO DEL FOOTER */
    .footer-title {
       font-size: 1.8rem;
       letter-spacing: 2px;
       background: linear-gradient(135deg, #ff1493, #ff69b4, #ff1493);
       -webkit-background-clip: text;
       -webkit-text-fill-color: transparent;
       background-clip: text;
       margin-bottom: 1rem;
       font-weight: 900;
       text-transform: uppercase;
       filter: drop-shadow(0 0 15px #ff1493) drop-shadow(0 0 25px #ff69b4);
       animation: glowFooter 4s infinite alternate;
    }

  @keyframes glowFooter {
    from { 
        filter: drop-shadow(0 0 10px #ff1493) drop-shadow(0 0 20px #ff69b4);
        opacity: 0.9;
    }
    to { 
        filter: drop-shadow(0 0 20px #ff69b4) drop-shadow(0 0 35px #ff1493);
        opacity: 1;
    }
}

    .redes-sociales a {
        color: #fff;
        text-decoration: none;
        margin: 0 1rem;
        transition: all 0.3s ease;
    }

    .redes-sociales a:hover {
        color: #ff69b4;
        text-shadow: 0 0 10px #ff69b4;
    }
    /* Ajustes para iconos SVG en footer */
    .redes-sociales {
        display: flex;
        justify-content: center;
        gap: 0.8rem;
        align-items: center;
        margin-top: 1rem;
    }
    .redes-sociales a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: transparent;
        margin: 0 0.4rem;
    }
    .redes-sociales a svg { width:22px; height:22px; display:block; }
    .sr-only { position: absolute !important; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .productos-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }

        .hero h2 {
            font-size: 1.8rem;
        }
    }
    /* NOTIFICACIÓN DE PRODUCTO AGREGADO */
    .notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: rgba(255, 20, 147, 0.9);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 15px;
    font-size: 1rem;
    font-weight: bold;
    box-shadow: 0 0 20px #ff69b4;
    z-index: 3000;
    opacity: 0;
    transform: translateY(-20px);
    animation: notiIn 0.4s forwards, notiOut 0.4s forwards 1.8s;
    }

    /* Animación de entrada */
    @keyframes notiIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
    }

    /* Animación de salida */
    @keyframes notiOut {
    to {
        opacity: 0;
        transform: translateY(-20px);
    }
    }

</style>

</head>
<body>

<!-- HEADER -->
<header>
    <div class="logo">
        <h1>✨ BELLEZA Y GLAMOUR ANGELITA</h1>
        <p>Tu lugar de confianza para realizar tu belleza con productos de calidad</p>
    </div>
    <nav class="menu">
        <a href="inicio.php">📍 Inicio</a>
        <a href="tienda.php">🛍️ Productos</a>
        <a href="historial_pedidos.php"> Historial de pedidos</a>
        <a href="#seccion-ayuda">❓ Ayuda</a>
        <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
            <a href="admin_pedidos.php">🧭 Admin pedidos</a>
        <?php endif; ?>
        <a href="carrito.php" class="carrito-btn">
            🛒 Carrito
            <span class="contador-carrito">0</span>
        </a>
    </nav>
</header>

<!-- HERO SECTION -->
<section class="hero">
    <h2>✨ Nuestros Productos</h2>
    <p>Descubre nuestra colección exclusiva de belleza y cuidado personal</p>
    
    <div class="categorias-filtro">
        <button class="filtro-btn active" onclick="filtrarProductos('todos')">✨ Todos</button>
        <button class="filtro-btn" onclick="filtrarProductos('maquillaje')">💄 Maquillaje</button>
        <button class="filtro-btn" onclick="filtrarProductos('facial')">🧴 Facial</button>
        <button class="filtro-btn" onclick="filtrarProductos('corporal')">💆🏻‍♀️ Corporal</button>
        <button class="filtro-btn" onclick="filtrarProductos('uñas')">💅 Uñas</button>
        <button class="filtro-btn" onclick="filtrarProductos('cabello')">💇‍♀️ Cabello</button>
        
    <div class="search-container" id="searchBox">
    <input type="text" id="buscador" class="search-input" placeholder="🔍Buscar productos...">
    <button id="clearBtn" class="clear-btn">❌</button>
</div>

<p id="noResults" class="no-results">No se encontraron resultados</p>


<!-- PRODUCTOS -->
<div class="contenedor-productos">
    <div class="productos-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="producto-card" data-categoria="<?php echo htmlspecialchars($row['categoria_producto']); ?>">
                    
                    <div class="etiqueta-nuevo">✨ Nuevo</div>
                    
                    <?php
                        $imagen = !empty($row['imagen']) ? 'uploads/' . $row['imagen'] : 'imagenes/producto_default.jpg';
                    ?>
                    <div class="producto-imagen">
                        <img src="<?php echo htmlspecialchars($imagen); ?>" 
                             alt="<?php echo htmlspecialchars($row['nombre_producto']); ?>" 
                             loading="lazy">
                    </div>

                    <div class="producto-info">
                        <span class="categoria-badge"><?php echo htmlspecialchars($row['categoria_producto']); ?></span>
                        <h3><?php echo htmlspecialchars($row['nombre_producto']); ?></h3>
                        <p class="producto-descripcion"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                        <div class="precio-seccion">
                            <span class="precio-actual">$<?php echo number_format($row['precio_producto'], 0, ',', '.'); ?></span>
                        </div>
                        <form method="POST" action="agregar_carrito.php" class="form-agregar">
                            <input type="hidden" name="id_producto" value="<?php echo $row['id_producto']; ?>">
                            <input type="hidden" name="cantidad" value="1">
                            <button type="submit" class="btn-agregar">🛒 Agregar al Carrito</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="sin-productos">
                <h3>🚫 Sin Productos Disponibles</h3>
                <p>Regresa más tarde, estaremos actualizando nuestro catálogo</p>
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- SECCIÓN DE AYUDA -->
<section class="seccion-ayuda" id="seccion-ayuda">
    <div class="ayuda-header">
        <h2>❓ Centro de Ayuda</h2>
        <p>Estamos aquí para acompañarte durante toda tu compra. En esta sección encontrarás respuestas rápidas, pasos sencillos y canales de soporte para que tu experiencia sea segura, fácil y agradable.</p>
        <a class="ayuda-enlace" href="#seccion-ayuda" aria-label="Ir a la sección de ayuda">💬 Ir a Ayuda</a>
    </div>

    <div class="ayuda-grid">
        <section class="ayuda-bloque" aria-labelledby="faq-title">
            <h3 id="faq-title">Preguntas frecuentes (FAQ)</h3>
            <ul>
                <li><strong>¿Cómo realizo una compra?</strong><br>Elige tus productos, agrégalos al carrito, confirma tu dirección y método de pago en checkout, y finaliza el pedido.</li>
                <li><strong>¿Puedo devolver un producto?</strong><br>Sí, aceptamos devoluciones dentro de los primeros 5 días hábiles, siempre que el producto esté en buen estado y con su empaque.</li>
                <li><strong>¿Cuánto tarda el envío?</strong><br>Los envíos nacionales suelen tardar entre 2 y 5 días hábiles, según la ciudad de destino.</li>
                <li><strong>¿Qué métodos de pago aceptan?</strong><br>Pago contra entrega, transferencias bancarias y pasarelas habilitadas durante el proceso de compra.</li>
            </ul>
        </section>

        <section class="ayuda-bloque" aria-labelledby="soporte-title">
            <h3 id="soporte-title">Canales de soporte</h3>
            <ul>
                <li><strong>Chat en vivo:</strong> Disponible de lunes a sábado, de 9:00 a.m. a 7:00 p.m.</li>
                <li><strong>WhatsApp:</strong> +57 311 620 8892 para consultas rápidas sobre pedidos y productos.</li>
                <li><strong>Correo electrónico:</strong> soporte@bellezayglamourangelita.com para solicitudes detalladas.</li>
                <li><strong>Instagram y Facebook:</strong> Escríbenos por mensaje directo y te responderemos lo antes posible.</li>
            </ul>
        </section>

        <section class="ayuda-bloque" aria-labelledby="recomendaciones-title">
            <h3 id="recomendaciones-title">Recomendaciones antes de contactarnos</h3>
            <ul>
                <li>Ten a mano tu número de pedido para darte una atención más rápida.</li>
                <li>Indica el nombre del producto y el detalle de tu consulta.</li>
                <li>Si reportas una novedad con el envío, comparte una foto y fecha de recepción.</li>
            </ul>
            <p>Queremos que tu experiencia sea excelente de principio a fin. ✨</p>
        </section>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <p class="footer-title">🌟 BELLEZA Y GLAMOUR ANGELITA</p>
    <p>© 2025 Todos los derechos reservados.</p>
    <p>Tu lugar de confianza para realizar tu belleza con productos de calidad</p>
    <p>Contáctanos - 311-620-8892</p>
    <div class="redes-sociales">
        <a href="https://www.facebook.com/profile.php?id=61570566590673&mibextid=ZbWKwL" target="_blank" aria-label="Facebook" title="Facebook">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
                <path fill="#1877F2" d="M22 12C22 6.48 17.52 2 12 2S2 6.48 2 12c0 4.99 3.66 9.12 8.44 9.88v-6.99H7.9v-2.89h2.54V9.41c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.25c-1.23 0-1.61.77-1.61 1.56v1.87h2.74l-.44 2.89h-2.3V21.88C18.34 21.12 22 16.99 22 12z"/>
            </svg>
            <span class="sr-only">Facebook</span>
        </a>
        <a href="https://www.instagram.com/" target="_blank" aria-label="Instagram" title="Instagram">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
                <defs>
                    <linearGradient id="igGrad2" x1="0" x2="1" y1="0" y2="1">
                        <stop offset="0%" stop-color="#feda75"/>
                        <stop offset="50%" stop-color="#d62976"/>
                        <stop offset="100%" stop-color="#962fbf"/>
                    </linearGradient>
                </defs>
                <path fill="url(#igGrad2)" d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm5 6.5A4.5 4.5 0 1 0 16.5 13 4.5 4.5 0 0 0 12 8.5zm5.5-.75a1.125 1.125 0 1 1-1.125 1.125A1.125 1.125 0 0 1 17.5 7.75z"/>
            </svg>
            <span class="sr-only">Instagram</span>
        </a>
        <a href="https://twitter.com/" target="_blank" aria-label="Twitter" title="Twitter">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
                <path fill="#1DA1F2" d="M22.46 6c-.77.35-1.6.58-2.46.69a4.26 4.26 0 0 0 1.86-2.35 8.53 8.53 0 0 1-2.7 1.03 4.24 4.24 0 0 0-7.23 3.87A12.01 12.01 0 0 1 3.15 4.6a4.24 4.24 0 0 0 1.31 5.66c-.67-.02-1.3-.21-1.85-.51v.05c0 2.02 1.44 3.7 3.36 4.08-.35.1-.72.15-1.1.15-.27 0-.53-.03-.78-.07.53 1.66 2.06 2.87 3.88 2.91A8.5 8.5 0 0 1 2 19.54a12.01 12.01 0 0 0 6.5 1.9c7.8 0 12.07-6.46 12.07-12.07 0-.18-.01-.36-.02-.54A8.7 8.7 0 0 0 22.46 6z"/>
            </svg>
            <span class="sr-only">Twitter</span>
        </a>
    </div>
</footer>

<script>
    // Función para filtrar productos por categoría
    function filtrarProductos(categoria) {
        const productos = document.querySelectorAll('.producto-card');
        const botones = document.querySelectorAll('.filtro-btn');

        // Actualizar botón activo
        botones.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        // Filtrar productos con animación
        productos.forEach((producto, index) => {
            if (categoria === 'todos' || producto.dataset.categoria.toLowerCase().includes(categoria.toLowerCase())) {
                producto.style.display = 'flex';
                setTimeout(() => {
                    producto.style.animation = 'fadeInUp 0.6s ease forwards';
                }, index * 50);
            } else {
                producto.style.display = 'none';
            }
        });
    }

    // Mostrar notificación cuando se agrega al carrito
    document.querySelectorAll('.form-agregar').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
          // 🔊 Reproducir sonido — con fallback para navegadores estrictos
           const sonido = document.getElementById('sonido-carrito');
            if (sonido) {
             sonido.currentTime = 0;
             const playPromise = sonido.play();

            if (playPromise !== undefined) {
                playPromise.catch(err => {
                    console.log("⚠ El navegador bloqueó el sonido, intentando desbloquear...");
                    // Reintento después de una mínima interacción
                    window.addEventListener('click', () => sonido.play(), { once: true });
                });
            }
        }
            // Crear notificación mejorada
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.textContent = '✅ ¡Producto agregado al carrito exitosamente!';
            document.body.appendChild(notification);

            actualizarContador();
            // Enviar formulario
            setTimeout(() => {
                this.submit();
            }, 300);

            // Eliminar notificación
            setTimeout(() => {
                notification.remove();
            }, 2000);
        });
    });
document.addEventListener('DOMContentLoaded', function() {
    // Obtener el contador del carrito
    actualizarContador();
    setInterval(actualizarContador, 5000);
});


    // Función para obtener la cantidad actual de productos en el carrito
    function actualizarContador() {
        fetch('obtener_carrito.php') // archivo que devolverá la cantidad
            .then(res => res.json())
            .then(data => {
                const contador = document.querySelector('.contador-carrito');
                contador.textContent = data.total_items ?? 0;
            })
            .catch(err => console.error('Error al actualizar el carrito:', err));
    }

    // Actualiza el contador al cargar la página
    actualizarContador();

    // También puedes actualizarlo cada pocos segundos si deseas que esté siempre al día
    setInterval(actualizarContador, 5000);


    
// ------- Animación al abrir -------
const searchInput = document.getElementById("buscador");
const searchContainer = document.getElementById("searchBox");
const clearBtn = document.getElementById("clearBtn");
const noResultsText = document.getElementById("noResults");

// Activar animación al enfocar
searchInput.addEventListener("focus", () => {
    searchContainer.classList.add("active");
});

// Quitar animación si el input queda vacío
searchInput.addEventListener("blur", () => {
    if (searchInput.value === "") {
        searchContainer.classList.remove("active");
    }
});

// ------- Botón ❌ limpiar -------
searchInput.addEventListener("input", () => {
    clearBtn.style.display = searchInput.value.length > 0 ? "block" : "none";
});

clearBtn.addEventListener("click", () => {
    searchInput.value = "";
    clearBtn.style.display = "none";
    noResultsText.style.display = "none";
    filterProducts(""); 
});

// ------- Filtro + mensaje "No se encontraron resultados" -------
function filterProducts(query) {
    const cards = document.querySelectorAll(".producto-card");
    let visibles = 0;

    cards.forEach(card => {
        const name = card.querySelector("h3").textContent.toLowerCase();

        if (name.includes(query.toLowerCase())) {
            card.style.display = "block";
            visibles++;
        } else {
            card.style.display = "none";
        }
    });

    // Mensaje de "No se encontraron resultados"
    noResultsText.style.display = (visibles === 0 && query !== "") ? "block" : "none";
}

// Filtrar a medida que se escribe
searchInput.addEventListener("input", () => {
    filterProducts(searchInput.value);
});

// ------- Filtro + mensaje "No se encontraron resultados" -------
function filterProducts(query) {
    const cards = document.querySelectorAll(".producto-card");
    let visibles = 0;

    cards.forEach(card => {
        const titleElement = card.querySelector("h3");
        const originalText = titleElement.dataset.original || titleElement.textContent;

        // Guardar texto original (solo una vez)
        if (!titleElement.dataset.original) {
            titleElement.dataset.original = originalText;
        }

        // Si el campo está vacío → mostrar todos y restaurar texto
        if (query.trim() === "") {
            titleElement.innerHTML = originalText;
            card.style.display = "block";
            visibles++;
            return;
        }

        const lowerOriginal = originalText.toLowerCase();
        const lowerQuery = query.toLowerCase();

        // Coincidencia
        if (lowerOriginal.includes(lowerQuery)) {
            card.style.display = "block";
            visibles++;

            // Resaltar coincidencias
            const highlighted = originalText.replace(
                new RegExp(query, "gi"),
                match => `<span class="highlight">${match}</span>`
            );

            titleElement.innerHTML = highlighted;
        } else {
            card.style.display = "none";
            titleElement.innerHTML = originalText;
        }
    });

    // Mostrar mensaje "No se encontraron resultados"
    noResultsText.style.display = (visibles === 0) ? "block" : "none";
}


</script>

<audio id="sonido-carrito" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/2354/2354-preview.mp3" type="audio/mpeg">
</audio>


</body>
</html>
