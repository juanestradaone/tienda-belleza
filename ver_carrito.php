<?php
session_start();
include("conexion.php");

// ID del usuario actual (supongamos que ya inició sesión)
$id_usuario = $_SESSION['id_usuario'];

// 1️⃣ Buscar el carrito más reciente del usuario
$sql_carrito = "SELECT id_carrito FROM carrito WHERE id_usuario = ? ORDER BY id_carrito DESC LIMIT 1";
$stmt = $conexion->prepare($sql_carrito);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    echo "<h2>No tienes productos en tu carrito 🛒</h2>";
    exit;
}

$fila = $resultado->fetch_assoc();
$id_carrito = $fila['id_carrito'];

// 2️⃣ Traer los productos del detalle_carrito
$sql_detalle = "
SELECT p.nombre, p.precio, d.cantidad, (p.precio * d.cantidad) AS subtotal
FROM detalle_carrito d
INNER JOIN productos p ON d.id_producto = p.id_producto
WHERE d.id_carrito = ?
";

$stmt2 = $conexion->prepare($sql_detalle);
$stmt2->bind_param("i", $id_carrito);
$stmt2->execute();
$productos = $stmt2->get_result();

// 3️⃣ Mostrar en tabla
$total = 0;
?>
<h2>🛒 Tu carrito de compras</h2>
<form action="actualizar_carrito.php" method="POST">
<table border="1" cellpadding="10">
    <tr>
        <th>Producto</th>
        <th>Precio</th>
        <th>Cantidad</th>
        <th>Subtotal</th>
        <th>Acción</th>
    </tr>

    <?php 
    $total = 0;
    while ($fila = $productos->fetch_assoc()) { 
        $subtotal = $fila['subtotal'];
        $total += $subtotal;
    ?>
        <tr>
            <td><?php echo $fila['nombre']; ?></td>
            <td>$<?php echo number_format($fila['precio'], 0, ',', '.'); ?></td>
            <td>
                <input type="number" name="cantidades[<?php echo $fila['nombre']; ?>]" 
                       value="<?php echo $fila['cantidad']; ?>" min="1">
            </td>
            <td>$<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
            <td>
                <button type="submit" name="eliminar" value="<?php echo $fila['nombre']; ?>">❌ Eliminar</button>
            </td>
        </tr>
    <?php } ?>
</table>

<h3>Total general: 💰 $<?php echo number_format($total, 0, ',', '.'); ?></h3>

<button type="submit" name="actualizar">🔄 Actualizar carrito</button>
</form>

<a href="productos.php">← Seguir comprando</a>
<a href="pago.php">Proceder al pago →</a>
