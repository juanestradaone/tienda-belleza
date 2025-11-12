<?php
session_start();
include("conexion.php");

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    echo "<script>
            alert('⚠️ Debes iniciar sesión para agregar productos al carrito');
            window.location.href = 'index.php';
          </script>";
    exit();
}

// Verificar si se envió el ID del producto
if (!isset($_POST['id_producto'])) {
    echo "<script>
            alert('❌ No se recibió ningún producto');
            window.location.href = 'tienda.php';
          </script>";
    exit();
}

$id_usuario = $_SESSION['usuario'];
$id_producto = $_POST['id_producto'];

// 1️⃣ Buscar si el usuario ya tiene un carrito activo
$sql_carrito = "SELECT id_carrito FROM carrito WHERE id_usuario = ?";
$stmt = $conn->prepare($sql_carrito);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result_carrito = $stmt->get_result();

if ($result_carrito->num_rows > 0) {
    $row_carrito = $result_carrito->fetch_assoc();
    $id_carrito = $row_carrito['id_carrito'];
} else {
    // Si no tiene carrito, se crea uno nuevo
    $sql_nuevo = "INSERT INTO carrito (id_usuario, fecha_creacion, fecha_actualizacion)
                  VALUES (?, NOW(), NOW())";
    $stmt_nuevo = $conn->prepare($sql_nuevo);
    $stmt_nuevo->bind_param("i", $id_usuario);
    $stmt_nuevo->execute();
    $id_carrito = $conn->insert_id;
}

// 2️⃣ Verificar si el producto ya está en el carrito
$sql_detalle = "SELECT * FROM detalle_carrito WHERE id_carrito = ? AND id_producto = ?";
$stmt_detalle = $conn->prepare($sql_detalle);
$stmt_detalle->bind_param("ii", $id_carrito, $id_producto);
$stmt_detalle->execute();
$result_detalle = $stmt_detalle->get_result();

if ($result_detalle->num_rows > 0) {
    // Si ya está, aumentamos la cantidad
    $sql_update = "UPDATE detalle_carrito SET cantidad = cantidad + 1 WHERE id_carrito = ? AND id_producto = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $id_carrito, $id_producto);
    $stmt_update->execute();
} else {
    // Si no está, lo agregamos con cantidad 1
    $sql_insert = "INSERT INTO detalle_carrito (id_carrito, id_producto, cantidad)
                   VALUES (?, ?, 1)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ii", $id_carrito, $id_producto);
    $stmt_insert->execute();
}

// 3️⃣ Actualizamos la fecha del carrito
$conn->query("UPDATE carrito SET fecha_actualizacion = NOW() WHERE id_carrito = $id_carrito");

// 4️⃣ Redirigimos al usuario
echo "<script>
        alert('✅ Producto agregado al carrito correctamente');
        window.location.href = 'tienda.php';
      </script>";

$conn->close();
?>
