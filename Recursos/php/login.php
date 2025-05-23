<?php
session_start();

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Mysql1234";
$dbname = "ssaludb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $conn->real_escape_string($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT users_id, password FROM users WHERE password_hash IS NULL";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $hashed = password_hash($row['password'], PASSWORD_BCRYPT);
            $update_sql = "UPDATE users SET password_hash = ? WHERE users_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $hashed, $row['users_id']);
            $update_stmt->execute();
        }
    }

    $sql = "SELECT users_id, email, password_hash FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password_hash"])) {
            $_SESSION["user_id"] = $user["users_id"];
            $_SESSION["email"] = $user["email"];

            echo json_encode(["success" => true, "redirect" => "dashboard.html"]);
            exit();
        } else {
            echo json_encode(["success" => false, "message" => "Incorrect password."]);
            exit();
        }
    } else {
        echo json_encode(["success" => false, "message" => "Email not found."]);
        exit();
    }
}

echo json_encode(["success" => false, "message" => "Invalid request method."]);
$conn->close();
?>
