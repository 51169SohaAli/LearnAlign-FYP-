<?php 

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
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Retrieve the POST data
    $data = json_decode(file_get_contents("php://input"), true);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $assessmentType = $data['assessmentType'];
        $courseId = $data['courseId'];
        $weightage = $data['weightage'];

        // Insert into assessments table
        $sql = "INSERT INTO assessments (assessment_type, course_id, weightage) VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssi", $assessmentType, $courseId, $weightage); // Bind parameters (string, integer, integer)
            if ($stmt->execute()) {
                $assessmentId = $stmt->insert_id; // Get the auto-generated ID
                echo json_encode([
                    "success" => true, 
                    "message" => "Assessment saved successfully.", 
                    "assessment_id" => $assessmentId
                ]);
            } else {
                echo json_encode(["success" => false, "error" => "Error saving assessment: " . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "error" => "Error preparing query: " . $conn->error]);
        }
    }

    $conn->close();

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
