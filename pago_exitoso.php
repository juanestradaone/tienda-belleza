<?php
session_start();
require 'conexion.php';

// Obtener usuario
$id_usuario = $_SESSION['usuario'] ?? null;

// Obtener el carrito más reciente que ya esté pagado
$sql = "
    SELECT id_carrito, fecha_compra
    FROM ordenes
    WHERE id_usuario = ? AND estado = 'pagada'
    ORDER BY fecha_compra DESC
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($carrito_pagado, $fecha_compra);
$stmt->fetch();
$stmt->close();


// Si no hay orden pagada reciente
if (!$carrito_pagado) {
    $carrito_pagado = 0;
    $productos = [];
} else {

    // Obtener productos de ese carrito
    $sql = "
        SELECT p.nombre, p.precio, pc.cantidad
        FROM productos_carrito pc
        INNER JOIN productos p ON p.id_producto = pc.id_producto
        WHERE pc.id_carrito = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $carrito_pagado);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $productos = [];
    $total = 0;

    while ($row = $resultado->fetch_assoc()) {
        $row["subtotal"] = $row["precio"] * $row["cantidad"];
        $total += $row["subtotal"];
        $productos[] = $row;
    }
    $stmt->close();
}

/* ======================================================
   CREAR NUEVO CARRITO ACTIVO Y LIMPIAR LA SESIÓN
====================================================== */

if ($id_usuario) {

    // Buscar carrito activo
    $sql = "SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->bind_result($nuevo_carrito);
    $stmt->fetch();
    $stmt->close();

    // Si no hay carrito, se crea
    if (!$nuevo_carrito) {
        $sql = "INSERT INTO carrito (id_usuario, estado) VALUES (?, 'activo')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $nuevo_carrito = $stmt->insert_id;
        $stmt->close();
    }

    // Resetear ID del carrito en sesión
    $_SESSION['carrito'] = $nuevo_carrito;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pago Exitoso</title>

<style>

/* ===========================
   ESTILO NEGRO + ROSA NEÓN
=========================== */

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #0a0a0a 0%, #1b1b1b 50%, #2b2b2b 100%);
    color: #f5f5f5;
    margin: 0;
    padding: 0;
    min-height: 100vh;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    background: rgba(20,20,20,0.9);
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 0 25px rgba(255, 20, 147, 0.3);
    border: 2px solid #ff1493;
}

h1 {
    color: #ff69b4;
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 10px;
    text-shadow: 0 0 10px #ff1493;
    animation: glow 2s infinite alternate;
}

@keyframes glow {
    from { text-shadow: 0 0 10px #ff1493; }
    to { text-shadow: 0 0 20px #ff69b4; }
}

.summary-title {
    text-align: center;
    font-size: 1.2rem;
    color: #ccc;
    margin-bottom: 25px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

table th {
    background: #111;
    color: #ff69b4;
    padding: 12px;
    font-size: 1rem;
    border-bottom: 2px solid #ff1493;
    text-shadow: 0 0 5px #ff1493;
}

table td {
    padding: 12px;
    border-bottom: 1px solid #333;
    font-size: 1rem;
    color: #eee;
}

.total {
    font-size: 1.6rem;
    text-align: right;
    margin-top: 20px;
    font-weight: bold;
    color: #ff69b4;
    text-shadow: 0 0 10px rgba(255,105,180,0.5);
}

.btn {
    display: block;
    width: 100%;
    text-align: center;
    padding: 14px;
    background: linear-gradient(135deg, #ff1493, #ff69b4);
    color: white;
    text-decoration: none;
    border-radius: 15px;
    font-size: 1.3rem;
    font-weight: bold;
    margin-top: 30px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn:hover {
    background: transparent;
    color: #ff69b4;
    border-color: #ff69b4;
    box-shadow: 0 0 20px #ff69b4;
    transform: translateY(-3px);
}

.order-info {
    background: #111;
    padding: 15px;
    border-radius: 12px;
    border: 1px solid #ff1493;
    margin-bottom: 20px;
    box-shadow: 0 0 15px rgba(255,20,147,0.2);
}

.order-info p {
    font-size: 1rem;
    color: #ddd;
}

</style>

</head>
<body>

<div class="container">

    <h1>¡Pago realizado con éxito!</h1>
    <p class="summary-title">Gracias por tu compra. Aquí tienes el resumen de tu orden.</p>

    <?php if ($carrito_pagado && !empty($productos)) : ?>

        <div class="order-info">
            <p><strong>Orden #<?= $carrito_pagado ?></strong></p>
            <p><strong>Fecha:</strong> <?= $fecha_compra ?></p>
        </div>

        <table>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio unidad</th>
                <th>Subtotal</th>
            </tr>

            <?php foreach ($productos as $p) : ?>
                <tr>
                    <td><?= $p["nombre"] ?></td>
                    <td><?= $p["cantidad"] ?></td>
                    <td>$<?= number_format($p["precio"], 0, ",", ".") ?></td>
                    <td>$<?= number_format($p["subtotal"], 0, ",", ".") ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <p class="total">Total pagado: $<?= number_format($total, 0, ",", ".") ?></p>

    <?php else : ?>
        <p>No se encontró información de la orden. (Puede deberse a que el webhook aún no la registró).</p>
    <?php endif; ?>

    <a href="index.php" class="btn">Volver a la tienda</a>

</div>

</body>
</html>
