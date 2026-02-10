<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

// 1️⃣ Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

// 2️⃣ Validar los datos recibidos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_producto'], $_POST['cantidad'])) {
    $id_producto = (int)$_POST['id_producto'];
    $cantidad = (int)$_POST['cantidad'];

    // Validar cantidad
    if ($cantidad < 1) {
        header('Location: tienda.php');
        exit;
    }

    $id_usuario = $_SESSION['usuario'];

    // 3️⃣ Buscar si el usuario ya tiene un carrito abierto
    $sql_carrito = "SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo' LIMIT 1";
    $stmt = $conn->prepare($sql_carrito);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result_carrito = $stmt->get_result();

    if ($result_carrito->num_rows > 0) {
        $row_carrito = $result_carrito->fetch_assoc();
        $id_carrito = $row_carrito['id_carrito'];
    } else {
        // Crear nuevo carrito
        $sql_nuevo = "INSERT INTO carrito (id_usuario, fecha_creacion, estado) VALUES (?, NOW(), 'activo')";
        $stmt_nuevo = $conn->prepare($sql_nuevo);
        $stmt_nuevo->bind_param("i", $id_usuario);
        $stmt_nuevo->execute();
        $id_carrito = $stmt_nuevo->insert_id;
        $stmt_nuevo->close();
    }
    $stmt->close();

    // 4️⃣ Verificar si el producto ya está en el carrito
    $sql_detalle = "SELECT id_detalle, cantidad FROM detalle_carrito WHERE id_carrito = ? AND id_producto = ?";
    $stmt = $conn->prepare($sql_detalle);
    $stmt->bind_param("ii", $id_carrito, $id_producto);
    $stmt->execute();
    $result_detalle = $stmt->get_result();

    if ($result_detalle->num_rows > 0) {
        // Ya existe → actualizar cantidad
        $detalle = $result_detalle->fetch_assoc();
        $nueva_cantidad = $detalle['cantidad'] + $cantidad;

        $sql_update = "UPDATE detalle_carrito SET cantidad = ? WHERE id_detalle = ?";
        $stmt_upd = $conn->prepare($sql_update);
        $stmt_upd->bind_param("ii", $nueva_cantidad, $detalle['id_detalle']);
        $stmt_upd->execute();
        $stmt_upd->close();
    } else {
        // Nuevo producto → obtener precio actual
        $sql_precio = "SELECT precio_producto FROM productos WHERE id_producto = ?";
        $stmt_precio = $conn->prepare($sql_precio);
        $stmt_precio->bind_param("i", $id_producto);
        $stmt_precio->execute();
        $result_precio = $stmt_precio->get_result();

        if ($result_precio->num_rows > 0) {
            $row_precio = $result_precio->fetch_assoc();
            $precio = $row_precio['precio_producto'];

            // Insertar producto en detalle_carrito
            $sql_insert = "INSERT INTO detalle_carrito (id_carrito, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iiid", $id_carrito, $id_producto, $cantidad, $precio);
            $stmt_insert->execute();
            $stmt_insert->close();
        } else {
            header('Location: tienda.php');
            exit;
        }
        $stmt_precio->close();
    }

    // 5️⃣ Confirmar y redirigir
    header('Location: tienda.php');
    exit;

} else {
    header('Location: tienda.php');
    exit;
}

$conn->close();
?>
