<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "obe";

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$questionId = isset($data['question_id']) ? intval($data['question_id']) : 0;

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["exists" => false, "error" => "DB connection failed"]);
    exit;
}

$stmt = $conn->prepare("SELECT question_id FROM questions WHERE question_id = ?");
$stmt->bind_param("i", $questionId);
$stmt->execute();
$stmt->store_result();

echo json_encode(["exists" => $stmt->num_rows > 0]);

$stmt->close();
$conn->close();
?>
