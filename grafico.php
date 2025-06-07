<?php
// Conexión a la base de datos
$con = new mysqli("localhost", "root", "daniela_0312", "almacen", 3306);

// Verificar la conexión
if ($con->connect_error) {
    die("Conexión fallida: " . $con->connect_error);
}

// Consultar productos y cantidad de ventas
$result = $con->query("SELECT nombre, vendidos FROM productos");

$productos = [];
$vendidos = [];

// Recoger los datos
while ($row = $result->fetch_assoc()) {
    $productos[] = $row['nombre'];
    $vendidos[] = $row['vendidos'];
}

// Retornar los datos en formato JSON
echo json_encode([
    'productos' => $productos,
    'vendidos' => $vendidos
]);

// Cerrar la conexión
$con->close();
?>
