<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

$id_usuario = $_SESSION['usuario'];

// Buscar el carrito actual
$sql = "SELECT id_carrito FROM carrito WHERE id_usuario = ? ORDER BY id_carrito DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "
    <div style='
        text-align:center;
        margin-top:100px;
        font-family:Poppins,sans-serif;
        color:#fff;
        background-color:#111;
        height:100vh;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;'>
        <h2>🛒 Tu carrito está vacío</h2>
        <a href='tienda.php' style='
            display:inline-block;
            background-color:#ff4081;
            color:white;
            padding:12px 25px;
            border-radius:8px;
            text-decoration:none;
            margin-top:20px;
            transition:0.3s;'>⬅ Regresar a la tienda</a>
    </div>";
    exit();
}

$id_carrito = $result->fetch_assoc()['id_carrito'];

// Consultar productos
$sql_detalle = "
    SELECT d.id_producto, p.nombre_producto, p.precio_producto, d.cantidad, p.imagen 
    FROM detalle_carrito d
    INNER JOIN productos p ON d.id_producto = p.id_producto
    WHERE d.id_carrito = ?
";
$stmt = $conn->prepare($sql_detalle);
$stmt->bind_param("i", $id_carrito);
$stmt->execute();
$productos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛍 Tu carrito</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #111;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .carrito-container {
            width: 90%;
            max-width: 1000px;
            margin: 60px auto;
            background: #1a1a1a;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0px 0px 25px rgba(255, 64, 129, 0.3);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h2 {
            text-align: center;
            color: #ff4081;
            margin-bottom: 30px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background-color: #ff4081;
            color: white;
            text-transform: uppercase;
            padding: 12px;
            font-weight: 500;
        }

        td {
            background-color: #222;
            text-align: center;
            padding: 15px;
            border-bottom: 1px solid #333;
        }

        tr:hover td {
            background-color: #2c2c2c;
        }

        img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }

        button {
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            cursor: pointer;
            transition: 0.3s ease;
            font-weight: 600;
        }

        .mas, .menos {
            background-color: #ff4081;
            color: white;
        }

        .mas:hover, .menos:hover {
            background-color: #ff1f75;
        }

        .eliminar {
            background-color: #ff1744;
            color: white;
        }

        .eliminar:hover {
            background-color: #e00032;
        }

        .total {
            text-align: right;
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 25px;
            color: #ff4081;
        }

        .botones-final {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 35px;
        }

        .btn-volver, .btn-pagar {
            display: inline-block;
            padding: 14px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-volver {
            background-color: #333;
            color: white;
        }

        .btn-volver:hover {
            background-color: #555;
            transform: scale(1.05);
        }

        .btn-pagar {
            background-color: #ff4081;
            color: white;
            box-shadow: 0 0 15px rgba(255, 64, 129, 0.4);
        }

        .btn-pagar:hover {
            background-color: #ff1f75;
            transform: scale(1.07);
        }

        .acciones button {
            margin: 5px;
        }

        .cantidad span {
            margin: 0 8px;
            font-weight: bold;
        }

        .thumb {
          width: 60px;
          height: 60px;
          object-fit: cover;
          border: 2px solid #ff33cc;
          border-radius: 8px;
          box-shadow: 0 0 8px #ff33cc;
        }

    </style>
</head>
<body>

<div class="carrito-container">
    <h2>🛒 Tu carrito de compras</h2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Imagen</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="carrito-body">
            <?php
            $total = 0;
            while ($row = $productos->fetch_assoc()) {
                $subtotal = $row['precio_producto'] * $row['cantidad'];
                $total += $subtotal;
                echo "
                <tr data-id='{$row['id_producto']}'>
                    <td>{$row['nombre_producto']}</td>
                    <td><img src='uploads/{$row['imagen']}' alt='{$row['nombre_producto']}' class='thumb'></td>
                    <td>$" . number_format($row['precio_producto'], 0, ',', '.') . "</td>


                    <td class='cantidad'>
                        <button class='menos'>−</button>
                        <span>{$row['cantidad']}</span>
                        <button class='mas'>+</button>
                    </td>
                    <td>$" . number_format($subtotal, 0, ',', '.') . "</td>
                    <td class='acciones'>
                        <button class='eliminar'>🗑️</button>
                    </td>
                </tr>
                ";
            }
            ?>
        </tbody>
    </table>

    <div class="total">💰 Total: $<?php echo number_format($total, 0, ',', '.'); ?></div>

    <div class="botones-final">
        <a href="tienda.php" class="btn-volver">⬅ Regresar a la tienda</a>
        <a href="checkout.php" class="btn-pagar">💳 Finalizar compra</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    // Aumentar cantidad
    $('.mas').click(function() {
        const fila = $(this).closest('tr');
        const idProducto = fila.data('id');
        $.post('actualizar_carrito.php', {accion: 'sumar', id_producto: idProducto}, function() {
            location.reload();
        });
    });

    // Disminuir cantidad
    $('.menos').click(function() {
        const fila = $(this).closest('tr');
        const idProducto = fila.data('id');
        $.post('actualizar_carrito.php', {accion: 'restar', id_producto: idProducto}, function() {
            location.reload();
        });
    });

    // Eliminar producto
    $('.eliminar').click(function() {
        const fila = $(this).closest('tr');
        const idProducto = fila.data('id');
        if (confirm('¿Seguro que deseas eliminar este producto?')) {
            $.post('actualizar_carrito.php', {accion: 'eliminar', id_producto: idProducto}, function() {
                location.reload();
            });
        }
    });

});
</script>

</body>
</html>
