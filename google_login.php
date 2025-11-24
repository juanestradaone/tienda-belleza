<?php
$config = include("config_google.php");

// Parámetros de Google OAuth
$params = [
    "client_id" => $config["client_id"],
    "redirect_uri" => $config["redirect_uri"],
    "response_type" => "code",
    "scope" => "email profile",
    "access_type" => "online",
    "prompt" => "select_account"
];

// Crear URL de Google
$url = $config["auth_url"] . "?" . http_build_query($params);

// Redirigir a Google
header("Location: " . $url);
exit;
?>
