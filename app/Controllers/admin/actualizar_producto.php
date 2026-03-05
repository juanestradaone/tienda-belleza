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
$nombre = trim($_POST['nombre'] ?? '');
$precio = (float)($_POST['precio'] ?? 0);
$cantidad = (int)($_POST['cantidad'] ?? 0);
$categoria = trim($_POST['categoria'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if ($id <= 0 || $nombre === '' || $precio <= 0 || $cantidad < 0 || $categoria === '') {
    die('Datos inválidos para actualizar el producto.');
}

$stmtActual = $conn->prepare('SELECT imagen FROM productos WHERE id_producto = ? LIMIT 1');
$stmtActual->bind_param('i', $id);
$stmtActual->execute();
$resActual = $stmtActual->get_result();
$actual = $resActual->fetch_assoc();

if (!$actual) {
    die('Producto no encontrado.');
}

$imagenFinal = $actual['imagen'];
if (!empty($_FILES['imagen']['name'])) {
    $directorio = __DIR__ . '/../../uploads/';
    $nombreImagen = basename($_FILES['imagen']['name']);
    $rutaFinal = $directorio . $nombreImagen;

    $tipo = strtolower(pathinfo($rutaFinal, PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($tipo, $permitidos, true)) {
        die('Formato de imagen no permitido.');
    }

    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFinal)) {
        die('No fue posible subir la nueva imagen.');
    }

    $imagenFinal = $nombreImagen;
}

$sql = 'UPDATE productos SET nombre_producto = ?, precio_producto = ?, cantidad_disponible = ?, categoria_producto = ?, descripcion = ?, imagen = ? WHERE id_producto = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('sdisssi', $nombre, $precio, $cantidad, $categoria, $descripcion, $imagenFinal, $id);

if ($stmt->execute()) {
    echo "<script>alert('✅ Producto actualizado correctamente'); window.location.href='../../tienda.php';</script>";
    exit;
}

echo '❌ Error al actualizar el producto: ' . $conn->error;
