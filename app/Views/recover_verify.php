<?php
require_once "config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST['email']) || empty($_POST['codigo'])) {
        exit("Datos incompletos.");
    }

    $email = trim($_POST['email']);
    $codigo = trim($_POST['codigo']);

    $stmt = $conexion->prepare("
        SELECT codigo_recuperacion, codigo_expira 
        FROM usuarios 
        WHERE email = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $usuario = $result->fetch_assoc();

        if (
            $usuario['codigo_recuperacion'] === $codigo &&
            strtotime($usuario['codigo_expira']) > time()
        ) {

            // Código correcto → redirige a cambiar contraseña
            header("Location: recover_reset.php?email=" . urlencode($email));
            exit();

        } else {
            echo "Codigo incorrecto o vencido.";
        }

    } else {
        echo "Correo no encontrado.";
    }
}
?>
