<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

if (!isset($_SESSION['usuario'])) exit();

$id_usuario = $_SESSION['usuario'];
$id_producto = $_POST['id_producto'];
$accion = $_POST['accion'];

// Buscar carrito del usuario
$sql_carrito = "SELECT id_carrito FROM carrito WHERE id_usuario = ? ORDER BY id_carrito DESC LIMIT 1";
$stmt = $conn->prepare($sql_carrito);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$id_carrito = $stmt->get_result()->fetch_assoc()['id_carrito'];

// Ejecutar acción
switch ($accion) {
    case 'sumar':
        $conn->query("UPDATE detalle_carrito SET cantidad = cantidad + 1 WHERE id_carrito = $id_carrito AND id_producto = $id_producto");
        break;
    case 'restar':
        $conn->query("UPDATE detalle_carrito SET cantidad = GREATEST(cantidad - 1, 1) WHERE id_carrito = $id_carrito AND id_producto = $id_producto");
        break;
    case 'eliminar':
        $conn->query("DELETE FROM detalle_carrito WHERE id_carrito = $id_carrito AND id_producto = $id_producto");
        break;
}

echo "OK";
?>
