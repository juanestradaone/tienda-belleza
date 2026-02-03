<?php
require __DIR__ . '/../Config/conexion.php';

$sql = "INSERT INTO usuarios (nombre, apellido, direccion, email, telefono, contrasena) 
        VALUES ('Juan', 'Estrada', 'Calle Falsa 123', 'juan@test.com', '123456789', '1234')";

if ($conn->query($sql) === TRUE) {
    echo "✅ Usuario insertado";
} else {
    echo "❌ Error: " . $conn->error;
}
?>
