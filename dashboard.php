<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.html");
    exit();
}

// Conexión a la base de datos
$con = new mysqli("localhost", "root", "daniela_0312", "almacen", 3306);
if ($con->connect_error) {
    die("Conexión fallida: " . $con->connect_error);
}

// CRUD Proveedores
if (isset($_POST['agregar_proveedor'])) {
    $nombre = $con->real_escape_string($_POST['nombre_prov']);
    $telefono = $con->real_escape_string($_POST['telefono_prov']);
    $direccion = $con->real_escape_string($_POST['direccion_prov']);
    $con->query("INSERT INTO proveedores (nombre, telefono, direccion) VALUES ('$nombre', '$telefono', '$direccion')");
    header("Location: dashboard.php#proveedores");
    exit();
}

if (isset($_GET['eliminar_proveedor'])) {
    $id = intval($_GET['eliminar_proveedor']);
    $con->query("DELETE FROM proveedores WHERE id=$id");
    header("Location: dashboard.php#proveedores");
    exit();
}

if (isset($_POST['editar_proveedor'])) {
    $id = intval($_POST['id_editar']);
    $nombre = $con->real_escape_string($_POST['nombre_edit']);
    $telefono = $con->real_escape_string($_POST['telefono_edit']);
    $direccion = $con->real_escape_string($_POST['direccion_edit']);
    $con->query("UPDATE proveedores SET nombre='$nombre', telefono='$telefono', direccion='$direccion' WHERE id=$id");
    header("Location: dashboard.php#proveedores");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
        .table th, .table td { vertical-align: middle; }
        .table thead { background-color: #007bff; color: white; }
        .table-striped tbody tr:nth-child(odd) { background-color: #f1f1f1; }
        .tab-content { padding: 15px; background: white; border-radius: 5px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .nav-tabs .nav-link { border-radius: 0; background-color: #007bff; color: white; }
        .nav-tabs .nav-link.active { background-color: #0056b3; }
        .float-end { margin-top: 10px; }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>Bienvenido, <?php echo $_SESSION['usuario']; ?></h2>
    <a href="logout.php" class="btn btn-danger float-end">Cerrar Sesión</a>

    <ul class="nav nav-tabs mt-4" id="myTab" role="tablist">
        <li class="nav-item"><button class="nav-link active" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas" type="button">Estadísticas</button></li>
        <li class="nav-item"><button class="nav-link" id="inventarios-tab" data-bs-toggle="tab" data-bs-target="#inventarios" type="button">Inventarios</button></li>
        <li class="nav-item"><button class="nav-link" id="alertas-tab" data-bs-toggle="tab" data-bs-target="#alertas" type="button">Alertas de Stock</button></li>
        <li class="nav-item"><button class="nav-link" id="proveedores-tab" data-bs-toggle="tab" data-bs-target="#proveedores" type="button">Proveedores</button></li>
        <li class="nav-item"><button class="nav-link" id="precios-tab" data-bs-toggle="tab" data-bs-target="#precios" type="button">Historial de Precios</button></li>
        <li class="nav-item"><button class="nav-link" id="descuentos-tab" data-bs-toggle="tab" data-bs-target="#descuentos" type="button">Descuentos</button></li>
        <li class="nav-item"><button class="nav-link" id="clientes-tab" data-bs-toggle="tab" data-bs-target="#clientes" type="button">Clientes</button></li>
        <li class="nav-item"><button class="nav-link" id="pedidos-tab" data-bs-toggle="tab" data-bs-target="#pedidos" type="button">Pedidos</button></li>
        <li class="nav-item"><button class="nav-link" id="pedidosprod-tab" data-bs-toggle="tab" data-bs-target="#pedidosprod" type="button">Productos de Pedidos</button></li>
    </ul>

    <div class="tab-content mt-3" id="myTabContent">
        <!-- Estadísticas -->
        <div class="tab-pane fade show active" id="estadisticas" role="tabpanel" aria-labelledby="estadisticas-tab">
            <h4>Estadísticas de Productos</h4>
            <a href="exportar_excel.php" class="btn btn-success mb-2">Exportar a Excel</a>
            <table class="table table-bordered">
                <thead><tr><th>Producto</th><th>Vendidos</th><th>Disponibles</th></tr></thead>
                <tbody>
                <?php
                $result = $con->query("SELECT id, nombre, vendidos, disponibles FROM productos");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['nombre']}</td><td>{$row['vendidos']}</td><td>{$row['disponibles']}</td></tr>";
                }
                ?>
                </tbody>
            </table>
            <h4 class="mt-5">Gráficos de Productos</h4>
            <div class="row">
                <?php
                $result = $con->query("SELECT id, nombre, vendidos, disponibles FROM productos");
                while ($row = $result->fetch_assoc()) {
                    $productoId = $row['id'];
                    $productoNombre = $row['nombre'];
                    $vendidos = $row['vendidos'];
                    $disponibles = $row['disponibles'];
                ?>
                    <div class="col-md-4 mb-4">
                        <h5><?php echo $productoNombre; ?></h5>
                        <canvas id="pieChart<?php echo $productoId; ?>" width="150" height="150"></canvas>
                        <script>
                            var ctx = document.getElementById('pieChart<?php echo $productoId; ?>').getContext('2d');
                            var pieChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: ['Vendidos', 'Disponibles'],
                                    datasets: [{
                                        data: [<?php echo $vendidos; ?>, <?php echo $disponibles; ?>],
                                        backgroundColor: ['rgba(255, 99, 132, 0.6)','rgba(54, 162, 235, 0.6)'],
                                        borderColor: ['rgba(255, 99, 132, 1)','rgba(54, 162, 235, 1)'],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: { position: 'top' }
                                    }
                                }
                            });
                        </script>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Inventarios -->
        <div class="tab-pane fade" id="inventarios" role="tabpanel" aria-labelledby="inventarios-tab">
            <h4>Movimientos de Inventario</h4>
            <table class="table table-striped">
                <thead><tr><th>ID</th><th>Producto</th><th>Cantidad</th><th>Tipo</th><th>Fecha</th><th>Precio Compra</th></tr></thead>
                <tbody>
                <?php
                $result = $con->query("SELECT i.id, p.nombre as producto, i.cantidad, i.tipo_accion, i.fecha, i.precio_compra FROM inventarios i LEFT JOIN productos p ON i.id_producto = p.id");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['producto']}</td><td>{$row['cantidad']}</td><td>{$row['tipo_accion']}</td><td>{$row['fecha']}</td><td>{$row['precio_compra']}</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <!-- Alertas de Stock -->
        <div class="tab-pane fade" id="alertas" role="tabpanel" aria-labelledby="alertas-tab">
            <h4>Alertas de Stock</h4>
            <table class="table table-striped">
                <thead><tr><th>ID</th><th>Producto</th><th>Stock Mínimo</th><th>Fecha Alerta</th><th>Estado</th></tr></thead>
                <tbody>
                <?php
                $result = $con->query("SELECT a.id, p.nombre as producto, a.stock_minimo, a.fecha_alerta, a.estado FROM alertas_stock a LEFT JOIN productos p ON a.id_producto = p.id");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['producto']}</td><td>{$row['stock_minimo']}</td><td>{$row['fecha_alerta']}</td><td>{$row['estado']}</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <!-- Proveedores -->
        <div class="tab-pane fade" id="proveedores" role="tabpanel" aria-labelledby="proveedores-tab">
            <h4>Proveedores</h4>
            <!-- Aquí va el CRUD de proveedores -->
            <?php
            $editando = false;
            if (isset($_GET['editar_proveedor'])) {
                $editando = true;
                $idEditar = intval($_GET['editar_proveedor']);
                $provEdit = $con->query("SELECT * FROM proveedores WHERE id=$idEditar")->fetch_assoc();
            }

            if (isset($_POST['agregar_proveedor'])) {
                $nombre = $con->real_escape_string($_POST['nombre_prov']);
                $telefono = $con->real_escape_string($_POST['telefono_prov']);
                $direccion = $con->real_escape_string($_POST['direccion_prov']);
                $con->query("INSERT INTO proveedores (nombre, telefono, direccion) VALUES ('$nombre', '$telefono', '$direccion')");
                header("Location: dashboard.php#proveedores");
                exit();
            }

            if (isset($_GET['eliminar_proveedor'])) {
                $id = intval($_GET['eliminar_proveedor']);
                $con->query("DELETE FROM proveedores WHERE id=$id");
                header("Location: dashboard.php#proveedores");
                exit();
            }

            if (isset($_POST['editar_proveedor'])) {
                $id = intval($_POST['id_editar']);
                $nombre = $con->real_escape_string($_POST['nombre_edit']);
                $telefono = $con->real_escape_string($_POST['telefono_edit']);
                $direccion = $con->real_escape_string($_POST['direccion_edit']);
                $con->query("UPDATE proveedores SET nombre='$nombre', telefono='$telefono', direccion='$direccion' WHERE id=$id");
                header("Location: dashboard.php#proveedores");
                exit();
            }
            ?>

            <button class="btn btn-success mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#formProveedor" aria-expanded="false" aria-controls="formProveedor">
                Agregar Proveedor
            </button>

            <div class="collapse mb-3" id="formProveedor">
                <form method="POST" action="#proveedores">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre_prov" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono_prov" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion_prov" class="form-control" required>
                    </div>
                    <button type="submit" name="agregar_proveedor" class="btn btn-primary">Agregar</button>
                </form>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th><th>Nombre</th><th>Teléfono</th><th>Dirección</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $con->query("SELECT * FROM proveedores");
                while ($row = $result->fetch_assoc()) {
                    if ($editando && $row['id'] == $idEditar) {
                        echo '<tr><form method="POST" action="dashboard.php#proveedores">';
                        echo '<td>'.$row['id'].'<input type="hidden" name="id_editar" value="'.$row['id'].'"></td>';
                        echo '<td><input type="text" name="nombre_edit" class="form-control" value="'.$provEdit['nombre'].'" required></td>';
                        echo '<td><input type="text" name="telefono_edit" class="form-control" value="'.$provEdit['telefono'].'" required></td>';
                        echo '<td><input type="text" name="direccion_edit" class="form-control" value="'.$provEdit['direccion'].'" required></td>';
                        echo '<td><button type="submit" name="editar_proveedor" class="btn btn-primary btn-sm">Guardar</button> ';
                        echo '<a href="dashboard.php#proveedores" class="btn btn-secondary btn-sm">Cancelar</a></td>';
                        echo '</form></tr>';
                    } else {
                        echo '<tr>';
                        echo '<td>'.$row['id'].'</td><td>'.$row['nombre'].'</td><td>'.$row['telefono'].'</td><td>'.$row['direccion'].'</td>';
                        echo '<td>';
                        echo '<a href="dashboard.php?editar_proveedor='.$row['id'].'#proveedores" class="btn btn-warning btn-sm">Editar</a> ';
                        echo '<a href="dashboard.php?eliminar_proveedor='.$row['id'].'#proveedores" class="btn btn-danger btn-sm" onclick="return confirm(\'¿Seguro que deseas eliminar este proveedor?\')">Eliminar</a>';
                        echo '</td></tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>

        <!-- Historial de Precios -->
        <div class="tab-pane fade" id="precios" role="tabpanel" aria-labelledby="precios-tab">
            <h4>Historial de Precios</h4>
            <table class="table table-striped">
                <thead><tr><th>ID</th><th>Producto</th><th>Precio</th><th>Fecha de Cambio</th></tr></thead>
                <tbody>
                <?php
                $result = $con->query("SELECT h.id, p.nombre as producto, h.precio, h.fecha_cambio FROM historial_precios h LEFT JOIN productos p ON h.id_producto = p.id");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['producto']}</td><td>{$row['precio']}</td><td>{$row['fecha_cambio']}</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <!-- Descuentos -->
        <div class="tab-pane fade" id="descuentos" role="tabpanel" aria-labelledby="descuentos-tab">
            <h4>Descuentos</h4>
            <table class="table table-striped">
                <thead><tr><th>ID</th><th>Producto</th><th>Descuento (%)</th><th>Inicio</th><th>Fin</th></tr></thead>
                <tbody>
                <?php
                $result = $con->query("SELECT d.id, p.nombre as producto, d.porcentaje_descuento, d.fecha_inicio, d.fecha_fin FROM descuentos d LEFT JOIN productos p ON d.id_producto = p.id");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['producto']}</td><td>{$row['porcentaje_descuento']}</td><td>{$row['fecha_inicio']}</td><td>{$row['fecha_fin']}</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <!-- Clientes -->
        <div class="tab-pane fade" id="clientes" role="tabpanel" aria-labelledby="clientes-tab">
            <h4>Clientes</h4>
            <table class="table table-striped">
                <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Dirección</th></tr></thead>
                <tbody>
                <?php
                $result = $con->query("SELECT id, nombre, email, telefono, direccion FROM clientes");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['nombre']}</td><td>{$row['email']}</td><td>{$row['telefono']}</td><td>{$row['direccion']}</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <!-- Pedidos -->
        <div class="tab-pane fade" id="pedidos" role="tabpanel" aria-labelledby="pedidos-tab">
            <h4>Pedidos</h4>
            <table class="table table-striped">
                <thead><tr><th>ID</th><th>Cliente</th><th>Fecha</th><th>Estado</th><th>Total</th></tr></thead>
                <tbody>
                <?php
                $result = $con->query("SELECT p.id, c.nombre as cliente, p.fecha_pedido, p.estado, p.total FROM pedidos p LEFT JOIN clientes c ON p.id_cliente = c.id");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['cliente']}</td><td>{$row['fecha_pedido']}</td><td>{$row['estado']}</td><td>{$row['total']}</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <!-- Productos de Pedidos -->
        <div class="tab-pane fade" id="pedidosprod" role="tabpanel" aria-labelledby="pedidosprod-tab">
            <h4>Productos de Pedidos</h4>
            <table class="table table-striped">
                <thead><tr><th>ID</th><th>Pedido</th><th>Producto</th><th>Cantidad</th><th>Precio</th></tr></thead>
                <tbody>
                <?php
                $result = $con->query("SELECT pp.id, pp.id_pedido, p.nombre as producto, pp.cantidad, pp.precio FROM pedidos_productos pp LEFT JOIN productos p ON pp.id_producto = p.id");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['id_pedido']}</td><td>{$row['producto']}</td><td>{$row['cantidad']}</td><td>{$row['precio']}</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function activateTabFromHash() {
    var hash = window.location.hash;
    if (hash) {
        var tabTrigger = document.querySelector('button[data-bs-target="' + hash + '"]');
        if (tabTrigger) {
            var tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    } else {
        var firstTab = document.querySelector('button[data-bs-toggle="tab"]');
        if (firstTab) {
            var tab = new bootstrap.Tab(firstTab);
            tab.show();
        }
    }
}
document.addEventListener('DOMContentLoaded', activateTabFromHash);
window.addEventListener('hashchange', activateTabFromHash);

document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function(btn) {
    btn.addEventListener('shown.bs.tab', function () {
        var hash = btn.getAttribute('data-bs-target');
        if (history.replaceState) {
            history.replaceState(null, null, window.location.pathname + hash);
        } else {
            location.hash = hash;
        }
    });
});
</script>
</body>
</html>
