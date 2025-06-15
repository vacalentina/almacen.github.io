<?php
session_start();
if (!isset($_SESSION['usuario'])) exit();

// Conexión a la base de datos
$con = new mysqli("localhost", "root", "daniela_0312", "almacen", 3306);

// Verificar si la conexión fue exitosa
if ($con->connect_error) {
    die("Conexión fallida: " . $con->connect_error);
}

// Obtener los datos enviados por POST
$id = $_POST['producto'];
$cantidad = intval($_POST['cantidad']);

// Obtener el precio del producto (columna 'precio' en lugar de 'precio_unitario')
$result = $con->query("SELECT precio FROM productos WHERE id = $id");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $precio = $row['precio'];  // Aquí estamos usando la columna 'precio' en lugar de 'precio_unitario'

    // Calcular el total de la venta
    $total = $precio * $cantidad;

    // Actualizar la tabla productos
    $con->query("UPDATE productos SET vendidos = vendidos + $cantidad, disponibles = disponibles - $cantidad WHERE id = $id");

    // Insertar la venta en la tabla ventas
    $fecha_venta = date('Y-m-d H:i:s'); // Fecha y hora actual
    $insertVenta = $con->prepare("INSERT INTO ventas (id_producto, cantidad, precio_unitario, total, fecha_venta) VALUES (?, ?, ?, ?, ?)");
    $insertVenta->bind_param("iidss", $id, $cantidad, $precio, $total, $fecha_venta);

    if ($insertVenta->execute()) {
        // Redirigir a la página del dashboard después de guardar la venta
        header("Location: dashboard.php");
    } else {
        echo "Error al guardar la venta: " . $insertVenta->error;
    }
} else {
    echo "Producto no encontrado.";
}

// Cerrar la conexión
$con->close();
?>
