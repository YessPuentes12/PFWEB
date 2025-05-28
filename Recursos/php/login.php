<?php
session_start();
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "Mysql1234";
$dbname = "ssaludb";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$email = isset($data["email"]) ? $conn->real_escape_string($data["email"]) : '';
$password = isset($data["password"]) ? $data["password"] : '';

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Faltan datos"]);
    exit();
}

// Función para manejar login y bloqueo
function loginUser($conn, $table, $email, $password, $tipo) {
    $sql = "SELECT id, email, password, intentos, activo, nombre FROM $table WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user["activo"] == 0) {
            echo json_encode(["success" => false, "message" => "Cuenta bloqueada. Contacta al administrador."]);
            exit();
        }

        if ($password === $user["password"]) {
            // Login correcto: reinicia intentos
            $update = $conn->prepare("UPDATE $table SET intentos = 0 WHERE id = ?");
            $update->bind_param("i", $user["id"]);
            $update->execute();

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["tipo"] = $tipo;
            $_SESSION["nombre"] = $user["nombre"];
            echo json_encode([
                "success" => true,
                "tipo" => $tipo,
                "user_id" => $user["id"],
                "nombre" => $user["nombre"]
            ]);
            exit();
        } else {
            // Login incorrecto: suma intento
            $intentos = $user["intentos"] + 1;
            $activo = $intentos >= 4 ? 0 : 1;
            $update = $conn->prepare("UPDATE $table SET intentos = ?, activo = ? WHERE id = ?");
            $update->bind_param("iii", $intentos, $activo, $user["id"]);
            $update->execute();

            if ($activo == 0) {
                echo json_encode(["success" => false, "message" => "Cuenta bloqueada por demasiados intentos."]);
            } else {
                $restantes = 4 - $intentos;
                echo json_encode(["success" => false, "message" => "Contraseña incorrecta. Intentos restantes: $restantes"]);
            }
            exit();
        }
    }
    // Si no existe el usuario
    return false;
}

// 1. Intentar login como doctor
if (loginUser($conn, "doctores", $email, $password, "doctor") !== false) exit();

// 2. Intentar login como paciente
if (loginUser($conn, "usuarios", $email, $password, "paciente") !== false) exit();

// Si no se encontró en ninguna tabla
echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
$conn->close();
exit();
?>