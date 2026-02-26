<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../Config/conexion.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

session_start();

// Validar usuario
$id_usuario = $_SESSION['usuario'] ?? null;
if (!$id_usuario) {
    die("Debes iniciar sesión.");
}

$errores = [];
$envio_seleccionado = $_POST['metodo_envio'] ?? 'recoger';
$direccion_seleccionada = $_POST['direccion_existente'] ?? '';
$direccion_nueva = [
    'nombre_destinatario' => trim($_POST['nombre_destinatario'] ?? ''),
    'direccion' => trim($_POST['direccion'] ?? ''),
    'ciudad' => trim($_POST['ciudad'] ?? ''),
    'departamento' => trim($_POST['departamento'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? '')
];

$envio_base = 10000.00;
$tiene_costo_envio = false;
$stmt = $conn->prepare("SHOW COLUMNS FROM envios LIKE 'costo_envio'");
if ($stmt) {
    $stmt->execute();
    $resultado_columnas = $stmt->get_result();
    $tiene_costo_envio = $resultado_columnas && $resultado_columnas->num_rows > 0;
    $stmt->close();
}

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
    SELECT dc.id_producto, dc.cantidad, dc.precio_unitario, p.nombre_producto
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
        "id_producto" => intval($row["id_producto"]),
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
// 2️⃣.1 OBTENER DIRECCIONES GUARDADAS
// ===============================
$sql = "SELECT id_direccion, nombre_destinatario, direccion, ciudad, departamento, telefono
        FROM direcciones_envio WHERE id_usuario = ? ORDER BY id_direccion DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$direcciones_guardadas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Obtener datos del perfil para prellenar
$sql = "SELECT nombre, apellido, direccion, telefono FROM usuarios WHERE id_usuario = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

$nombre_defecto = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? ''));
$direccion_nueva['nombre_destinatario'] = $direccion_nueva['nombre_destinatario'] ?: $nombre_defecto;
$direccion_nueva['direccion'] = $direccion_nueva['direccion'] ?: ($usuario['direccion'] ?? '');
$direccion_nueva['telefono'] = $direccion_nueva['telefono'] ?: ($usuario['telefono'] ?? '');

