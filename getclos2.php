<?php
// Improved get_clos.php script

// Database connection settings
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "obe"; // Replace with your database name

// Set the response header to JSON
header('Content-Type: application/json');

try {
    // Create database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        throw new Exception("Database connection failed");
    }

    // Check if course_id is set and not empty
    if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {
        $course_id = $_GET['course_id'];

        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT clo, description, weightage FROM clo WHERE course_id = ?");
        $stmt->bind_param("s", $course_id); // Bind course_id as a string parameter
        $stmt->execute();

        // Fetch the results
        $result = $stmt->get_result();
        $clo = $result->fetch_all(MYSQLI_ASSOC);

        // Return CLOs as JSON

        echo json_encode($clo);

        $stmt->close();
    } else {
        // Return an error if course_id is missing
        echo json_encode(['error' => 'Course ID is required']);
    }
} catch (Exception $e) {
    // Log the error internally and return a generic error message
    error_log($e->getMessage());
    echo json_encode(['error' => 'An unexpected error occurred']);
} finally {
    // Close the database connection
    $conn->close();
}
?>