<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

$estado_labels = [
    'pendiente' => 'En espera',
    'pagado' => 'Listo para enviar',
    'enviado' => 'En camino',
    'entregado' => 'Entregado',
    'cancelado' => 'Cancelado'
];

$estado_steps = ['pendiente', 'pagado', 'enviado', 'entregado'];
$estado_index = array_flip($estado_steps);

$notificacion_whatsapp = null;
$tiene_costo_envio = false;
$stmt = $conn->prepare("SHOW COLUMNS FROM envios LIKE 'costo_envio'");
if ($stmt) {
    $stmt->execute();
    $resultado_columnas = $stmt->get_result();
    $tiene_costo_envio = $resultado_columnas && $resultado_columnas->num_rows > 0;
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'], $_POST['orden_id'])) {
    $orden_id = intval($_POST['orden_id']);
    $accion = $_POST['accion'];

    if ($accion === 'estado' && isset($_POST['estado'])) {
        $nuevo_estado = $_POST['estado'];

        if (!array_key_exists($nuevo_estado, $estado_labels)) {
            $nuevo_estado = 'pendiente';
        }

        $stmt = $conn->prepare('UPDATE ordenes SET estado = ? WHERE id_orden = ?');
        $stmt->bind_param('si', $nuevo_estado, $orden_id);
        $stmt->execute();
        $stmt->close();

        if (in_array($nuevo_estado, ['enviado', 'entregado'], true)) {
            $stmt = $conn->prepare(
                'SELECT o.id_orden, o.estado, u.nombre, u.apellido, u.telefono FROM ordenes o INNER JOIN usuarios u ON o.id_usuario = u.id_usuario WHERE o.id_orden = ?'
            );
            $stmt->bind_param('i', $orden_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $orden = $result->fetch_assoc();
            $stmt->close();

            if ($orden && !empty($orden['telefono'])) {
                $telefono = preg_replace('/[^0-9]/', '', $orden['telefono']);
                if (strlen($telefono) === 10 && str_starts_with($telefono, '3')) {
                    $telefono = '57' . $telefono;
                }

                $nombre_cliente = trim($orden['nombre'] . ' ' . $orden['apellido']);
                $estado_texto = $estado_labels[$nuevo_estado] ?? $nuevo_estado;
                $mensaje = "Hola {$nombre_cliente}, tu pedido #{$orden['id_orden']} ahora está {$estado_texto}. ¡Gracias por comprar con Belleza y Glamour Angelita!";
                $whatsapp_link = 'https://wa.me/' . $telefono . '?text=' . urlencode($mensaje);

                $notificacion_whatsapp = [
                    'orden_id' => $orden['id_orden'],
                    'estado' => $estado_texto,
                    'telefono' => $telefono,
                    'link' => $whatsapp_link,
                    'cliente' => $nombre_cliente
                ];
            }
        }
    }

    if ($accion === 'envio' && isset($_POST['costo_envio']) && $tiene_costo_envio) {
        $nuevo_costo = max(0, floatval($_POST['costo_envio']));

        $stmt = $conn->prepare('SELECT o.total, e.costo_envio FROM ordenes o INNER JOIN envios e ON o.id_orden = e.id_orden WHERE o.id_orden = ?');
        $stmt->bind_param('i', $orden_id);
        $stmt->execute();
        $stmt->bind_result($total_actual, $costo_actual);
        $stmt->fetch();
        $stmt->close();

        if ($total_actual !== null) {
            $total_actualizado = ($total_actual - floatval($costo_actual)) + $nuevo_costo;

            $stmt = $conn->prepare('UPDATE envios SET costo_envio = ? WHERE id_orden = ?');
            $stmt->bind_param('di', $nuevo_costo, $orden_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare('UPDATE ordenes SET total = ? WHERE id_orden = ?');
            $stmt->bind_param('di', $total_actualizado, $orden_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$columna_costo = $tiene_costo_envio ? 'e.costo_envio' : '0 AS costo_envio';

$sql = "SELECT o.id_orden, o.fecha_compra, o.total, o.estado, o.id_direccion,
               u.nombre, u.apellido, u.email, u.telefono,
               e.metodo_envio, {$columna_costo},
               d.direccion, d.ciudad, d.departamento
        FROM ordenes o
        INNER JOIN usuarios u ON o.id_usuario = u.id_usuario
        LEFT JOIN envios e ON o.id_orden = e.id_orden
        LEFT JOIN direcciones_envio d ON o.id_direccion = d.id_direccion
        ORDER BY o.fecha_compra DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrador de pedidos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0a0a0a 0%, #1b1b1b 50%, #2b2b2b 100%);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 24px;
        }

        h1 {
            text-align: center;
            color: #ff69b4;
            margin-bottom: 12px;
            font-size: 2.1rem;
            text-shadow: 0 0 12px rgba(255, 105, 180, 0.4);
        }

        .subtitulo {
            text-align: center;
            color: #cfcfcf;
            margin-bottom: 24px;
        }

        .panel {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            gap: 18px;
        }

        .card {
            background: rgba(17, 17, 17, 0.95);
            border: 1px solid rgba(255, 105, 180, 0.2);
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 15px 35px rgba(255, 20, 147, 0.2);
        }

        .card-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
        }

        .card h3 {
            margin: 0;
            font-size: 1.2rem;
        }

        .meta {
            color: #cfcfcf;
            font-size: 0.95rem;
        }

        .status-pill {
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.9rem;
            background: rgba(255, 20, 147, 0.2);
            color: #ff69b4;
        }

        .track {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 16px;
        }

        .track-step {
            text-align: center;
            padding: 10px 12px;
            border-radius: 12px;
            background: #1c1c1c;
            border: 1px solid rgba(255, 105, 180, 0.15);
            font-size: 0.85rem;
            color: #bdbdbd;
        }

        .track-step.active {
            background: linear-gradient(135deg, rgba(255, 20, 147, 0.35), rgba(255, 105, 180, 0.35));
            color: #fff;
            border-color: rgba(255, 105, 180, 0.4);
            box-shadow: 0 8px 16px rgba(255, 20, 147, 0.25);
        }

        form {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        select,
        button {
            padding: 10px 12px;
            border-radius: 10px;
            border: none;
            font-family: inherit;
            font-weight: 500;
        }

        select {
            background: #1f1f1f;
            color: #fff;
            border: 1px solid rgba(255, 105, 180, 0.4);
        }

        button {
            background: linear-gradient(135deg, #ff1493, #ff69b4);
            color: #fff;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(255, 20, 147, 0.35);
        }

        .whatsapp-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            color: #25d366;
            text-decoration: none;
            font-weight: 600;
        }

        .notice {
            max-width: 1200px;
            margin: 0 auto 18px;
            padding: 12px 16px;
            border-radius: 12px;
            background: rgba(37, 211, 102, 0.12);
            color: #e8ffe8;
            border: 1px solid rgba(37, 211, 102, 0.4);
        }

        .back-link {
            display: inline-block;
            margin: 18px auto 0;
            text-decoration: none;
            color: #fff;
            background: #ff1493;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
        }

        .empty {
            text-align: center;
            padding: 24px;
            color: #cfcfcf;
            background: rgba(17, 17, 17, 0.7);
            border-radius: 12px;
        }
    </style>
</head>
<body>
<h1>📦 Seguimiento de pedidos</h1>
<p class="subtitulo">Administra el estado de los pedidos y envía notificaciones por WhatsApp cuando estén en camino o entregados.</p>

<?php if ($notificacion_whatsapp): ?>
    <div class="notice">
        Notificación lista para <strong><?= htmlspecialchars($notificacion_whatsapp['cliente']) ?></strong> (pedido #<?= intval($notificacion_whatsapp['orden_id']) ?>).
        <a class="whatsapp-link" href="<?= htmlspecialchars($notificacion_whatsapp['link']) ?>" target="_blank" rel="noopener">📲 Enviar WhatsApp</a>
    </div>
<?php endif; ?>

<div class="panel">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
                $estado_actual = $row['estado'] ?: 'pendiente';
                if ($estado_actual === 'pagada') {
                    $estado_actual = 'pagado';
                }
                $etiqueta = $estado_labels[$estado_actual] ?? $estado_actual;
                $paso_actual = $estado_index[$estado_actual] ?? -1;
                $telefono = preg_replace('/[^0-9]/', '', $row['telefono'] ?? '');
                if (!empty($telefono) && strlen($telefono) === 10 && str_starts_with($telefono, '3')) {
                    $telefono = '57' . $telefono;
                }
                $link_whatsapp = null;
                if (!empty($telefono) && in_array($estado_actual, ['enviado', 'entregado'], true)) {
                    $nombre_cliente = trim($row['nombre'] . ' ' . $row['apellido']);
                    $mensaje = "Hola {$nombre_cliente}, tu pedido #{$row['id_orden']} ahora está {$etiqueta}. ¡Gracias por comprar con Belleza y Glamour Angelita!";
                    $link_whatsapp = 'https://wa.me/' . $telefono . '?text=' . urlencode($mensaje);
                }
            ?>
            <article class="card">
                <div class="card-header">
                    <div>
                        <h3>Pedido #<?= intval($row['id_orden']) ?> - <?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) ?></h3>
                        <div class="meta"><?= htmlspecialchars($row['email']) ?> · <?= htmlspecialchars($row['telefono'] ?? 'Sin teléfono') ?></div>
                        <div class="meta">Fecha: <?= htmlspecialchars($row['fecha_compra']) ?> · Total: $<?= number_format((float) $row['total'], 2) ?></div>
                        <div class="meta">Entrega: <?= htmlspecialchars($row['metodo_envio'] ?? 'Sin definir') ?> · Envío: $<?= number_format((float) ($row['costo_envio'] ?? 0), 2) ?></div>
                        <?php if (!empty($row['direccion'])): ?>
                            <div class="meta">Dirección: <?= htmlspecialchars($row['direccion'] . ', ' . $row['ciudad'] . ' (' . $row['departamento'] . ')') ?></div>
                        <?php endif; ?>
                    </div>
                    <span class="status-pill"><?= htmlspecialchars($etiqueta) ?></span>
                </div>

                <?php if ($estado_actual === 'cancelado'): ?>
                    <p class="meta">Pedido cancelado. No se enviarán notificaciones.</p>
                <?php else: ?>
                    <div class="track">
                        <?php foreach ($estado_steps as $index => $step): ?>
                            <div class="track-step <?= $index <= $paso_actual ? 'active' : '' ?>">
                                <?= htmlspecialchars($estado_labels[$step]) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="accion" value="estado">
                    <input type="hidden" name="orden_id" value="<?= intval($row['id_orden']) ?>">
                    <label for="estado-<?= intval($row['id_orden']) ?>" class="meta">Actualizar estado:</label>
                    <select name="estado" id="estado-<?= intval($row['id_orden']) ?>">
                        <?php foreach ($estado_labels as $valor => $texto): ?>
                            <option value="<?= htmlspecialchars($valor) ?>" <?= $valor === $estado_actual ? 'selected' : '' ?>>
                                <?= htmlspecialchars($texto) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Guardar</button>
                </form>

                <?php if (($row['metodo_envio'] ?? '') === 'Envío a domicilio' && $tiene_costo_envio): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="accion" value="envio">
                        <input type="hidden" name="orden_id" value="<?= intval($row['id_orden']) ?>">
                        <label for="costo-envio-<?= intval($row['id_orden']) ?>" class="meta">Costo de envío:</label>
                        <input type="number" step="0.01" min="0" name="costo_envio" id="costo-envio-<?= intval($row['id_orden']) ?>" value="<?= htmlspecialchars((float) ($row['costo_envio'] ?? 0)) ?>">
                        <button type="submit">Actualizar envío</button>
                    </form>
                <?php endif; ?>

                <?php if ($link_whatsapp): ?>
                    <a class="whatsapp-link" href="<?= htmlspecialchars($link_whatsapp) ?>" target="_blank" rel="noopener">📲 Enviar WhatsApp al cliente</a>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty">No hay pedidos registrados.</div>
    <?php endif; ?>
</div>

<div style="text-align:center;">
    <a class="back-link" href="tienda.php">⬅ Volver a la tienda</a>
</div>
</body>
</html>
