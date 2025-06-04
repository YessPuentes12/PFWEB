<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$name = isset($data["name"]) ? $conn->real_escape_string($data["name"]) : '';
$email = isset($data["email"]) ? $conn->real_escape_string($data["email"]) : '';
$password = isset($data["password"]) ? $conn->real_escape_string($data["password"]) : '';

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Faltan datos"]);
    exit();
}

$sql = "SELECT id FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "El email ya está registrado"]);
    exit();
}
$stmt->close();

$sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
    exit(); 
} else {
    echo json_encode(["success" => false, "message" => "Error al registrar"]);
    exit(); 
}

$stmt->close();
$conn->close();
?>