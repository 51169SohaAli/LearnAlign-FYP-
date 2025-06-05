<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "obe"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
header('Content-Type: application/json');

if (!isset($_GET['course_id'])) {
    echo json_encode(['success' => false, 'message' => 'Course ID is missing']);
    exit;
}

$course_id = $_GET['course_id'];

$query = "SELECT clo_id, weightage, description FROM clos WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$clo_weightages = [];
$clo_details = [];

while ($row = $result->fetch_assoc()) {
    $clo_weightages[$row['clo_id']] = $row['weightage'];
    $clo_details[$row['clo_id']] = ['description' => $row['description']];
}

echo json_encode(['success' => true, 'clo_weightages' => $clo_weightages, 'clo_details' => $clo_details]);
?>
