<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="../style.css">
    <meta charset="UTF-8">
    <title>Agregar producto</title>
<style>
    /* CSS breve para el formulario de agregar producto */
    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #f5f7fa;
        color: #333;
        padding: 24px;
    }

    h2 {
        margin: 0 0 12px 0;
        display: inline-block;
        font-size: 1.2rem;
    }

    form {
        max-width: 560px;
        background: #fff;
        padding: 18px;
        border-radius: 8px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
    }

    label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    select,
    input[type="file"] {
        width: 100%;
        padding: 8px 10px;
        margin: 6px 0 12px 0;
        border: 1px solid #e1e6ef;
        border-radius: 6px;
        box-sizing: border-box;
    }

    textarea {
        min-height: 90px;
        resize: vertical;
    }

    button {
        background: #007bff;
        color: #fff;
        border: none;
        padding: 10px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    button:hover {
        background: #0069d9;
    }

    @media (max-width: 600px) {
        form {
            padding: 14px;
        }
    }
</style>

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
    <select name="categoria" required>
        <option value=""> Seleccione una categoría </option>
        <option value="Maquillaje">Maquillaje</option>
        <option value="Cabello">Cabello</option>
        <option value="Facial">Facial</option>
        <option value="Corporal">Corporal</option>
          <option value="Uñas">Uñas</option>
        <option value="Ofertas">Ofertas</option>
    </select>
    <br><br>
    <label>Descripción:</label><br>
    <textarea name="descripcion"></textarea><br><br>

    <label>Imagen del producto:</label><br>
    <input type="file" name="imagen" accept="image/*" required><br><br>

    <button type="submit">Guardar producto</button>
</form>
//cambio realizado

</body>
</html>

