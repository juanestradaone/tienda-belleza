<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

// Verificar usuario
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}
$id_usuario = $_SESSION['usuario'];

/*
  PROCESAR REPETIR PEDIDO:
  Solo procesamos cuando llegue POST con action=repeat y confirm=1 (confirmación desde modal).
  Después de procesar redirigimos a carrito.php?repetido=1
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'repeat' && isset($_POST['confirm']) && $_POST['confirm'] == '1') {
    $order_id = intval($_POST['order_id']);

    // 1) Buscar id_carrito original de la orden
    $sql = "SELECT id_carrito FROM ordenes WHERE id_orden = ? LIMIT 1";
    $stm = $conn->prepare($sql);
    $stm->bind_param("i", $order_id);
    $stm->execute();
    $stm->bind_result($original_carrito);
    $stm->fetch();
    $stm->close();

    if (!$original_carrito) {
        // No existe la orden: redirigimos con error (puedes adaptar)
        header("Location: historial_pedidos.php?repetido=0&error=1");
        exit;
    }

    // 2) Obtener items del carrito original (detalle_carrito)
    $sql = "
        SELECT dc.id_producto, dc.cantidad, dc.precio_unitario
        FROM detalle_carrito dc
        WHERE dc.id_carrito = ?
    ";
    $stm = $conn->prepare($sql);
    $stm->bind_param("i", $original_carrito);
    $stm->execute();
    $resItems = $stm->get_result();
    $items = $resItems->fetch_all(MYSQLI_ASSOC);
    $stm->close();

    if (empty($items)) {
        header("Location: historial_pedidos.php?repetido=0&empty=1");
        exit;
    }

    // 3) Obtener/crear carrito activo del usuario
    $sql = "SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo' LIMIT 1";
    $stm = $conn->prepare($sql);
    $stm->bind_param("i", $id_usuario);
    $stm->execute();
    $stm->bind_result($active_carrito);
    $stm->fetch();
    $stm->close();

    if (!$active_carrito) {
        $sql = "INSERT INTO carrito (id_usuario, estado) VALUES (?, 'activo')";
        $stm = $conn->prepare($sql);
        $stm->bind_param("i", $id_usuario);
        $stm->execute();
        $active_carrito = $stm->insert_id;
        $stm->close();
    }

    // 4) Insertar/actualizar items en carrito activo
    $fail = false;
    foreach ($items as $it) {
        $pid = intval($it['id_producto']);
        $qty = intval($it['cantidad']);
        $price_unit = $it['precio_unitario'] !== null ? floatval($it['precio_unitario']) : null;

        // Verificar si ya existe el producto en el carrito activo
        $sql = "SELECT id_detalle, cantidad FROM detalle_carrito WHERE id_carrito = ? AND id_producto = ? LIMIT 1";
        $stm = $conn->prepare($sql);
        $stm->bind_param("ii", $active_carrito, $pid);
        $stm->execute();
        $stm->bind_result($existing_detalle, $existing_qty);
        $stm->fetch();
        $stm->close();

        if ($existing_detalle) {
            $new_qty = $existing_qty + $qty;
            // Actualizar cantidad y precio_unitario solo si tenemos precio_unit
            if ($price_unit !== null) {
                $sql = "UPDATE detalle_carrito SET cantidad = ?, precio_unitario = ? WHERE id_detalle = ?";
                $stm = $conn->prepare($sql);
                $stm->bind_param("dii", $new_qty, $price_unit, $existing_detalle);
            } else {
                $sql = "UPDATE detalle_carrito SET cantidad = ? WHERE id_detalle = ?";
                $stm = $conn->prepare($sql);
                $stm->bind_param("ii", $new_qty, $existing_detalle);
            }
            if (!$stm->execute()) $fail = true;
            $stm->close();
        } else {
            // Insertar nuevo detalle. Si price_unit es null, insertamos 0.0 y luego actualizamos a NULL.
            $sql = "INSERT INTO detalle_carrito (id_carrito, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
            $stm = $conn->prepare($sql);
            $price_insert = ($price_unit === null) ? 0.0 : $price_unit;
            $stm->bind_param("iiid", $active_carrito, $pid, $qty, $price_insert);
            if (!$stm->execute()) $fail = true;
            $last_id = $conn->insert_id;
            $stm->close();

            if ($price_unit === null) {
                $sql = "UPDATE detalle_carrito SET precio_unitario = NULL WHERE id_detalle = ?";
                $stm = $conn->prepare($sql);
                $stm->bind_param("i", $last_id);
                $stm->execute();
                $stm->close();
            }
        }
    }

    if ($fail) {
        header("Location: historial_pedidos.php?repetido=0&error=1");
        exit;
    } else {
        // Éxito: redirigir al carrito para ver los items añadidos
        header("Location: carrito.php?repetido=1");
        exit;
    }
}

// Si no es POST de confirmación, seguimos mostrando la página normalmente

// Obtener todas las órdenes del usuario (lista)
$sql = "
    SELECT id_orden, fecha_compra, total, estado, id_carrito
    FROM ordenes
    WHERE id_usuario = ?
    ORDER BY fecha_compra DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$ordenes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Historial de Pedidos</title>
<meta name="viewport" content="width=device-width,initial-scale=1">

<style>
/* Estilo negro + rosa neón */
:root{
    --neon:#ff1493;
    --neon-2:#ff69b4;
    --bg:#0a0a0a;
    --card:#111;
    --muted:#cfcfcf;
}
*{box-sizing:border-box}
body{
    margin:0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #0a0a0a 0%, #1b1b1b 50%, #2b2b2b 100%);
    color:#f5f5f5;
    min-height:100vh;
    padding:28px 16px;
}
.container{
    max-width:1100px;
    margin:0 auto;
}
h1{
    text-align:center;
    color:var(--neon-2);
    font-size:2.2rem;
    text-shadow:0 0 10px var(--neon);
    margin-bottom:18px;
}
.order-card{
    background: rgba(17,17,17,0.95);
    border:1px solid rgba(255,20,147,0.12);
    box-shadow:0 10px 30px rgba(255,20,147,0.03);
    margin: 18px 0;
    border-radius:12px;
    overflow:hidden;
}
.order-head{
    display:flex;
    gap:16px;
    align-items:center;
    padding:18px;
    border-bottom:1px solid rgba(255,20,147,0.03);
}
.order-meta{
    flex:1;
}
.order-meta h2{
    margin:0;
    color:var(--neon-2);
    font-size:1.15rem;
    text-shadow:0 0 6px rgba(255,105,180,0.12);
}
.order-meta p{ margin:6px 0; color:var(--muted); font-size:0.95rem; }
.order-actions{
    display:flex;
    gap:10px;
    align-items:center;
}
.btn{
    background:linear-gradient(135deg,var(--neon),var(--neon-2));
    color:#fff;
    padding:10px 14px;
    border-radius:10px;
    text-decoration:none;
    font-weight:700;
    border:2px solid transparent;
    cursor:pointer;
    transition:all .18s ease;
}
.btn:hover{ transform:translateY(-3px); box-shadow:0 6px 20px rgba(255,105,180,0.12); background:transparent; color:var(--neon-2); border-color:var(--neon-2); }
.btn.ghost{ background:transparent; color:var(--neon-2); border:1px solid rgba(255,20,147,0.12); padding:8px 12px; font-weight:600; }

