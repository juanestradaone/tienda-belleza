<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

$total_items = 0;

if (isset($_SESSION['usuario'])) {
    $id_usuario = $_SESSION['usuario'];

    // Buscar el carrito actual del usuario
    $sql = "SELECT id_carrito FROM carrito WHERE id_usuario = ? ORDER BY id_carrito DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $id_carrito = $result->fetch_assoc()['id_carrito'];

        // Contar la cantidad total de productos en el carrito
        $sql_detalle = "SELECT SUM(cantidad) AS total_items FROM detalle_carrito WHERE id_carrito = ?";
        $stmt = $conn->prepare($sql_detalle);
        $stmt->bind_param("i", $id_carrito);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $total_items = $res['total_items'] ?? 0;
    }
}

echo json_encode(['total_items' => (int)$total_items]);
?>
