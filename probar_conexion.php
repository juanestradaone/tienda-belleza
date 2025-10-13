<?php
include("conexion.php");

if ($conn) {
    echo "✅ Conectado correctamente a la base de datos: " . $conn->host_info;
} else {
    echo "❌ No se pudo conectar a la base de datos.";
}
?>
