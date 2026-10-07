<?php
session_start();
session_unset();   // Libera todas las variables de sesión
session_destroy(); // Destruye la sesión por completo

// Redirigir al inicio de sesión
header("Location: login.php");
exit;
?>