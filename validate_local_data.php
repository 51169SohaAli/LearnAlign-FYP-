<?php
// validate_local_data.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "obe";

header('Content-Type: application/json');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "DB connection failed"]);
    exit;
}

$result = $conn->query("SELECT question_id FROM questions");
$validIds = [];

while ($row = $result->fetch_assoc()) {
    $validIds[] = $row['question_id'];
}

echo json_encode(["success" => true, "valid_question_ids" => $validIds]);

$conn->close();
?>
