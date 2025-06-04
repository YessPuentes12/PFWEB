<?php
require_once 'conexion.php';

$resultado = $conexion->query("SELECT id, nombre FROM especialidades ORDER BY nombre");
$especialidades = [];
while ($fila = $resultado->fetch_assoc()) {
    $especialidades[] = $fila;
}

header('Content-Type: application/json');
echo json_encode($especialidades);
$conn->close();
?>