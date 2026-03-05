<?php
session_start();
if (!isset($_SESSION['usuario']) || (($_SESSION['rol'] ?? '') !== 'admin')) {
    header('Location: ../../index.php');
    exit;
}

require __DIR__ . '/../../Config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../tienda.php');
    exit;
}

$id = (int)($_POST['id_producto'] ?? 0);
if ($id <= 0) {
    die('ID inválido.');
}

$stmt = $conn->prepare('UPDATE productos SET activo = 0 WHERE id_producto = ?');
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo "<script>alert('🗑️ Producto eliminado correctamente'); window.location.href='../../tienda.php';</script>";
    exit;
}

echo '❌ Error al eliminar producto: ' . $conn->error;
