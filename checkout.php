<?php
require __DIR__ . '/vendor/autoload.php';
require 'conexion.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

session_start();

// Validar usuario
$id_usuario = $_SESSION['usuario'] ?? null;
if (!$id_usuario) {
    die("Debes iniciar sesión.");
}




// Configurar Mercado Pago
MercadoPagoConfig::setAccessToken("APP_USR-3931003156939559-111613-23fed6c1bc5b2c31497f64b6c07e4ea8-2992707168");

// ===============================
// 1️⃣ OBTENER CARRITO ACTIVO
// ===============================
$sql = "SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($id_carrito);
$stmt->fetch();
$stmt->close();

if (!$id_carrito) {
    die("No tienes carrito activo.");
}

// ===============================
// 2️⃣ OBTENER DETALLES DEL CARRITO
// ===============================
$sql = "
    SELECT dc.cantidad, dc.precio_unitario, p.nombre_producto
    FROM detalle_carrito dc
    INNER JOIN productos p ON p.id_producto = dc.id_producto
    WHERE dc.id_carrito = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_carrito);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $subtotal = $row["cantidad"] * $row["precio_unitario"];
    $total += $subtotal;

    $items[] = [
        "title" => $row["nombre_producto"],
        "quantity" => intval($row["cantidad"]),
        "unit_price" => floatval($row["precio_unitario"]),
        "currency_id" => "COP"
    ];
}

$stmt->close();

if (empty($items)) {
    die("El carrito está vacío.");
}

// ===============================
// 3️⃣ CREAR PREFERENCIA MERCADOPAGO
// ===============================

$client = new PreferenceClient();

// URLs públicas de ngrok
$base_url = "https://lennox-unmilitaristic-inspiringly.ngrok-free.dev/tienda-belleza";

try {
    $preference = $client->create([
        "items" => $items,
        "external_reference" => $id_carrito,
        "back_urls" => [
            "success" => "$base_url/pago_exitoso.php",
            "failure" => "$base_url/pago_fallido.php",
            "pending" => "$base_url/pago_pendiente.php",
        ],
        "notification_url" => "$base_url/webhook.php", // 🔥 IMPORTANTE
        "auto_return" => "approved",
    ]);

} catch (\Exception $e) {
    echo "<pre>";
    echo "❌ ERROR al crear preferencia:\n";
    print_r($e->getMessage());
    echo "</pre>";
    exit;
}

$preference_id = $preference->id;

// ===============================
// 4️⃣ GUARDAR ORDEN EN BD
// ===============================
$sql = "INSERT INTO ordenes (id_usuario, total, estado, mp_preference_id, id_carrito)
        VALUES (?, ?, 'pendiente', ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("idsi", $id_usuario, $total, $preference_id, $id_carrito);
$stmt->execute();
$stmt->close();

// ===============================
// 5️⃣ REDIRIGIR A MERCADOPAGO
// ===============================
header("Location: " . $preference->init_point);
exit();
