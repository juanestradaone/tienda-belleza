<?php
session_start();
include("conexion.php");

$id_usuario = $_SESSION['id_usuario'];

// Buscar el carrito actual
$sql = "SELECT id_carrito FROM carrito WHERE id_usuario = ? ORDER BY id_carrito DESC LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$fila = $result->fetch_assoc();
$id_carrito = $fila['id_carrito'];

// Si se presionó “Actualizar”
if (isset($_POST['actualizar']) && !empty($_POST['cantidades'])) {
    foreach ($_POST['cantidades'] as $nombre => $cantidad) {
        // Buscar ID del producto
        $sql_producto = "SELECT id_producto FROM productos WHERE nombre = ?";
        $stmt_p = $conexion->prepare($sql_producto);
        $stmt_p->bind_param("s", $nombre);
        $stmt_p->execute();
        $res_p = $stmt_p->get_result();
        if ($res_p->num_rows > 0) {
            $id_producto = $res_p->fetch_assoc()['id_producto'];
            // Actualizar cantidad
            $sql_upd = "UPDATE detalle_carrito SET cantidad = ? WHERE id_carrito = ? AND id_producto = ?";
            $stmt_u = $conexion->prepare($sql_upd);
            $stmt_u->bind_param("iii", $cantidad, $id_carrito, $id_producto);
            $stmt_u->execute();
        }
    }
    echo "<script>alert('🛒 Carrito actualizado correctamente'); window.location='ver_carrito.php';</script>";
}

// Si se presionó “Eliminar”
if (isset($_POST['eliminar'])) {
    $nombre = $_POST['eliminar'];
    $sql_producto = "SELECT id_producto FROM productos WHERE nombre = ?";
    $stmt_p = $conexion->prepare($sql_producto);
    $stmt_p->bind_param("s", $nombre);
    $stmt_p->execute();
    $res_p = $stmt_p->get_result();
    if ($res_p->num_rows > 0) {
        $id_producto = $res_p->fetch_assoc()['id_producto'];
        // Eliminar del detalle
        $sql_del = "DELETE FROM detalle_carrito WHERE id_carrito = ? AND id_producto = ?";
        $stmt_d = $conexion->prepare($sql_del);
        $stmt_d->bind_param("ii", $id_carrito, $id_producto);
        $stmt_d->execute();
    }
    echo "<script>alert('❌ Producto eliminado'); window.location='ver_carrito.php';</script>";
}
?>
