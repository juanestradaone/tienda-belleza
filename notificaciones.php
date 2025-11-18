<?php
require __DIR__ . "/vendor/autoload.php";

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

MercadoPagoConfig::setAccessToken("APP_USR-3931003156939559-111613-23fed6c1bc5b2c31497f64b6c07e4ea8-2992707168");

// Leer notificación enviada por Mercado Pago
$body = file_get_contents("php://input");
$data = json_decode($body, true);

// Verificar que realmente venga un ID de pago
if (!isset($data["data"]["id"])) {
    http_response_code(400);
    echo "No payment ID";
    exit();
}

$payment_id = $data["data"]["id"];

// Consultar el pago en Mercado Pago
$client = new PaymentClient();
$payment = $client->get($payment_id);

// Obtener información del pago
$status = $payment->status;
$preference_id = $payment->additional_info["items"][0]["id"] ?? null;

// Conexión BD
$pdo = new PDO("mysql:host=localhost;dbname=tienda-belleza", "root", "");

// Buscar la orden por el preference_id
$stmt = $pdo->prepare("SELECT id_orden FROM ordenes WHERE mp_preference_id = ?");
$stmt->execute([$payment->order->id ?? null]);
$orden = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra la orden
if (!$orden) {
    http_response_code(404);
    echo "Orden no encontrada";
    exit();
}

$id_orden = $orden["id_orden"];

// Actualizar la orden según estado del pago
if ($status == "approved") {
    $stmt = $pdo->prepare(
        "UPDATE ordenes 
        SET estado='pagado', mp_payment_id=?, mp_status=? 
        WHERE id_orden=?"
    );
    $stmt->execute([$payment_id, $status, $id_orden]);
} else {
    $stmt = $pdo->prepare(
        "UPDATE ordenes 
        SET mp_payment_id=?, mp_status=? 
        WHERE id_orden=?"
    );
    $stmt->execute([$payment_id, $status, $id_orden]);
}

http_response_code(200);
echo "OK";
?>
