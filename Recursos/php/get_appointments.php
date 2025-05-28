<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "12345";
$dbname = "pfweb_db";
$conn = new mysqli($servername, $username, $password, $dbname);

$data = json_decode(file_get_contents("php://input"), true);
$userType = $data["userType"];
$userId = $data["userId"];

if ($userType === "doctor") {
    $sql = "SELECT * FROM citas WHERE id_doctor = ?";
} else {
    $sql = "SELECT * FROM citas WHERE id_usuario = ?";
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
?>