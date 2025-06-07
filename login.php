<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos ingresados
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Conectar a la base de datos
    $con = new mysqli("localhost", "root", "daniela_0312", "almacen", 3306);
    
    // Verificar si la conexión es exitosa
    if ($con->connect_error) {
        die("Conexión fallida: " . $con->connect_error);
    }

    // Consultar si el usuario y la contraseña coinciden
    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND password = '$password'";
    $result = $con->query($sql);

    if ($result->num_rows == 1) {
        // Si el usuario existe, iniciar sesión
        $_SESSION['usuario'] = $usuario;
        header("Location: dashboard.php");
        exit();
    } else {
        // Si el usuario no existe o la contraseña es incorrecta, mostrar mensaje de error
        $error_message = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Iniciar Sesión - Almacén Deportivo</h2>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="mx-auto" style="max-width: 400px;">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>

        <!-- Enlace para volver a intentar iniciar sesión -->
        <div class="text-center mt-3">
            <a href="index.html">Volver a intentar</a>
        </div>
    </div>
</body>
</html>
