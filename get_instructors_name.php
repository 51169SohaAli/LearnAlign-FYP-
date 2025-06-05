<?php
// get_instructor_name.php
session_start();
if (!isset($_SESSION['instructor_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$instructorId = $_SESSION['instructor_id'];


error_log("Instructor ID in session: " . $_SESSION['instructor_id']);


// Connect to DB (update credentials as needed)
$conn = new mysqli("localhost", "root", "", "obe");

if ($conn->connect_error) {
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}

$query = "SELECT name FROM logininstructor WHERE instructor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $instructorId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["name" => $row['name']]);
} else {
    echo json_encode(["error" => "Instructor not found"]);
    
}




$stmt->close();
$conn->close();
?>
