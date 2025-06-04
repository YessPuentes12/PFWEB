<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
$raw = file_get_contents('php://input');
file_put_contents('debug_modificar_cita_raw.txt', $raw);
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No se recibieron datos.']);
    exit;
}

file_put_contents('debug_modificar_cita.txt', print_r($data, true));
if (
    !isset($data['cita_id']) ||
    !isset($data['usuario_id']) ||
    !isset($data['especialidad']) ||
    !isset($data['doctor_id']) ||
    !isset($data['fecha']) ||
    !isset($data['hora']) ||
    !isset($data['motivo'])
) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

require_once 'conexion.php';

$stmt = $conn->prepare("UPDATE citas SET 
    id_usuario = ?, 
    id_especialidad = ?, 
    id_doctor = ?, 
    fecha = ?, 
    hora = ?, 
    motivo = ?
    WHERE id = ?");

$id_usuario = intval($data['usuario_id']);
$id_especialidad = intval($data['especialidad']);
$id_doctor = intval($data['doctor_id']);
$fecha = $data['fecha'];
$hora = $data['hora'];
$motivo = $data['motivo'];
$cita_id = intval($data['cita_id']);

$stmt->bind_param(
    "iiisssi",
    $id_usuario,
    $id_especialidad,
    $id_doctor,
    $fecha,
    $hora,
    $motivo,
    $cita_id
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cita modificada correctamente.']);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al modificar la cita: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>