<?php
$conexion = new mysqli("localhost", "root", "", "ssalud");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$resultado = $conexion->query("SELECT id, nombre FROM doctores");
$doctores = [];

while ($fila = $resultado->fetch_assoc()) {
    $doctores[] = $fila;
}

header('Content-Type: application/json');
echo json_encode($doctores);



$conexion->close();
?>
