<?php
require __DIR__ . '/vendor/autoload.php';

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

MercadoPagoConfig::setAccessToken("TU_ACCESS_TOKEN_AQUI");

try {

    $client = new PreferenceClient();

    $preference = $client->create([
        "items" => [
            [
                "title" => "Producto de ejemplo",
                "quantity" => 1,
                "currency_id" => "COP",
                "unit_price" => 50000
            ]
        ]
    ]);

    echo "<h2>Preferencia creada</h2>";
    echo "<pre>";
    print_r($preference);
    echo "</pre>";

} catch (Exception $e) {

    echo "<h2 style='color:red'>⚠️ ERROR DETALLADO DE MERCADO PAGO</h2>";

    echo "<pre>";
    print_r($e->getMessage());
    echo "</pre>";

    echo "<h3>🔎 Información del error devuelta por la API:</h3>";
    echo "<pre>";
    print_r($e);
    echo "</pre>";
}
