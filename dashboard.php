<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.html");
    exit();
}

// Conexión a la base de datos
$con = new mysqli("localhost", "root", "diana76", "almacen", 3307);
if ($con->connect_error) {
    die("Conexión fallida: " . $con->connect_error);
}

// Obtener productos
$productos_result = $con->query("SELECT * FROM productos");

// Obtener usuarios
$usuarios_result = $con->query("SELECT * FROM usuarios");

// Obtener ventas
$ventas_result = $con->query("SELECT v.id, p.nombre AS producto, v.cantidad, v.precio_unitario, v.total, v.fecha_venta FROM ventas v JOIN productos p ON v.id_producto = p.id");

// Para obtener datos para los gráficos
$productos_data = [];
$ventas_data = [];
$ventas_dates = [];

while ($row = $productos_result->fetch_assoc()) {
    $productos_data[] = $row;
}

while ($row = $ventas_result->fetch_assoc()) {
    $ventas_data[] = $row['total'];
    $ventas_dates[] = $row['fecha_venta'];
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Cargar Chart.js desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Cargar Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .table thead {
            background-color: #007bff;
            color: white;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: #f1f1f1;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .btn-custom {
            background-color: #28a745;
            color: white;
        }

        .btn-custom:hover {
            background-color: #218838;
        }

        .header-text {
            color: #007bff;
        }

        .card {
            margin-bottom: 30px;
        }

        .row {
            margin-top: 30px;
        }

        .col-md-4 {
            margin-bottom: 20px;
        }

        .chart-container {
            height: 200px;  /* Cambié la altura aquí */
            width: 100%;
        }

        .nav-tabs .nav-link {
            border-radius: 0;
            background-color: #007bff;
            color: white;
        }

        .nav-tabs .nav-link.active {
            background-color: #0056b3;
        }

        .navbar {
            margin-bottom: 30px;
        }

        .tab-content {
            padding: 15px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        }

        .float-end {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>Bienvenido, <?php echo $_SESSION['usuario']; ?></h2>
    <a href="logout.php" class="btn btn-danger float-end">Cerrar Sesión</a>

    <ul class="nav nav-tabs mt-4" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas" type="button">Estadísticas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="movimientos-tab" data-bs-toggle="tab" data-bs-target="#movimientos" type="button">Movimientos</button>
        </li>
    </ul>

    <div class="tab-content mt-3" id="myTabContent">
        <!-- Estadísticas -->
        <div class="tab-pane fade show active" id="estadisticas" role="tabpanel">
            <h4>Estadísticas de Productos</h4>
            <a href="exportar_excel.php" class="btn btn-success mb-2">Exportar a Excel</a>

            <!-- Tabla de productos -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Vendidos</th>
                        <th>Disponibles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Obtener los productos, vendidos y disponibles
                    $result = $con->query("SELECT id, nombre, vendidos, disponibles FROM productos");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>{$row['nombre']}</td><td>{$row['vendidos']}</td><td>{$row['disponibles']}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Mostrar gráficos fuera de la tabla -->
            <h4 class="mt-5">Gráficos de Productos</h4>
            <div class="row">
                <?php
                // Recorremos los productos para generar gráficos individuales
                $result = $con->query("SELECT id, nombre, vendidos, disponibles FROM productos");
                while ($row = $result->fetch_assoc()) {
                    $productoId = $row['id'];
                    $productoNombre = $row['nombre'];
                    $vendidos = $row['vendidos'];
                    $disponibles = $row['disponibles'];
                ?>
                    <div class="col-md-4 mb-4">
                        <h5><?php echo $productoNombre; ?></h5>
                        <canvas id="pieChart<?php echo $productoId; ?>" width="150" height="150"></canvas> <!-- Tamaño ajustado -->
                        <script>
                            // Crear gráfico de pastel para cada producto
                            var ctx = document.getElementById('pieChart<?php echo $productoId; ?>').getContext('2d');
                            var pieChart = new Chart(ctx, {
                                type: 'pie', // Tipo de gráfico: pastel
                                data: {
                                    labels: ['Vendidos', 'Disponibles'],
                                    datasets: [{
                                        label: '<?php echo $productoNombre; ?>',
                                        data: [<?php echo $vendidos; ?>, <?php echo $disponibles; ?>],
                                        backgroundColor: [
                                            'rgba(255, 99, 132, 0.6)', // Color para "Vendidos"
                                            'rgba(54, 162, 235, 0.6)'  // Color para "Disponibles"
                                        ],
                                        borderColor: [
                                            'rgba(255, 99, 132, 1)',   // Color para "Vendidos"
                                            'rgba(54, 162, 235, 1)'    // Color para "Disponibles"
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(tooltipItem) {
                                                    return tooltipItem.label + ': ' + tooltipItem.raw;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Movimientos -->
        <div class="tab-pane fade" id="movimientos" role="tabpanel">
            <h4>Registrar Movimiento</h4>
            <form action="movimiento.php" method="POST">
                <div class="mb-3">
                    <label for="producto" class="form-label">Producto</label>
                    <select name="producto" class="form-select">
                        <?php
                        $res = $con->query("SELECT id, nombre FROM productos");
                        while ($prod = $res->fetch_assoc()) {
                            echo "<option value='{$prod['id']}'>{$prod['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad Vendida</label>
                    <input type="number" name="cantidad" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Registrar</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

</html>
