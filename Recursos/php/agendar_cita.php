<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

$conexion = new mysqli("localhost", "root", "", "ssalud");

if ($conexion->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "Error de conexión: " . $conexion->connect_error]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (
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

$especialidad = $data->especialidad;
$doctor_id = intval($data->doctor_id);
$fecha = $data->fecha;
$hora = $data->hora;
$motivo = $data->motivo;

$stmt = $conexion->prepare("INSERT INTO citas (especialidad, doctor_id, fecha, hora, motivo) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sisss", $especialidad, $doctor_id, $fecha, $hora, $motivo);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "Cita agendada correctamente"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Error al agendar cita", "error" => $stmt->error]);
}


// Validar formato de fecha (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    http_response_code(400);
    echo json_encode(["message" => "Formato de fecha inválido"]);
    exit;
}

// Validar formato de hora (HH:MM)
if (!preg_match('/^\d{2}:\d{2}$/', $hora)) {
    http_response_code(400);
    echo json_encode(["message" => "Formato de hora inválido"]);
    exit;
}

if (strlen($motivo) > 255) {
    http_response_code(400);
    echo json_encode(["message" => "El motivo es demasiado largo"]);
    exit;
}
$stmt->close();
$conexion->close();

?>