// ===============================
// 3️⃣ CREAR PREFERENCIA MERCADOPAGO (POST)
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodo_envio = $envio_seleccionado === 'domicilio' ? 'domicilio' : 'recoger';
    $costo_envio = $metodo_envio === 'domicilio' ? $envio_base : 0.0;
    $id_direccion = null;

    if ($metodo_envio === 'domicilio') {
        if (!empty($direccion_seleccionada)) {
            $sql = "SELECT id_direccion FROM direcciones_envio WHERE id_direccion = ? AND id_usuario = ?";
            $stmt = $conn->prepare($sql);
            $id_dir = intval($direccion_seleccionada);
            $stmt->bind_param("ii", $id_dir, $id_usuario);
            $stmt->execute();
            $stmt->bind_result($id_direccion);
            $stmt->fetch();
            $stmt->close();

            if (!$id_direccion) {
                $errores[] = "La dirección seleccionada no es válida.";
            }
        } else {
            foreach (['nombre_destinatario', 'direccion', 'ciudad', 'departamento'] as $campo) {
                if (empty($direccion_nueva[$campo])) {
                    $errores[] = "Completa todos los datos de la dirección para el envío.";
                    break;
                }
            }

            if (empty($errores)) {
                $sql = "INSERT INTO direcciones_envio (id_usuario, nombre_destinatario, direccion, ciudad, departamento, telefono)
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "isssss",
                    $id_usuario,
                    $direccion_nueva['nombre_destinatario'],
                    $direccion_nueva['direccion'],
                    $direccion_nueva['ciudad'],
                    $direccion_nueva['departamento'],
                    $direccion_nueva['telefono']
                );
                $stmt->execute();
                $id_direccion = $stmt->insert_id;
                $stmt->close();
            }
        }
    }

    if (empty($errores)) {
        // Configurar Mercado Pago
        MercadoPagoConfig::setAccessToken("APP_USR-3931003156939559-111613-23fed6c1bc5b2c31497f64b6c07e4ea8-2992707168");

        $mp_items = $items;
        if ($costo_envio > 0) {
            $mp_items[] = [
                "title" => "Envío a domicilio",
                "quantity" => 1,
                "unit_price" => $costo_envio,
                "currency_id" => "COP"
            ];
        }

        $total_final = $total + $costo_envio;

        $client = new PreferenceClient();

        // URLs públicas de ngrok
        $base_url = "https://lennox-unmilitaristic-inspiringly.ngrok-free.dev/tienda-belleza";

        try {
            $preference = $client->create([
                "items" => $mp_items,
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
        if ($id_direccion) {
            $sql = "INSERT INTO ordenes (id_usuario, total, estado, mp_preference_id, id_carrito, id_direccion)
                    VALUES (?, ?, 'pendiente', ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("idsii", $id_usuario, $total_final, $preference_id, $id_carrito, $id_direccion);
        } else {
            $sql = "INSERT INTO ordenes (id_usuario, total, estado, mp_preference_id, id_carrito, id_direccion)
                    VALUES (?, ?, 'pendiente', ?, ?, NULL)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("idsi", $id_usuario, $total_final, $preference_id, $id_carrito);
        }
        $stmt->execute();
        $id_orden = $stmt->insert_id;
        $stmt->close();

        // Guardar snapshot de productos comprados para historial de pedidos
        $stmtDetalleOrden = $conn->prepare(
            "INSERT INTO detalle_orden (id_orden, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)"
        );

        if ($stmtDetalleOrden) {
            foreach ($items as $item) {
                $id_producto = (int) ($item['id_producto'] ?? 0);
                $cantidad_item = (int) ($item['quantity'] ?? 0);
                $precio_item = (float) ($item['unit_price'] ?? 0);

                if ($id_producto <= 0 || $cantidad_item <= 0) {
                    continue;
                }

                $stmtDetalleOrden->bind_param("iiid", $id_orden, $id_producto, $cantidad_item, $precio_item);
                $stmtDetalleOrden->execute();
            }

            $stmtDetalleOrden->close();
        }

        // ===============================
        // 4️⃣.1 GUARDAR DATOS DE ENVÍO
        // ===============================
        $metodo_envio_texto = $metodo_envio === 'domicilio' ? 'Envío a domicilio' : 'Recoger en tienda';
        if ($tiene_costo_envio) {
            $sql = "INSERT INTO envios (id_orden, metodo_envio, estado_envio, fecha_envio, costo_envio)
                    VALUES (?, ?, 'pendiente', NULL, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isd", $id_orden, $metodo_envio_texto, $costo_envio);
        } else {
            $sql = "INSERT INTO envios (id_orden, metodo_envio, estado_envio, fecha_envio)
                    VALUES (?, ?, 'pendiente', NULL)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $id_orden, $metodo_envio_texto);
        }
        $stmt->execute();
        $stmt->close();

        // ===============================
        // 5️⃣ REDIRIGIR A MERCADOPAGO
        // ===============================
        header("Location: " . $preference->init_point);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Checkout | Belleza y Glamour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1b1b1b 50%, #2b2b2b 100%);
            color: #fff;
            padding: 24px;
        }

        .container {
            max-width: 980px;
            margin: 0 auto;
            display: grid;
            gap: 18px;
        }

        .card {
            background: rgba(17, 17, 17, 0.95);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(255, 105, 180, 0.2);
            box-shadow: 0 12px 30px rgba(255, 20, 147, 0.2);
        }

        h1 {
            margin: 0 0 6px;
            text-align: center;
            color: #ff69b4;
        }

        .subtitle {
            text-align: center;
            color: #cfcfcf;
            margin-bottom: 18px;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 12px;
        }

        .items {
            display: grid;
            gap: 10px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 12px;
            background: #1f1f1f;
            border-radius: 10px;
        }

        .total {
            display: flex;
            justify-content: space-between;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 14px;
        }

        .form-grid {
            display: grid;
            gap: 12px;
        }

        label {
            font-size: 0.9rem;
            color: #cfcfcf;
        }

        input,
        select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(255, 105, 180, 0.4);
            background: #1f1f1f;
            color: #fff;
            font-family: inherit;
        }

        .radio-group {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .radio-card {
            flex: 1 1 200px;
            padding: 12px 16px;
            background: #1c1c1c;
            border-radius: 12px;
            border: 1px solid rgba(255, 105, 180, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error {
            background: rgba(255, 20, 147, 0.12);
            border: 1px solid rgba(255, 20, 147, 0.45);
            color: #ffb3d9;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 12px;
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 10px;
        }

        .btn-volver-carrito {
          background: linear-gradient(135deg, #ff1493, #ff69b4);
            border: none;
            color: #fff;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }

        

        .btn {
            background: linear-gradient(135deg, #ff1493, #ff69b4);
            border: none;
            color: #fff;
            padding: 12px 18px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }


        .muted {
            color: #cfcfcf;
            font-size: 0.9rem;
        }

        .address-block {
            display: none;
        }

        .address-block.active {
            display: block;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>✅ Finaliza tu pedido</h1>
        <p class="subtitle">Elige cómo quieres recibir tu pedido y confirma la dirección de envío.</p>
    </div>

    <?php if (!empty($errores)): ?>
        <div class="card">
            <?php foreach ($errores as $error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="section-title">Resumen del carrito</div>
        <div class="items">
            <?php foreach ($items as $item): ?>
                <div class="item">
                    <span><?= htmlspecialchars($item['title']) ?> × <?= intval($item['quantity']) ?></span>
                    <span>$<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="total">
            <span>Subtotal</span>
            <span>$<?= number_format($total, 2) ?></span>
        </div>
        <p class="muted">El costo de envío estándar es de $<?= number_format($envio_base, 2) ?> (el administrador puede ajustarlo según la zona).</p>
    </div>

    <form class="card" method="POST" action="">
        <div class="section-title">Método de entrega</div>
        <div class="radio-group">
            <label class="radio-card">
                <input type="radio" name="metodo_envio" value="recoger" <?= $envio_seleccionado === 'recoger' ? 'checked' : '' ?>>
                Recoger en tienda física
            </label>
            <label class="radio-card">
                <input type="radio" name="metodo_envio" value="domicilio" <?= $envio_seleccionado === 'domicilio' ? 'checked' : '' ?>>
                Envío a domicilio
            </label>
        </div>

        <div class="address-block <?= $envio_seleccionado === 'domicilio' ? 'active' : '' ?>" id="direccionBlock">
            <div class="section-title">Dirección de envío</div>
            <?php if (!empty($direcciones_guardadas)): ?>
                <label>Selecciona una dirección guardada</label>
                <select name="direccion_existente">
                    <option value="">Nueva dirección</option>
                    <?php foreach ($direcciones_guardadas as $dir): ?>
                        <option value="<?= intval($dir['id_direccion']) ?>" <?= $direccion_seleccionada == $dir['id_direccion'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dir['direccion'] . ', ' . $dir['ciudad'] . ' (' . $dir['departamento'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <div class="form-grid" style="margin-top:12px;">
                <div>
                    <label>Nombre destinatario</label>
                    <input type="text" name="nombre_destinatario" value="<?= htmlspecialchars($direccion_nueva['nombre_destinatario']) ?>">
                </div>
                <div>
                    <label>Dirección</label>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($direccion_nueva['direccion']) ?>">
                </div>
                <div>
                    <label>Ciudad</label>
                    <input type="text" name="ciudad" value="<?= htmlspecialchars($direccion_nueva['ciudad']) ?>">
                </div>
                <div>
                    <label>Departamento</label>
                    <input type="text" name="departamento" value="<?= htmlspecialchars($direccion_nueva['departamento']) ?>">
                </div>
                <div>
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($direccion_nueva['telefono']) ?>">
                </div>
            </div>
        </div>

        <div class="actions">
            <button class="btn" type="submit">Continuar al pago</button>
            <a href="carrito.php" class="btn-volver-carrito">⬅ Volver al carrito</a>
        </div>
    </form>
</div>

<script>
    const radioEnvio = document.querySelectorAll('input[name="metodo_envio"]');
    const direccionBlock = document.getElementById('direccionBlock');

    radioEnvio.forEach((radio) => {
        radio.addEventListener('change', () => {
            if (radio.value === 'domicilio') {
                direccionBlock.classList.add('active');
            } else {
                direccionBlock.classList.remove('active');
            }
        });
    });
</script>
</body>
</html>
