<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

if (!isset($_POST['credential'])) {
    die("Error: no se recibió la credencial de Google");
}

// Decodificar token JWT
$jwt = explode(".", $_POST['credential']);
$userData = json_decode(base64_decode($jwt[1]), true);

$google_id = $userData["sub"];
$nombre = $userData["name"];
$email = $userData["email"];
$foto = $userData["picture"]; // URL de Google

// Buscar usuario existente
$stmt = $conn->prepare("SELECT id_usuario, nombre, email, foto, rol FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    // Usuario existente
    $row = $result->fetch_assoc();

    $_SESSION["usuario"] = $row["id_usuario"];
    $_SESSION["nombre"]  = $row["nombre"];
    $_SESSION["email"]   = $row["email"];
    $_SESSION["rol"]     = $row["rol"];

    // ⚠ ACTUALIZAR FOTO SI ESTA VACÍA O CAMBIÓ
    if (empty($row["foto"]) || $row["foto"] !== $foto) {
        $stmt = $conn->prepare("UPDATE usuarios SET foto = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $foto, $row["id_usuario"]);
        $stmt->execute();
    }

    // Guardar la foto en la sesión SIEMPRE
    $_SESSION["foto"] = $foto;

} else {

    // Usuario nuevo
    $stmt = $conn->prepare("
        INSERT INTO usuarios (nombre, email, google_id, foto, fecha_registro)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("ssss", $nombre, $email, $google_id, $foto);
    $stmt->execute();

    $id_nuevo = $stmt->insert_id;

    // Guardar sesión
    $_SESSION["usuario"] = $id_nuevo;
    $_SESSION["nombre"]  = $nombre;
    $_SESSION["email"]   = $email;
    $_SESSION["rol"]     = "cliente";
    $_SESSION["foto"]    = $foto;
}

header("Location: bienvenido.php");
exit;
?>
