<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar producto</title>
</head>
<body>

<h2>🛍️ Agregar nuevo producto</h2>

<form action="guardar_producto.php" method="POST" enctype="multipart/form-data">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br><br>

    <label>Precio:</label><br>
    <input type="number" step="0.01" name="precio" required><br><br>

    <label>Cantidad disponible:</label><br>
    <input type="number" name="cantidad" required><br><br>

    <label>Categoría:</label><br>
    <input type="text" name="categoria"><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion"></textarea><br><br>

    <label>Imagen del producto:</label><br>
    <input type="file" name="imagen" accept="image/*" required><br><br>

    <button type="submit">Guardar producto</button>
</form>

</body>
</html>
