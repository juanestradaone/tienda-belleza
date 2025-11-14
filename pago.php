<?php
require __DIR__ . '/mercadopago/vendor/autoload.php';

MercadoPago\SDK::setAccessToken("TU_ACCESS_TOKEN_AQUI"); // <-- pon aquí tu token real

$preference = new MercadoPago\Preference();

// Producto
$item = new MercadoPago\Item();
$item->title = "Shampoo Profesional";   // Nombre del producto
$item->quantity = 1;                    // Cantidad
$item->unit_price = 35000;              // Precio en pesos colombianos
$preference->items = array($item);

// URLs a donde redirige después del pago
$preference->back_urls = array(
    "success" => "http://localhost/tienda/exito.php",
    "failure" => "http://localhost/tienda/error.php",
    "pending" => "http://localhost/tienda/pendiente.php"
);
$preference->auto_return = "approved"; // regresa automáticamente si se aprueba el pago

$preference->save();
?>

<h2>Compra tu producto</h2>
<a href="<?php echo $preference->init_point; ?>">
  <button>Pagar con Mercado Pago</button>
</a>
