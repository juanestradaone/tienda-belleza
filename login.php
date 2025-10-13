<?php
session_start();
include("conexion.php");

// Activar errores para depurar si algo falla
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Consulta segura con prepare()
    $sql = "SELECT id_usuario, nombre, contrasena, rol FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['contrasena'])) {
            // Iniciar sesión
            $_SESSION['usuario'] = $user['id_usuario'];
            $_SESSION['nombre']  = $user['nombre'];
            $_SESSION['rol']     = $user['rol'];

            echo "<script>
                    alert('✅ Bienvenido {$user['nombre']}');
                    window.location.href = 'tienda.php';
                  </script>";
        } else {
            echo "<script>
                    alert('❌ Contraseña incorrecta');
                    window.location.href = 'index.html';
                  </script>";
        }
    } else {
        echo "<script>
                alert('❌ Usuario no encontrado');
                window.location.href = 'index.html';
              </script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "⚠️ No se recibieron datos del formulario.";
}
?>
