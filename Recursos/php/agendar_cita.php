<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"));

$id_usuario = isset($data->usuario_id) ? intval($data->usuario_id) : null;

if (
    empty($id_usuario) ||
    empty($data->especialidad) ||
    empty($data->doctor_id) ||
    empty($data->fecha) ||
    empty($data->hora) ||
    empty($data->motivo)
) {
    http_response_code(400);
    echo json_encode(["message" => "Faltan datos obligatorios"]);
    exit;
}

$id_especialidad = intval($data->especialidad);
$id_doctor = intval($data->doctor_id);
$fecha = $data->fecha;
$hora = $data->hora;
$motivo = $data->motivo;

$stmt = $conn->prepare("INSERT INTO citas (id_usuario, id_doctor, id_especialidad, fecha, hora, motivo) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiisss", $id_usuario, $id_doctor, $id_especialidad, $fecha, $hora, $motivo);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "Cita agendada correctamente"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Error al agendar cita", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>