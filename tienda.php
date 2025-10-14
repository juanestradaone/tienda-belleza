<?php
session_start();
include("conexion.php");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.html");
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
        <button class="filtro-btn active" onclick="filtrarProductos('todos')">Todos</button>
        <button class="filtro-btn" onclick="filtrarProductos('maquillaje')">💄 Maquillaje</button>
        <button class="filtro-btn" onclick="filtrarProductos('facial')">🧴 Facial</button>
                <button class="filtro-btn" onclick="filtrarProductos('corporal')"> 💆🏻‍♀️ corporal</button>
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
                    
                    <div class="etiqueta-nuevo">Nuevo</div>
                    
                    <div class="producto-imagen">
                        <img src="imagenes/producto_default.jpg" alt="<?php echo htmlspecialchars($row['nombre_producto']); ?>" loading="lazy">
                    </div>

                    <div class="producto-info">
                        <span class="categoria-badge"><?php echo htmlspecialchars($row['categoria_producto']); ?></span>
                        
                        <h3><?php echo htmlspecialchars($row['nombre_producto']); ?></h3>
                        
                        <p class="producto-descripcion"><?php echo htmlspecialchars($row['descripcion']); ?></p>

                        <div class="precio-seccion">
                            <span class="precio-actual">$<?php echo number_format($row['precio_producto'], 0, ',', '.'); ?></span>
                        </div>

                        <form method="POST" action="carrito.php" class="form-agregar">
                            <input type="hidden" name="id_producto" value="<?php echo $row['id_producto']; ?>">
                            <button type="submit" class="btn-agregar">Agregar al Carrito</button>
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
<<footer>
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

        // Filtrar productos
        productos.forEach(producto => {
            if (categoria === 'todos' || producto.dataset.categoria.toLowerCase().includes(categoria.toLowerCase())) {
                producto.style.display = 'flex';
                setTimeout(() => {
                    producto.style.opacity = '1';
                }, 10);
            } else {
                producto.style.display = 'none';
            }
        });
    }

    // Mostrar notificación cuando se agrega al carrito
    document.querySelectorAll('.form-agregar').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Crear notificación
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.textContent = '✅ ¡Producto agregado al carrito!';
            document.body.appendChild(notification);

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

    // Actualizar contador del carrito
    function actualizarContador() {
        const contador = document.querySelector('.contador-carrito');
        // Aquí puedes hacer una petición AJAX para obtener la cantidad de items del carrito
        contador.textContent = '0'; // Valor por defecto
    }

    actualizarContador();
</script>

</body>
</html>