<?php
require_once "../Config/conexion.php";

$token = $_GET['token'] ?? '';

if (!$token) {
    exit("Token inválido.");
}

$stmt = $conn->prepare("
    SELECT email, expires_at 
    FROM password_resets 
    WHERE token = ?
");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit("Token inválido.");
}

$data = $result->fetch_assoc();

if (strtotime($data['expires_at']) < time()) {
    exit("El enlace ha expirado.");
}
?>

<form method="POST" action="recover_update.php">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    <input type="password" name="password" placeholder="Nueva contraseña" required>
    <button type="submit">Cambiar contraseña</button>
</form>