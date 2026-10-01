<?php
// conexion.php
$host = 'localhost';
$port = '3307'; //
$dbname = 'arcade_sistemas';
$username = 'root'; // Usuario por defecto en XAMPP
$password = '';     // Contraseña por defecto en XAMPP (vacía)

try {
    // Añadimos "port=$port" a la cadena de conexión
    $conexion = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configurar PDO para que lance excepciones si hay errores
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si hay un error, detener todo y mostrar el mensaje
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>