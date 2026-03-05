<?php
session_start();
if (!isset($_SESSION['usuario']) || (($_SESSION['rol'] ?? '') !== 'admin')) {
    header('Location: ../index.php');
    exit;
}

require __DIR__ . '/../../Config/conexion.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    die('ID de producto inválido.');
}

$stmt = $conn->prepare('SELECT * FROM productos WHERE id_producto = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();

if (!$producto) {
    die('Producto no encontrado.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f5f7fa; padding: 24px; }
        .card { max-width: 640px; background:#fff; padding:20px; border-radius:10px; box-shadow:0 6px 16px rgba(0,0,0,.08); }
        label { display:block; margin: 10px 0 6px; font-weight:600; }
        input, select, textarea { width:100%; padding:10px; border:1px solid #d9dfe8; border-radius:8px; box-sizing:border-box; }
        textarea { min-height: 100px; }
        .actions { margin-top:14px; display:flex; gap:10px; }
        button, a.btn { border:none; border-radius:8px; padding:10px 14px; cursor:pointer; text-decoration:none; font-weight:600; }
        button { background:#007bff; color:#fff; }
        .btn { background:#6c757d; color:#fff; }
        img { max-width: 150px; border-radius:8px; margin-top:8px; background:#fff; border:1px solid #eee; }
    </style>
</head>
<body>
<div class="card">
    <h2>✏️ Editar producto</h2>
    <form action="actualizar_producto.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_producto" value="<?= (int)$producto['id_producto'] ?>">

        <label>Nombre</label>
        <input type="text" name="nombre" required value="<?= htmlspecialchars($producto['nombre_producto']) ?>">

        <label>Precio</label>
        <input type="number" step="0.01" name="precio" required value="<?= htmlspecialchars($producto['precio_producto']) ?>">

        <label>Cantidad disponible</label>
        <input type="number" name="cantidad" required value="<?= htmlspecialchars($producto['cantidad_disponible']) ?>">

        <label>Categoría</label>
        <select name="categoria" required>
            <?php
            $categorias = ['Maquillaje', 'Cabello', 'Facial', 'Corporal', 'Uñas', 'Ofertas'];
            foreach ($categorias as $cat):
            ?>
                <option value="<?= $cat ?>" <?= ($producto['categoria_producto'] === $cat ? 'selected' : '') ?>><?= $cat ?></option>
            <?php endforeach; ?>
        </select>

        <label>Descripción</label>
        <textarea name="descripcion"><?= htmlspecialchars($producto['descripcion']) ?></textarea>

        <label>Imagen actual</label>
        <?php if (!empty($producto['imagen'])): ?>
            <img src="../../uploads/<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen actual">
        <?php else: ?>
            <p>Sin imagen</p>
        <?php endif; ?>

        <label>Nueva imagen (opcional)</label>
        <input type="file" name="imagen" accept="image/*">

        <div class="actions">
            <button type="submit">Guardar cambios</button>
            <a class="btn" href="../tienda.php">Volver</a>
        </div>
    </form>
</div>
</body>
</html>
