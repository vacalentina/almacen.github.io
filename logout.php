<?php
// Iniciar la sesión
session_start();

// Destruir la sesión
session_destroy();

// Redirigir a la página de inicio de sesión
header("Location: index.html");
exit();  // Asegura que no se ejecute más código después de la redirección
?>
