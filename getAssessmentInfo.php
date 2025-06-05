<?php
$servername = "localhost";
$username = "root"; // Replace 'root' with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "obe"; // Make sure you're using the correct database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Get the course_id and assessment_type from the query parameters
$course_id = $_GET['course_id'] ?? null;
$assessment_type = $_GET['assessment_type'] ?? null;  // Assuming you send assessment type in the query

if ($course_id && $assessment_type) {
    // Prepare the query to count the assessments of the same type for the course
    $query = "SELECT COUNT(*) FROM assessments WHERE course_id = ? AND assessment_type = ?";

    if ($stmt = $conn->prepare($query)) {
        // Bind parameters to the query
        $stmt->bind_param("is", $course_id, $assessment_type); // Assuming course_id is an integer and assessment_type is a string

        // Execute the query
        $stmt->execute();

        // Get the result (number of assessments)
        $stmt->bind_result($assessmentCount);
        $stmt->fetch();

        // Calculate the assessment number (i.e., how many assessments of this type)
        $assessmentNumber = $assessmentCount + 1; // Adding 1 to get the current assessment number

        // Prepare the response with assessment details (without weightage)
        $response = [
            'assessmentType' => $assessment_type,
            'assessmentNumber' => $assessmentNumber  // Dynamically generated number
        ];

        // Send the response as JSON
        echo json_encode($response);
    } else {
        // Return error if SQL query preparation fails
        echo json_encode(['error' => 'Failed to prepare query']);
    }
} else {
    // Return error if course_id or assessment_type is missing
    echo json_encode(['error' => 'Missing course_id or assessment_type']);
}

// Close the database connection
$conn->close();
?>
