<?php
require_once 'conexion.php';

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$resultado = $conn->query("SELECT id, nombre, id_especialidad, hora_llegada, hora_salida FROM doctores");
$doctores = [];

while ($fila = $resultado->fetch_assoc()) {
    $doctores[] = $fila;
}

header('Content-Type: application/json');
echo json_encode($doctores);



$conn->close();
?>
