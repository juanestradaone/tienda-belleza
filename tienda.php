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
// Cambio desde el nuevo equipo
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda | Belleza y Glamour Angelita</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #ffd1dc, #fff0f5);
            margin: 0;
            padding: 0;
        }

        /* 🔝 ENCABEZADO */
        header {
            background-color: #000;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header .logo h1 {
            color: #ff00cc;
            font-size: 22px;
            margin: 0;
        }

        header .menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        header .menu a {
            color: #ffccf9;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s;
        }

        header .menu a:hover {
            color: #fff;
        }

        /* 🛒 Icono del carrito */
        header .carrito {
            font-size: 22px;
            background-color: #ff00cc;
            color: #fff !important;
            border-radius: 50%;
            padding: 6px 10px;
            transition: background 0.3s;
        }

        header .carrito:hover {
            background-color: #e600b3;
        }

        /* 🛍️ Título */
        h2 {
            text-align: center;
            color: #ff00cc;
            margin-top: 30px;
            font-size: 28px;
        }

        /* 🧴 Cuadrícula de productos */
        .productos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 40px;
            max-width: 1200px;
            margin: auto;
        }

        .producto {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
            padding: 15px;
            transition: transform 0.3s;
        }

        .producto:hover {
            transform: translateY(-5px);
        }

        .producto img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .producto h3 {
            color: #ff00cc;
            margin: 10px 0 5px;
        }

        .producto p {
            color: #333;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .precio {
            color: #e600b3;
            font-weight: bold;
            font-size: 16px;
        }

        .categoria {
            color: #999;
            font-size: 13px;
            margin-top: 5px;
        }

        .btn {
            display: inline-block;
            background-color: #ff00cc;
            color: #fff;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 8px;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #e600b3;
        }

        .sin-productos {
            text-align: center;
            color: #777;
            font-size: 18px;
            margin-top: 50px;
        }

        footer {
            background-color: #000;
            color: #ffccf9;
            text-align: center;
            padding: 15px 0;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <h1>✨ Belleza y Glamour Angelita</h1>
    </div>
    <nav class="menu">
        <a href="dashboard.php">Inicio</a>
        <a href="tienda.php">Productos</a>
        <a href="carrito.php" class="carrito">🛒</a>
        <a href="logout.php">Salir</a>
    </nav>
</header>

<h2>🛍️ Nuestros Productos</h2>

<div class="productos">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="producto">
                <!-- Imagen por defecto -->
                <img src="imagenes/producto_default.jpg" alt="<?php echo htmlspecialchars($row['nombre_producto']); ?>">

                <h3><?php echo htmlspecialchars($row['nombre_producto']); ?></h3>
                <p class="categoria">Categoría: <?php echo htmlspecialchars($row['categoria_producto']); ?></p>
                <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                <p class="precio">$<?php echo number_format($row['precio_producto'], 0, ',', '.'); ?></p>
                <p><strong>Disponibles:</strong> <?php echo htmlspecialchars($row['cantidad_disponible']); ?></p>

                <form method="POST" action="carrito.php">
                    <input type="hidden" name="id_producto" value="<?php echo $row['id_producto']; ?>">
                    <button class="btn" type="submit">Agregar al carrito</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="sin-productos">
            <p>🚫 No hay productos disponibles en este momento.</p>
        </div>
    <?php endif; ?>
</div>

<footer>
    <p>© 2025 Belleza y Glamour Angelita. Todos los derechos reservados.</p>
</footer>

</body>
</html>
