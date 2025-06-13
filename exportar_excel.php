<?php
// Conexión a la base de datos
$con = new mysqli("localhost", "root", "diana76", "almacen", 3307);

// Establecer la codificación correcta para UTF-8
$con->set_charset("utf8");

// Establecer encabezados para un archivo CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="estadisticas_almacen.csv"');

// Abrir el flujo de salida en formato CSV
$output = fopen('php://output', 'w');

// Escribir el BOM para que Excel reconozca UTF-8
fwrite($output, "\xEF\xBB\xBF"); // Esto es el BOM UTF-8

// Escribir los encabezados de las columnas en el CSV
fputcsv($output, array('Producto', 'Vendidos', 'Disponibles'));

// Consulta para obtener los productos
$res = $con->query("SELECT nombre, vendidos, disponibles FROM productos");

// Mostrar los datos en el archivo CSV
while ($row = $res->fetch_assoc()) {
    // Codificar los nombres en UTF-8 para evitar problemas con caracteres especiales
    $nombre = $row['nombre'];  // No es necesario usar utf8_encode aquí si la conexión está en UTF-8
    fputcsv($output, array($nombre, $row['vendidos'], $row['disponibles']));
}

// Cerrar el flujo de salida
fclose($output);
?>
