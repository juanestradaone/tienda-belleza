<?php
require __DIR__ . '/../Config/conexion.php';

$sql = "SELECT v.id_venta, u.nombre, v.total, v.fecha 
        FROM ventas v
        INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
        ORDER BY v.id_venta DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de ventas</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
<style>
    body {
        background-color: #111;
        color: white;
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 20px;
    }

    h2 {
        text-align: center;
        color: #ff4081;
        margin-bottom: 30px;
        font-size: 2rem;
    }

    .tabla-container {
        width: 90%;
        max-width: 900px;
        margin: auto;
        background: #1a1a1a;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 0 25px rgba(255, 64, 129, 0.3);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        overflow: hidden;
        border-radius: 12px;
    }

    th {
        background: #ff4081;
        padding: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: white;
    }

    td {
        background: #222;
        padding: 12px;
        text-align: center;
    }

    tr:nth-child(even) td {
        background: #2a2a2a;
    }

    tr:hover td {
        background: #333;
    }

    .volver {
        display: block;
        width: 200px;
        margin: 25px auto;
        padding: 12px;
        background: #ff4081;
        color: white;
        text-align: center;
        text-decoration: none;
        border-radius: 10px;
        font-weight: bold;
        transition: 0.3s;
    }

    .volver:hover {
        background: #ff1f75;
        transform: scale(1.05);
    }
</style>
</head>
<body>

<h2>📊 Panel de Ventas</h2>

<div class="tabla-container">
    <table>
        <tr>
            <th>ID Venta</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Fecha</th>
        </tr>

        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id_venta'] ?></td>
            <td><?= $row['nombre'] ?></td>
            <td>$<?= number_format($row['total'], 2) ?></td>
            <td><?= $row['fecha'] ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

<a class="volver" href="tienda.php">⬅ Volver a la tienda</a>

</body>
</html>
