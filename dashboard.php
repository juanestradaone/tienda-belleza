<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.html");
    exit();
}
echo "Hola, " . $_SESSION['nombre'] . ". Has iniciado sesión como " . $_SESSION['rol'];
?>
