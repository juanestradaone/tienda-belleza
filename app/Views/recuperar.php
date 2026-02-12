<?php
session_start();
require __DIR__ . '/../Config/conexion.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = trim($_POST['email'] ?? '');
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$message = 'Introduce un correo válido.';
	} else {
		// Crear tabla password_resets si no existe
		$conn->query("CREATE TABLE IF NOT EXISTS password_resets (
			id INT AUTO_INCREMENT PRIMARY KEY,
			email VARCHAR(255) NOT NULL,
			code VARCHAR(10) NOT NULL,
			expires_at DATETIME NOT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			INDEX (email)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

		// Generar código de 6 dígitos
		$code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
		$expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora

		// Eliminar códigos anteriores para ese email
		$del = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
		$del->bind_param('s', $email);
		$del->execute();

		// Insertar nuevo código
		$ins = $conn->prepare("INSERT INTO password_resets (email, code, expires_at) VALUES (?, ?, ?)");
		$ins->bind_param('sss', $email, $code, $expires);
		$ins->execute();

		// Construir enlace al formulario de verificación (ajusta si tu sitio está en una subcarpeta)
		$host = $_SERVER['HTTP_HOST'];
		$base = rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
		$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $host . $base . '/reset_password.php?email=' . urlencode($email);

		$subject = 'Recuperación de contraseña';
		$body = "Has solicitado recuperar tu contraseña.\n\nCódigo de verificación: $code\n\nVisita el siguiente enlace para introducir el código y restablecer tu contraseña:\n$link\n\nEste código expirará en 1 hora.";
		$headers = "From: no-reply@$host\r\n" .
				   "Reply-To: no-reply@$host\r\n" .
				   "Content-Type: text/plain; charset=utf-8\r\n";

		$sent = @mail($email, $subject, $body, $headers);

		if ($sent) {
			$message = 'Hemos enviado un correo con un código de verificación. Revisa tu bandeja (y la carpeta de spam).';
		} else {
			// En entornos locales sin SMTP mostrar el código y enlace para pruebas
			$message = "No se pudo enviar el correo desde este servidor.\nCódigo: $code\nEnlace: $link";
		}
	}
}

?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Recuperar contraseña</title>
	<link rel="stylesheet" href="/style.css">
	<style>
		.rec-card { max-width:420px; margin:48px auto; background:#0b0b0b; padding:22px; border-radius:12px; border:1px solid rgba(255,20,147,0.06); }
		.rec-card h2 { color:#ff69b4; margin-bottom:10px }
		.rec-card input{ width:100%; padding:10px; margin-bottom:10px; border-radius:8px; border:1px solid #222; background:#090909; color:#fff }
		.rec-card button{ background:linear-gradient(90deg,#ff0080,#ff69b4); color:#fff; border:none; padding:10px 14px; border-radius:8px; cursor:pointer }
		.rec-card p.msg{ color:#fff; white-space:pre-line }
	</style>
</head>
<body>
	<div class="rec-card">
		<h2>Recuperar contraseña</h2>
		<?php if ($message): ?>
			<p class="msg"><?php echo htmlspecialchars($message); ?></p>
		<?php endif; ?>

		<form method="POST">
			<label>Tu correo electrónico</label>
			<input type="email" name="email" required placeholder="usuario@ejemplo.com">
			<button type="submit">Enviar código</button>
		</form>
	</div>
</body>
</html>

