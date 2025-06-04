<?php
header('Content-Type: application/json');
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!isset($data['cita_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de cita no recibido.']);
    exit;
}

require_once 'conexion.php';

$stmt = $conn->prepare("DELETE FROM citas WHERE id = ?");
$stmt->bind_param("i", $data['cita_id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cita eliminada correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar la cita: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>