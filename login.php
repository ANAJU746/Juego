<?php
session_start();
require_once 'config/conexion.php';
$error = '';
$mensaje_exito = '';

if (isset($_GET['exito'])) {
    $mensaje_exito = "¡Cuenta creada con éxito! Ahora puedes iniciar sesión.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = trim($_POST['correo']);
    $password_ingresada = $_POST['password'];

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = :correo");
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password_ingresada, $usuario['password_hash'])) {
        // Guardar datos en sesión
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre_usuario'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        // Enrutamiento automático
        if ($usuario['rol'] === 'maestro') {
            header("Location: panel_maestro.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}

// ESTA LÍNEA ES LA MAGIA: Llama a tu nuevo diseño
require_once 'vistas/auth/login_vista.php';
?>