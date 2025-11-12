<?php
include("../conexion.php"); // sube un nivel porque está en /admin

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $categoria = $_POST['categoria'];
    $descripcion = $_POST['descripcion'];

    // Manejo de la imagen
    $directorio = "../uploads/";
    $nombre_imagen = basename($_FILES["imagen"]["name"]);
    $ruta_final = $directorio . $nombre_imagen;

    // Verificar si el archivo es una imagen
    $tipo = strtolower(pathinfo($ruta_final, PATHINFO_EXTENSION));
    $permitidos = ["jpg", "jpeg", "png", "gif"];

    if (!in_array($tipo, $permitidos)) {
        echo "<script>alert('❌ Solo se permiten imágenes (JPG, JPEG, PNG, GIF)'); window.history.back();</script>";
        exit;
    }

    // Mover la imagen a la carpeta uploads
    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_final)) {

        // Guardar en la base de datos
        $sql = "INSERT INTO productos (nombre_producto, precio_producto, cantidad_disponible, categoria_producto, descripcion, imagen)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdisss", $nombre, $precio, $cantidad, $categoria, $descripcion, $nombre_imagen);

        if ($stmt->execute()) {
            echo "<script>
                    alert('✅ Producto agregado correctamente');
                    window.location.href='agregar_producto.php';
                  </script>";
        } else {
            echo "❌ Error al guardar el producto: " . $conn->error;
        }

    } else {
        echo "<script>alert('❌ Error al subir la imagen'); window.history.back();</script>";
    }
}
?>
