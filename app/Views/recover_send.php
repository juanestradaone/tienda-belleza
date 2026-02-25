<?php
require_once "../Config/conexion.php";
require_once "../../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("ERROR");
}

$stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit("ERROR");
}

$codigo = rand(100000, 999999);
$expira = date("Y-m-d H:i:s", strtotime("+10 minutes"));

$update = $conn->prepare("
    UPDATE usuarios 
    SET codigo_recuperacion = ?, 
        codigo_expira = ?
    WHERE email = ?
");
$update->bind_param("sss", $codigo, $expira, $email);
$update->execute();

$mail = new PHPMailer(true);

$mail->CharSet = 'UTF-8';
$mail->Encoding = 'base64';

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'bellezayglamurangelita@gmail.com';
$mail->Password = 'ucsg gltf kizw ztdv';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('bellezayglamurangelita@gmail.com', 'Belleza y Glamour Angelita');
$mail->addAddress($email);
$mail->isHTML(true);
$mail->Subject = 'Recuperación de contraseña | Belleza y Glamour Angelita';

$mail->Body = "
<div style='
    max-width:500px;
    margin:auto;
    padding:30px;
    font-family:Arial, sans-serif;
    background:#fff0f5;
    border-radius:15px;
    text-align:center;
    border:1px solid #ffd6e7;
'>

    <h2 style='color:#d63384; margin-bottom:10px;'>
        Belleza y Glamour Angelita
    </h2>

    <p style='color:#555; font-size:14px;'>
        Hemos recibido una solicitud para restablecer tu contraseña.
    </p>

    <p style='margin-top:20px; font-size:16px; color:#333;'>
        Tu código de verificación es:
    </p>

    <div style='
        font-size:32px;
        font-weight:bold;
        letter-spacing:8px;
        color:#ff1493;
        margin:20px 0;
    '>
        $codigo
    </div>

    <p style='font-size:13px; color:#777;'>
        Este código expira en 10 minutos.
    </p>

    <hr style='margin:25px 0; border:none; border-top:1px solid #eee;'>

    <p style='font-size:12px; color:#999;'>
        Si no solicitaste este cambio, puedes ignorar este mensaje.
    </p>

</div>
";

$mail->send();

echo "OK";
?>