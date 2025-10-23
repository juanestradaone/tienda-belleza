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

//--------------------------------- HTML Y CSS MEJORADOS ---------------------------------//
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
            background: linear-gradient(135deg, #ffeef8 0%, #fff5f9 50%, #ffe8f5 100%);
            min-height: 100vh;
        }

        /* HEADER MEJORADO */
        header {
            background: linear-gradient(135deg, #ff69b4 0%, #ff1493 100%);
            color: white;
            padding: 1.5rem 2rem;
            box-shadow: 0 8px 32px rgba(255, 20, 147, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .logo h1 {
            font-size: 2rem;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.2);
            letter-spacing: 1px;
            animation: brillo 2s ease-in-out infinite;
        }

        @keyframes brillo {
            0%, 100% { text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.2); }
            50% { text-shadow: 3px 3px 15px rgba(255, 255, 255, 0.5), 0 0 20px rgba(255, 255, 255, 0.3); }
        }

        .logo p {
            font-size: 0.95rem;
            margin-top: 0.5rem;
            opacity: 0.95;
        }

        .menu {
            display: flex;
            gap: 1.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .menu a {
            color: white;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 600;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .menu a:hover {
            background: white;
            color: #ff1493;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .carrito-btn {
            position: relative;
        }

        .contador-carrito {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff0080;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(255, 0, 128, 0.5);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* HERO SECTION MEJORADO */
        .hero {
            text-align: center;
            padding: 3rem 2rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 182, 193, 0.3));
            border-radius: 30px;
            margin: 2rem;
            box-shadow: 0 10px 40px rgba(255, 105, 180, 0.2);
            backdrop-filter: blur(10px);
        }

        .hero h2 {
            font-size: 2.5rem;
            color: #ff1493;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(255, 20, 147, 0.2);
        }

        .hero p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
        }

        /* FILTROS MEJORADOS */
        .categorias-filtro {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .filtro-btn {
            padding: 0.9rem 1.8rem;
            border: 3px solid #ff69b4;
            background: white;
            color: #ff1493;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 105, 180, 0.2);
        }

        .filtro-btn:hover {
            background: #ff69b4;
            color: white;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(255, 105, 180, 0.4);
        }

        .filtro-btn.active {
            background: linear-gradient(135deg, #ff69b4, #ff1493);
            color: white;
            box-shadow: 0 6px 20px rgba(255, 20, 147, 0.4);
            transform: scale(1.05);
        }

        /* PRODUCTOS GRID MEJORADO */
        .contenedor-productos {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2.5rem;
            padding: 1rem;
        }

        /* TARJETAS DE PRODUCTO MEJORADAS */
        .producto-card {
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(255, 105, 180, 0.15);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .producto-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 20px 60px rgba(255, 105, 180, 0.3);
        }

        .etiqueta-nuevo {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #ff0080, #ff69b4);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            z-index: 10;
            box-shadow: 0 4px 15px rgba(255, 0, 128, 0.4);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .producto-imagen {
            width: 100%;
            height: 300px;
            background: linear-gradient(135deg, #ffe8f5, #fff0f8);
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .producto-imagen::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .producto-imagen img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .producto-card:hover .producto-imagen img {
            transform: scale(1.15) rotate(2deg);
        }

        .producto-info {
            padding: 1.5rem;
        }

        .categoria-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #ffe8f5, #ffd0e8);
            color: #ff1493;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .producto-info h3 {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 0.8rem;
            font-weight: 700;
        }

        .producto-descripcion {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.2rem;
            height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .precio-seccion {
            margin: 1.5rem 0;
        }

        .precio-actual {
            font-size: 2rem;
            font-weight: 800;
            color: #ff1493;
            text-shadow: 2px 2px 4px rgba(255, 20, 147, 0.1);
        }

        /* BOTÓN AGREGAR MEJORADO */
        .form-agregar {
            width: 100%;
        }

        .btn-agregar {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #ff69b4, #ff1493);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(255, 20, 147, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-agregar:hover {
            background: linear-gradient(135deg, #ff1493, #ff0080);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 20, 147, 0.5);
        }

        .btn-agregar:active {
            transform: translateY(0);
        }

        /* NOTIFICACIÓN MEJORADA */
        .notification {
            position: fixed;
            top: 100px;
            right: 30px;
            background: linear-gradient(135deg, #00ff88, #00cc6a);
            color: white;
            padding: 1.2rem 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 255, 136, 0.4);
            z-index: 2000;
            animation: slideIn 0.5s ease, slideOut 0.5s ease 1.5s;
            font-weight: 600;
            font-size: 1.1rem;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        /* SIN PRODUCTOS */
        .sin-productos {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 182, 193, 0.2));
            border-radius: 25px;
            box-shadow: 0 10px 40px rgba(255, 105, 180, 0.15);
        }

        .sin-productos h3 {
            font-size: 2rem;
            color: #ff1493;
            margin-bottom: 1rem;
        }

        .sin-productos p {
            font-size: 1.1rem;
            color: #666;
        }

        /* FOOTER MEJORADO */
        footer {
            background: linear-gradient(135deg, #ff69b4, #ff1493);
            color: white;
            text-align: center;
            padding: 3rem 2rem;
            margin-top: 4rem;
            box-shadow: 0 -8px 32px rgba(255, 20, 147, 0.3);
        }

        .footer-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        footer p {
            margin: 0.5rem 0;
            opacity: 0.95;
        }

        .redes-sociales {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 1.5rem;
        }

        .redes-sociales a {
            color: white;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .redes-sociales a:hover {
            background: white;
            color: #ff1493;
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .hero h2 {
                font-size: 1.8rem;
            }

            .productos-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1.5rem;
            }

            .menu {
                justify-content: center;
            }

            .categorias-filtro {
                gap: 0.5rem;
            }

            .filtro-btn {
                padding: 0.7rem 1.2rem;
                font-size: 0.9rem;
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
            
            // Crear notificación mejorada
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.textContent = '✅ ¡Producto agregado al carrito exitosamente!';
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

    // Animación de entrada para las tarjetas
    window.addEventListener('load', () => {
        const cards = document.querySelectorAll('.producto-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });
</script>

</body>
</html>