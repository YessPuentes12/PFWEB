<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Mysql1234";
$dbname = "Ssalud";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión"]);
    exit();
}

// Recibe datos en JSON
$data = json_decode(file_get_contents("php://input"), true);
$name = isset($data["name"]) ? $conn->real_escape_string($data["name"]) : '';
$email = isset($data["email"]) ? $conn->real_escape_string($data["email"]) : '';
$password = isset($data["password"]) ? $conn->real_escape_string($data["password"]) : '';

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Faltan datos"]);
    exit();
}

// Verifica si el email ya existe
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

// Inserta el nuevo paciente
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