<?php
session_start();
require 'conexion.php';

if (!isset($_POST['credential'])) {
    die("Error: no se recibió la credencial de Google");
}

// Decodificar token JWT de Google
$jwt = explode(".", $_POST['credential']);
$userData = json_decode(base64_decode($jwt[1]), true);

$google_id = $userData["sub"];
$nombre = $userData["name"];
$email = $userData["email"];
$foto = $userData["picture"];

// Verificar si el usuario ya existe
$stmt = $conn->prepare("SELECT id_usuario, nombre, email, foto FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    // Usuario existente → iniciar sesión
    $row = $result->fetch_assoc();
    $_SESSION["usuario"] = $row["id_usuario"];
    $_SESSION["nombre"]  = $row["nombre"];
    $_SESSION["email"]   = $row["email"];
    $_SESSION["foto"]    = $row["foto"];

} else {
    
    // Usuario nuevo → registrarlo
    $stmt = $conn->prepare(
        "INSERT INTO usuarios (nombre, email, google_id, foto, fecha_registro) 
         VALUES (?, ?, ?, ?, NOW())"
    );
    $stmt->bind_param("ssss", $nombre, $email, $google_id, $foto);
    $stmt->execute();

    $id_nuevo = $stmt->insert_id;

    $_SESSION["usuario"] = $id_nuevo;
    $_SESSION["nombre"]  = $nombre;
    $_SESSION["email"]   = $email;
    $_SESSION["foto"]    = $foto;
}

// Redirigir al home
header("Location: inicio.php");
exit;
?>
