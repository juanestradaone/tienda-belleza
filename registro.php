<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre    = $_POST['nombre'];
    $apellido  = $_POST['apellido'];
    $direccion = $_POST['direccion'];
    $email     = $_POST['email'];
    $telefono  = $_POST['telefono'];
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Verificar si el correo ya existe
    $checkEmail = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        echo "<script>
                alert('⚠️ Este correo ya está registrado.');
                window.location.href = 'index.php';
              </script>";
    } else {
        // Insertar usuario
        $sql = $conn->prepare("INSERT INTO usuarios (nombre, apellido, direccion, email, telefono, contrasena) VALUES (?, ?, ?, ?, ?, ?)");
        $sql->bind_param("ssssss", $nombre, $apellido, $direccion, $email, $telefono, $password);

        if ($sql->execute()) {
            echo "<script>
                    alert('✅ Usuario registrado con éxito. Ahora puedes iniciar sesión.');
                    window.location.href = 'index.php';
                  </script>";
        } else {
            echo "<script>
                    alert('❌ Error al registrar el usuario. Inténtalo de nuevo.');
                    window.location.href = 'index.php';
                  </script>";
        }

        $sql->close();
    }

    $checkEmail->close();
    $conn->close();
} else {
    echo "⚠️ No se recibieron datos del formulario.";
}
?>
