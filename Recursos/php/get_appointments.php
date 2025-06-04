<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$userType = $data["userType"];
$userId = $data["userId"];

if ($userType === "doctor") {
    $sql = "SELECT c.*, u.nombre AS paciente_nombre, d.nombre AS doctor_nombre, e.nombre AS especialidad
            FROM citas c
            JOIN usuarios u ON c.id_usuario = u.id
            JOIN doctores d ON c.id_doctor = d.id
            JOIN especialidades e ON d.id_especialidad = e.id
            WHERE c.id_doctor = ?";
} else {
    $sql = "SELECT c.*, u.nombre AS paciente_nombre, d.nombre AS doctor_nombre, e.nombre AS especialidad
            FROM citas c
            JOIN usuarios u ON c.id_usuario = u.id
            JOIN doctores d ON c.id_doctor = d.id
            JOIN especialidades e ON d.id_especialidad = e.id
            WHERE c.id_usuario = ?";
}
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$citas = [];
while ($row = $result->fetch_assoc()) {
    $citas[] = $row;
}
echo json_encode($citas);
$conn->close();
?>