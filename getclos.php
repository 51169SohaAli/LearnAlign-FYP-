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
    echo json_encode(['success' => false, 'message' => 'Missing Course ID']);
    exit;
}

$course_id = $_GET['course_id'];

// Corrected query to properly return CLO name and CLO ID
$query = "SELECT clo_id, clo AS clo_name, description, weightage FROM clo WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$clos = [];

while ($row = $result->fetch_assoc()) {
    $clos[] = [
        'clo_id' => $row['clo_id'],  // Return CLO ID
        'clo_name' => $row['clo_name'],  // Return CLO name
        'description' => $row['description'],
        'weightage' => $row['weightage']
    ];
}

echo json_encode(['success' => true, 'clos' => $clos]);
?>
