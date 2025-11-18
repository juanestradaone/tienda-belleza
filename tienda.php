<?php
session_start();
include("conexion.php");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

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

    .logo h1 {
        font-size: 2rem;
        letter-spacing: 1px;
        text-shadow: 0 0 10px #ff1493, 0 0 20px #ff69b4;
        animation: glow 3s infinite alternate;
    }

    @keyframes glow {
        from { text-shadow: 0 0 10px #ff1493; }
        to { text-shadow: 0 0 20px #ff69b4, 0 0 30px #ff1493; }
    }

    .menu {
        display: flex;
        gap: 1.2rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .menu a {
        color: #fff;
        text-decoration: none;
        padding: 0.8rem 1.5rem;
        background: linear-gradient(135deg, #ff1493, #ff69b4);
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .menu a:hover {
        background: transparent;
        border-color: #ff69b4;
        color: #ff69b4;
        transform: translateY(-3px);
        box-shadow: 0 0 15px #ff69b4;
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
        border-radius: 25px;
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
        width: 100%;
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
        border-radius: 15px;
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

    /* FOOTER */
    footer {
        background: #111;
        color: #fff;
        text-align: center;
        padding: 3rem 2rem;
        border-top: 2px solid #ff1493;
        box-shadow: 0 -4px 20px rgba(255, 20, 147, 0.3);
    }

    .footer-title {
        font-size: 1.8rem;
        color: #ff69b4;
        text-shadow: 0 0 10px #ff69b4;
        margin-bottom: 1rem;
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
        <a href="dashboard.php">📍 Inicio</a>
        <a href="tienda.php">🛍️ Productos</a>
        <a href="carrito.php" class="carrito-btn">
            🛒 Carrito
            <span class="contador-carrito">0</span>
        </a>
        <a href="logout.php">🚪 Salir</a>
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
        <button class="filtro-btn" onclick="filtrarProductos('unas')">💅 Uñas</button>
        <button class="filtro-btn" onclick="filtrarProductos('cabello')">💇‍♀️ Cabello</button>
    </div>
</section>

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

<!-- FOOTER -->
<footer>
    <p class="footer-title">🌟 BELLEZA Y GLAMOUR ANGELITA</p>
    <p>© 2025 Todos los derechos reservados.</p>
    <p>Tu lugar de confianza para realizar tu belleza con productos de calidad</p>
    <p>Contáctanos - 311-620-8892</p>
    <div class="redes-sociales">
        <a href="https://www.facebook.com/profile.php?id=61570566590673&mibextid=ZbWKwL" target="_blank">📘 Facebook</a>
        <a href="https://www.instagram.com/" target="_blank">📸 Instagram</a>
        <a href="https://twitter.com/" target="_blank">🐦 Twitter</a>
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

</script>

<audio id="sonido-carrito" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/2354/2354-preview.mp3" type="audio/mpeg">
</audio>


</body>
</html>
