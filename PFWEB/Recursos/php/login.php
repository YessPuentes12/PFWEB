<?php
session_start();
header('Content-Type: application/json');

require_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = isset($data["email"]) ? $conn->real_escape_string($data["email"]) : '';
$password = isset($data["password"]) ? $data["password"] : '';

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Faltan datos"]);
    exit();
}

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
    return false;
}

if (loginUser($conn, "doctores", $email, $password, "doctor") !== false) exit();

if (loginUser($conn, "usuarios", $email, $password, "paciente") !== false) exit();

echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
$conn->close();
exit();
?>