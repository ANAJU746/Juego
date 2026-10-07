<?php
// registro.php (Este es el Controlador que va en tu carpeta principal)
require_once __DIR__ . '/../config/conexion.php';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol']; 

    try {
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, password_hash, rol) VALUES (:nombre, :correo, :password, :rol)");
        $stmt->execute([
            ':nombre' => $nombre, 
            ':correo' => $correo, 
            ':password' => $password,
            ':rol' => $rol
        ]);
        
        header("Location: login.php?exito=1");
        exit;
    } catch (PDOException $e) {
        $mensaje = "Hubo un error. Es posible que el correo ya esté registrado.";
    }
}

// ESTA LÍNEA ES LA MAGIA: Llama a tu nuevo diseño con Glassmorphism, fondo y Jaguar
require_once __DIR__ . '/../vistas/auth/registro_vista.php';
?>