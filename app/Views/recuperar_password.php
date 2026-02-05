<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

function smtpConfig(): array
{
    return [
        'host' => getenv('SMTP_HOST') ?: '',
        'port' => (int) (getenv('SMTP_PORT') ?: 587),
        'username' => getenv('SMTP_USER') ?: '',
        'password' => getenv('SMTP_PASS') ?: '',
        'from_email' => getenv('SMTP_FROM_EMAIL') ?: (getenv('SMTP_USER') ?: ''),
        'from_name' => getenv('SMTP_FROM_NAME') ?: 'Belleza y Glamour Angelita',
        'secure' => getenv('SMTP_SECURE') ?: 'tls', // tls|ssl|none
    ];
}

function isDevelopmentEnvironment(): bool
{
    $appEnv = strtolower((string) getenv('APP_ENV'));

    if ($appEnv === '' || $appEnv === 'local' || $appEnv === 'dev' || $appEnv === 'development') {
        return true;
    }

    return false;
}

function smtpSend(string $toEmail, string $subject, string $body, ?string &$error = null): bool
{
    $config = smtpConfig();

    if ($config['host'] === '' || $config['username'] === '' || $config['password'] === '' || $config['from_email'] === '') {
        $error = 'SMTP incompleto (faltan credenciales).';
        return false;
    }

    $transport = $config['secure'] === 'ssl' ? 'ssl://' : '';
    $fp = @fsockopen($transport . $config['host'], $config['port'], $errno, $errstr, 15);

    if (!$fp) {
        $error = 'No se pudo conectar al SMTP: ' . $errstr . ' (' . $errno . ')';
        return false;
    }

    $read = function () use ($fp): string {
        $data = '';
        while ($line = fgets($fp, 515)) {
            $data .= $line;
            if (preg_match('/^\d{3}\s/', $line)) {
                break;
            }
        }

        return $data;
    };

    $send = function (string $command) use ($fp, $read): string {
        fwrite($fp, $command . "\r\n");
        return $read();
    };

    $resp = $read();
    if (!preg_match('/^220/', $resp)) {
        $error = 'SMTP respuesta inicial inválida: ' . trim($resp);
        fclose($fp);
        return false;
    }

    $resp = $send('EHLO localhost');
    if (!preg_match('/^250/', $resp)) {
        $error = 'EHLO rechazado: ' . trim($resp);
        fclose($fp);
        return false;
    }

    if ($config['secure'] === 'tls') {
        $resp = $send('STARTTLS');
        if (!preg_match('/^220/', $resp)) {
            $error = 'STARTTLS rechazado: ' . trim($resp);
            fclose($fp);
            return false;
        }

        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            $error = 'No se pudo negociar TLS con el servidor SMTP.';
            fclose($fp);
            return false;
        }

        $resp = $send('EHLO localhost');
        if (!preg_match('/^250/', $resp)) {
            $error = 'EHLO post-TLS rechazado: ' . trim($resp);
            fclose($fp);
            return false;
        }
    }

    $resp = $send('AUTH LOGIN');
    if (!preg_match('/^334/', $resp)) {
        $error = 'AUTH LOGIN rechazado: ' . trim($resp);
        fclose($fp);
        return false;
    }

    $resp = $send(base64_encode($config['username']));
    if (!preg_match('/^334/', $resp)) {
        $error = 'Usuario SMTP rechazado: ' . trim($resp);
        fclose($fp);
        return false;
    }

    $resp = $send(base64_encode($config['password']));
    if (!preg_match('/^235/', $resp)) {
        $error = 'Password SMTP rechazado: ' . trim($resp);
        fclose($fp);
        return false;
    }

    $resp = $send('MAIL FROM:<' . $config['from_email'] . '>');
    if (!preg_match('/^250/', $resp)) {
        $error = 'MAIL FROM rechazado: ' . trim($resp);
        fclose($fp);
        return false;
    }

    $resp = $send('RCPT TO:<' . $toEmail . '>');
    if (!preg_match('/^(250|251)/', $resp)) {
        $error = 'RCPT TO rechazado: ' . trim($resp);
        fclose($fp);
        return false;
    }

    $resp = $send('DATA');
    if (!preg_match('/^354/', $resp)) {
        $error = 'DATA rechazado: ' . trim($resp);
        fclose($fp);
        return false;
    }

    $headers = [
        'From: ' . $config['from_name'] . ' <' . $config['from_email'] . '>',
        'To: <' . $toEmail . '>',
        'Subject: ' . $subject,
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
    ];

    $messageData = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
    $resp = $send($messageData);

    $send('QUIT');
    fclose($fp);

    if (!preg_match('/^250/', $resp)) {
        $error = 'No se aceptó el contenido del correo: ' . trim($resp);
        return false;
    }

    return true;
}

function sendRecoveryCodeEmail(string $email, string $codigo): array
{
    $subject = 'Codigo para recuperar tu contrasena';
    $mensaje = "Hola,\n\nTu codigo de recuperacion es: {$codigo}\n\nEste codigo vence en 10 minutos.\nSi no solicitaste este cambio, ignora este correo.";

    $smtpError = '';
    if (smtpSend($email, $subject, $mensaje, $smtpError)) {
        return ['sent' => true, 'error' => ''];
    }

    $headers = "From: no-reply@tienda-belleza.com\r\n" .
               "Reply-To: no-reply@tienda-belleza.com\r\n" .
               "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($email, $subject, $mensaje, $headers)) {
        return ['sent' => true, 'error' => ''];
    }

    return ['sent' => false, 'error' => $smtpError !== '' ? $smtpError : 'mail() también falló.'];
}

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

    $sendResult = sendRecoveryCodeEmail($email, $codigo);

    if ($sendResult['sent']) {
        header('Location: index.php?msg=✅ Código enviado al correo#recuperar');
        exit;
    }

    error_log('[RECUPERAR_PASSWORD] Fallo envío de correo para ' . $email . ': ' . $sendResult['error']);

    if (isDevelopmentEnvironment()) {
        header('Location: index.php?msg=⚠️ No se pudo enviar correo en este servidor. Código temporal (solo desarrollo): ' . $codigo . '#recuperar');
        exit;
    }

    unset($_SESSION['password_reset'][$email]);
    header('Location: index.php?msg=❌ No se pudo enviar el correo. Configura SMTP_HOST, SMTP_USER y SMTP_PASS en el servidor.#recuperar');
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
