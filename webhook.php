<?php
require __DIR__ . '/vendor/autoload.php';
require 'conexion.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\MerchantOrder\MerchantOrderClient;

MercadoPagoConfig::setAccessToken("APP_USR-3931003156939559-111613-23fed6c1bc5b2c31497f64b6c07e4ea8-2992707168");

// Log inicial
file_put_contents("logs/webhook.log", date("Y-m-d H:i:s") . " - RAW: " . file_get_contents("php://input") . "\n", FILE_APPEND);

$body = json_decode(file_get_contents("php://input"), true);
if (!$body) {
    http_response_code(400);
    exit("Invalid body");
}

$topic = $body["topic"] ?? ($body["type"] ?? null);

// ====================================================
// 🔥 FUNCIÓN PARA EVITAR PROCESAR PAGOS REPETIDOS
// ====================================================
function yaProcesado($paymentId, $conn)
{
    $stmt = $conn->prepare("SELECT id FROM pagos_procesados WHERE payment_id = ?");
    $stmt->bind_param("s", $paymentId);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

function marcarProcesado($paymentId, $conn)
{
    $stmt = $conn->prepare("INSERT INTO pagos_procesados (payment_id) VALUES (?)");
    $stmt->bind_param("s", $paymentId);
    $stmt->execute();
}

// ====================================================
// 🔥 PROCESAR SOLO EVENTOS PAYMENT
// ====================================================
if ($topic === "payment") {

    $paymentId = $body["data"]["id"] ?? null;
    if (!$paymentId) exit("NO PAYMENT ID");

    // Evitar pagos repetidos
    if (yaProcesado($paymentId, $conn)) {
        file_put_contents("logs/webhook.log", date("Y-m-d H:i:s") . " - ⚠ Pago duplicado ignorado: $paymentId\n", FILE_APPEND);
        exit("DUPLICATED");
    }

    $client = new PaymentClient();
    $payment = $client->get($paymentId);

    $external_reference = $payment->external_reference;
    $status = $payment->status;

    if ($status === "approved") {
        procesarPago($external_reference, $status, $conn);

        // marcar como procesado
        marcarProcesado($paymentId, $conn);
    }

    exit("OK payment");
}

// ====================================================
// 🔵 NOTIFICACIONES MERCHANT ORDER (IGNORADAS)
// ====================================================
file_put_contents("logs/webhook.log", date("Y-m-d H:i:s") . " - Merchant_order recibido, ignorado.\n", FILE_APPEND);

exit("IGNORED");

// ====================================================
// 🔥 FUNCIÓN PRINCIPAL: MARCAR ORDEN Y CERRAR CARRITO
// ====================================================
function procesarPago($external_reference, $status, $conn)
{
    if ($status !== "approved") return;

    global $paymentId; // usamos paymentId dentro de la función

    // 1. Obtener usuario propietario del carrito
    $sql = "SELECT id_usuario FROM carrito WHERE id_carrito = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $external_reference);
    $stmt->execute();
    $stmt->bind_result($id_usuario);
    $stmt->fetch();
    $stmt->close();

    if (!$id_usuario) {
        file_put_contents("logs/webhook.log", date("Y-m-d H:i:s") . " - ⚠ No se encontró id_usuario para carrito $external_reference\n", FILE_APPEND);
        return;
    }

    // 2. Actualizar estado de la orden
    $sql = "UPDATE ordenes SET estado = 'pagada' WHERE id_carrito = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $external_reference);
    $stmt->execute();
    $stmt->close();

    // 2b. Guardar payment_id
    $sql = "UPDATE ordenes SET mp_payment_id = ?, mp_status = 'approved' WHERE id_carrito = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $paymentId, $external_reference);
    $stmt->execute();
    $stmt->close();

    // 3. Cerrar carrito
    $sql = "UPDATE carrito SET estado = 'cerrado' WHERE id_carrito = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $external_reference);
    $stmt->execute();
    $stmt->close();

    // 4. Vaciar productos del carrito
    $sql = "DELETE FROM carrito_detalle WHERE id_carrito = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $external_reference);
    $stmt->execute();
    $stmt->close();

    // 5. Crear nuevo carrito para el usuario
    $sql = "INSERT INTO carrito (id_usuario, estado) VALUES (?, 'activo')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();

    file_put_contents("logs/webhook.log",
        date("Y-m-d H:i:s") .
        " - 🔥 Pago aprobado para carrito $external_reference, mp_payment_id=$paymentId guardado, carrito vaciado y nuevo carrito creado para usuario $id_usuario\n",
        FILE_APPEND
    );
}



?>
