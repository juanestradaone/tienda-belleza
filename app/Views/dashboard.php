<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
echo "Hola, " . $_SESSION['nombre'] . ". Has iniciado sesión como " . $_SESSION['rol'];
?>


<br><br>
<a href="logout.php">Salir</a>