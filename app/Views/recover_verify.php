<?php
require_once "../Config/conexion.php";
session_start();

$codigo = trim($_POST['codigo'] ?? '');

if (empty($codigo)) {
    exit("ERROR");
}

$stmt = $conn->prepare("
    SELECT email, codigo_expira 
    FROM usuarios 
    WHERE codigo_recuperacion = ?
");
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit("ERROR");
}

$data = $result->fetch_assoc();

if (strtotime($data['codigo_expira']) < time()) {
    exit("ERROR");
}

$_SESSION['email_recuperacion'] = $data['email'];

echo "OK";
?>