<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?msg=⚠️ Solicitud no válida');
    exit;
}

$accion = $_POST['accion'] ?? '';

if ($accion === 'solicitar') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: index.php?msg=⚠️ Correo inválido#recuperar');
        exit;
    }

    $stmt = $conn->prepare('SELECT id_usuario FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header('Location: index.php?msg=⚠️ Correo no encontrado#recuperar');
        exit;
    }

    $codigo = (string) random_int(100000, 999999);
    $codigoHash = password_hash($codigo, PASSWORD_DEFAULT);

    $_SESSION['password_reset'][$email] = [
        'codigo_hash' => $codigoHash,
        'expira_en' => time() + (10 * 60),
    ];

    $subject = 'Código para recuperar tu contraseña';
    $mensaje = "Hola,\n\nTu código de recuperación es: {$codigo}\n\nEste código vence en 10 minutos.\nSi no solicitaste este cambio, ignora este correo.";
    $headers = "From: no-reply@tienda-belleza.com\r\n" .
               "Reply-To: no-reply@tienda-belleza.com\r\n" .
               "Content-Type: text/plain; charset=UTF-8\r\n";

    if (@mail($email, $subject, $mensaje, $headers)) {
        header('Location: index.php?msg=✅ Código enviado al correo#recuperar');
        exit;
    }

    unset($_SESSION['password_reset'][$email]);
    header('Location: index.php?msg=❌ No se pudo enviar el correo. Verifica la configuración del servidor.#recuperar');
    exit;
}

if ($accion === 'restablecer') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $codigo = preg_replace('/\D/', '', $_POST['codigo'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: index.php?msg=⚠️ Correo inválido#recuperar');
        exit;
    }

    if (!preg_match('/^\d{6}$/', $codigo)) {
        header('Location: index.php?msg=⚠️ El código debe tener 6 dígitos#recuperar');
        exit;
    }

    if (strlen($password) < 6) {
        header('Location: index.php?msg=⚠️ La nueva contraseña debe tener al menos 6 caracteres#recuperar');
        exit;
    }

    if ($password !== $passwordConfirm) {
        header('Location: index.php?msg=⚠️ Las contraseñas no coinciden#recuperar');
        exit;
    }

    $resetData = $_SESSION['password_reset'][$email] ?? null;

    if (!$resetData) {
        header('Location: index.php?msg=⚠️ Primero debes solicitar un código#recuperar');
        exit;
    }

    if (($resetData['expira_en'] ?? 0) < time()) {
        unset($_SESSION['password_reset'][$email]);
        header('Location: index.php?msg=⚠️ El código expiró, solicita uno nuevo#recuperar');
        exit;
    }

    if (!password_verify($codigo, $resetData['codigo_hash'])) {
        header('Location: index.php?msg=❌ Código incorrecto#recuperar');
        exit;
    }

    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $update = $conn->prepare('UPDATE usuarios SET contrasena = ? WHERE email = ?');
    $update->bind_param('ss', $newHash, $email);

    if ($update->execute() && $update->affected_rows > 0) {
        unset($_SESSION['password_reset'][$email]);
        header('Location: index.php?msg=✅ Contraseña restablecida. Ya puedes iniciar sesión#login-form');
        exit;
    }

    header('Location: index.php?msg=❌ No se pudo actualizar la contraseña#recuperar');
    exit;
}

header('Location: index.php?msg=⚠️ Acción no válida#recuperar');
exit;
