<?php
if (isset($_POST['agregar_proveedor'])) {
    $nombre = $con->real_escape_string($_POST['nombre_prov']);
    $telefono = $con->real_escape_string($_POST['telefono_prov']);
    $direccion = $con->real_escape_string($_POST['direccion_prov']);

    $sql = "INSERT INTO proveedores (nombre, telefono, direccion) VALUES ('$nombre', '$telefono', '$direccion')";
    if ($con->query($sql)) {
        echo '<div class="alert alert-success">Proveedor agregado correctamente.</div>';
    } else {
        echo '<div class="alert alert-danger">Error al agregar proveedor: '.$con->error.'</div>';
    }
}
?>
