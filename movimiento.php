<?php
session_start();
if (!isset($_SESSION['usuario'])) exit();
$con = new mysqli("localhost", "root", "daniela_0312", "almacen", 3306);
$id = $_POST['producto'];
$cantidad = intval($_POST['cantidad']);
$con->query("UPDATE productos SET vendidos = vendidos + $cantidad, disponibles = disponibles - $cantidad WHERE id = $id");
header("Location: dashboard.php");
?>