.order-body{
    padding:14px 18px 20px;
}
.products-grid{
    display:grid;
    grid-template-columns: 80px 1fr 110px;
    gap:12px;
    align-items:center;
    padding:8px 0;
    border-bottom:1px dashed rgba(255,20,147,0.03);
}
.prod-img{
    width:80px; height:80px; border-radius:8px; overflow:hidden; background:#222; display:flex; align-items:center; justify-content:center;
}
.prod-img img{ width:100%; height:100%; object-fit:cover; display:block; }
.prod-title{ color:#fff; font-weight:700; }
.prod-meta{ color:var(--muted); font-size:0.95rem; margin-top:6px; }
.prod-price{ text-align:right; color:var(--neon-2); font-weight:800; font-size:1rem; }

.order-footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px 18px 18px;
}
.total-left{ color:var(--muted); font-weight:600; }
.total-right{ color:var(--neon-2); font-weight:900; font-size:1.15rem; }

.empty{
    text-align:center;
    color:var(--muted);
    padding:40px 0;
    border:2px dashed rgba(255,20,147,0.06);
    border-radius:12px;
}

.details-toggle{
    background:transparent; border:0; color:var(--neon-2); cursor:pointer; font-weight:700;
}

/* Modal */
.modal-backdrop{
    position:fixed; inset:0; background:rgba(0,0,0,0.6); display:none; align-items:center; justify-content:center; z-index:9999;
}
.modal{
    background:linear-gradient(180deg,#111,#0d0d0d);
    border:2px solid rgba(255,20,147,0.12);
    padding:18px;
    border-radius:12px;
    width:94%;
    max-width:520px;
    box-shadow:0 20px 60px rgba(0,0,0,0.6);
}
.modal h3{ margin:0 0 10px; color:var(--neon-2); }
.modal p{ color:var(--muted); margin-bottom:14px; }
.modal .actions{ display:flex; gap:10px; justify-content:flex-end; }

/* Responsive */
@media (max-width:720px){
    .products-grid{ grid-template-columns: 70px 1fr 90px; }
    .order-head{ flex-direction:column; align-items:flex-start; gap:8px; }
    .order-actions{ width:100%; justify-content:space-between; }
}
</style>

</head>
<body>
<div class="container">
    <h1>Historial de Pedidos</h1>

    <?php if (empty($ordenes)): ?>
        <div class="empty">
            <p>No tienes pedidos registrados aún.</p>
            <p><a href="index.php" class="btn">Volver a la tienda</a></p>
        </div>
    <?php else: ?>

        <?php foreach ($ordenes as $orden): ?>
            <?php
                // Obtener productos guardados en el detalle oficial de la orden
                $sqlProd = "
                    SELECT do.id_detalle, do.id_producto, do.cantidad, do.precio_unitario,
                           p.nombre_producto, p.precio_producto, p.imagen
                    FROM detalle_orden do
                    LEFT JOIN productos p ON do.id_producto = p.id_producto
                    WHERE do.id_orden = ?
                ";
                $stmtP = $conn->prepare($sqlProd);
                $idOrden = intval($orden['id_orden']);
                $stmtP->bind_param("i", $idOrden);
                $stmtP->execute();
                $resP = $stmtP->get_result();
                $productos = $resP ? $resP->fetch_all(MYSQLI_ASSOC) : [];
                $stmtP->close();

                // Compatibilidad con órdenes antiguas sin detalle_orden.
                // Solo usamos detalle_carrito cuando ese carrito pertenece
                // de forma única a esta orden para evitar mostrar productos
                // repetidos o incorrectos entre pedidos diferentes.
                $carritoUnico = false;
                if (empty($productos) && !empty($orden['id_carrito'])) {
                    $idCarrito = intval($orden['id_carrito']);

                    $sqlCarritoUnico = "SELECT COUNT(*) AS total_ordenes FROM ordenes WHERE id_carrito = ?";
                    $stmtCarritoUnico = $conn->prepare($sqlCarritoUnico);
                    $stmtCarritoUnico->bind_param("i", $idCarrito);
                    $stmtCarritoUnico->execute();
                    $resCarritoUnico = $stmtCarritoUnico->get_result();
                    $carritoUnico = ($resCarritoUnico && ($rowCarritoUnico = $resCarritoUnico->fetch_assoc()))
                        ? intval($rowCarritoUnico['total_ordenes']) === 1
                        : false;
                    $stmtCarritoUnico->close();

                    if (!$carritoUnico) {
                        $productos = [];
                    }
                }

                if (empty($productos) && !empty($orden['id_carrito']) && $carritoUnico) {
                    $sqlProdCarrito = "
                        SELECT dc.id_detalle, dc.id_producto, dc.cantidad, dc.precio_unitario,
                               p.nombre_producto, p.precio_producto, p.imagen
                        FROM detalle_carrito dc
                        LEFT JOIN productos p ON dc.id_producto = p.id_producto
                        WHERE dc.id_carrito = ?
                    ";
                    $stmtPCarrito = $conn->prepare($sqlProdCarrito);
                    $stmtPCarrito->bind_param("i", $idCarrito);
                    $stmtPCarrito->execute();
                    $resPCarrito = $stmtPCarrito->get_result();
                    $productos = $resPCarrito ? $resPCarrito->fetch_all(MYSQLI_ASSOC) : [];
                    $stmtPCarrito->close();
                }

                // calcular total mostrado (en caso quieras mostrar por seguridad)
                $calc_total = 0;
                foreach ($productos as $pp) {
                    $unit = $pp['precio_unitario'] !== null ? floatval($pp['precio_unitario']) : floatval($pp['precio_producto']);
                    $calc_total += $unit * intval($pp['cantidad']);
                }
            ?>
            <div class="order-card">
                <div class="order-head">
                    <div class="order-meta">
                        <h2>Pedido #<?= htmlspecialchars($orden['id_orden']) ?></h2>
                        <p>Fecha: <?= htmlspecialchars($orden['fecha_compra']) ?></p>
                        <p>Estado: <?= htmlspecialchars($orden['estado']) ?></p>
                    </div>

                    <div class="order-actions">
                        <div style="text-align:right">
                            <p style="margin:0; color:var(--muted);">Total registro</p>
                            <p style="margin:6px 0 0; color:var(--neon-2); font-weight:900;">
                                $<?= number_format(floatval($orden['total']), 0, ",", ".") ?>
                            </p>
                        </div>

                        <div>
                            <button class="btn repeat-btn" data-order-id="<?= intval($orden['id_orden']) ?>">Repetir pedido</button>
                            <button class="btn ghost details-toggle" data-target="details-<?= $orden['id_orden'] ?>">Ver detalles</button>
                        </div>
                    </div>
                </div>

                <div id="details-<?= $orden['id_orden'] ?>" class="order-body" style="display:none;">
                    <?php if (empty($productos)): ?>
                        <p class="prod-meta">No hay productos registrados para este pedido.</p>
                    <?php else: ?>
                        <?php foreach ($productos as $p): ?>
                            <?php
                                $unit = $p['precio_unitario'] !== null ? floatval($p['precio_unitario']) : floatval($p['precio_producto']);
                                $subtotal = $unit * intval($p['cantidad']);
                                $img = $p['imagen'] ? 'uploads/' . htmlspecialchars($p['imagen']) : null;
                            ?>
                            <div class="products-grid">
                                <div class="prod-img">
                                    <?php if ($img): ?>
                                        <img src="<?= $img ?>" alt="<?= htmlspecialchars($p['nombre_producto']) ?>" onerror="this.style.display='none'">
                                    <?php else: ?>
                                        <div style="color:var(--muted); font-size:12px;">Sin imagen</div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="prod-title"><?= htmlspecialchars($p['nombre_producto'] ?? 'Producto') ?></div>
                                    <div class="prod-meta">Cantidad: <?= intval($p['cantidad']) ?></div>
                                </div>
                                <div class="prod-price">
                                    $<?= number_format($subtotal, 0, ",", ".") ?><br>
                                    <small style="color:var(--muted); font-weight:600;">(u. $<?= number_format($unit, 0, ",", ".") ?>)</small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="order-footer">
                        <div class="total-left">Total calculado de items: </div>
                        <div class="total-right">$<?= number_format($calc_total, 0, ",", ".") ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

    <div style="text-align:center; margin-top:20px;">
        <a href="index.php" class="btn">Volver a la tienda</a>
    </div>
</div>

<!-- Modal de confirmación -->
<div id="modal-backdrop" class="modal-backdrop" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <h3 id="modal-title">Confirmar repetición de pedido</h3>
        <p id="modal-text">¿Deseas repetir el pedido <strong id="modal-order"></strong>? Los productos se añadirán a tu carrito activo.</p>

        <div class="actions">
            <button id="modal-cancel" class="btn ghost">Cancelar</button>
            <form id="modal-form" method="post" style="display:inline">
                <input type="hidden" name="action" value="repeat">
                <input type="hidden" name="order_id" id="modal-order-id" value="">
                <input type="hidden" name="confirm" value="1">
                <button type="submit" class="btn">Confirmar</button>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle detalles
document.querySelectorAll('.details-toggle').forEach(btn=>{
    btn.addEventListener('click', e=>{
        const id = btn.getAttribute('data-target');
        const el = document.getElementById(id);
        if (!el) return;
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
        if (el.style.display === 'block') el.scrollIntoView({behavior:'smooth', block:'center'});
    });
});

// Modal logic
const modalBackdrop = document.getElementById('modal-backdrop');
const modalOrderText = document.getElementById('modal-order');
const modalOrderId = document.getElementById('modal-order-id');
document.querySelectorAll('.repeat-btn').forEach(btn=>{
    btn.addEventListener('click', e=>{
        const orderId = btn.getAttribute('data-order-id');
        modalOrderText.textContent = "#" + orderId;
        modalOrderId.value = orderId;
        modalBackdrop.style.display = 'flex';
        modalBackdrop.setAttribute('aria-hidden', 'false');
    });
});
document.getElementById('modal-cancel').addEventListener('click', e=>{
    modalBackdrop.style.display = 'none';
    modalBackdrop.setAttribute('aria-hidden', 'true');
});

// cerrar con escape o click fuera del modal
window.addEventListener('keydown', e=>{
    if (e.key === 'Escape') {
        modalBackdrop.style.display = 'none';
        modalBackdrop.setAttribute('aria-hidden', 'true');
    }
});
modalBackdrop.addEventListener('click', e=>{
    if (e.target === modalBackdrop) {
        modalBackdrop.style.display = 'none';
        modalBackdrop.setAttribute('aria-hidden', 'true');
    }
});
</script>

</body>
</html>
