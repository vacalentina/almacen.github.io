<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $con = new mysqli("localhost", "root", "diana76", "almacen", 3307);
    if ($con->connect_error) {
        die("Conexión fallida: " . $con->connect_error);
    }

    $stmt = $con->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "El nombre de usuario ya está en uso.";
    } else {
        $stmt = $con->prepare("INSERT INTO usuarios (usuario, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $usuario, $password_hash);
        if ($stmt->execute()) {
            echo "Usuario registrado exitosamente. <a href='login.php'>Iniciar sesión</a>";
        } else {
            echo "Error al registrar usuario.";
        }
    }

    $stmt->close();
    $con->close();
}
?>
