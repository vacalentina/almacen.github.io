<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$con = new mysqli("localhost", "root", "diana76", "almacen", 3307);
if ($con->connect_error) {
    die("Conexión fallida: " . $con->connect_error);
}

// Agregar producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    $stmt = $con->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $stock);
    $stmt->execute();
    $stmt->close();
}

// Obtener productos
$result = $con->query("SELECT * FROM productos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Productos</h2>

    <form method="POST" class="mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="descripcion" class="form-control" placeholder="Descripción">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="stock" class="form-control" placeholder="Stock" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100">Agregar</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['descripcion']) ?></td>
                <td>$<?= $row['precio'] ?></td>
                <td><?= $row['stock'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Volver al Dashboard</a>
</div>
</body>
</html>
