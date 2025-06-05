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

header('Content-Type: application/json');

if (!isset($_GET['course_id'])) {
    echo json_encode(["error" => "No course_id provided"]);
    exit;
}

$course_id = $_GET['course_id']; // Get course ID as a string
error_log("Received course_id: $course_id"); // Log the received course ID

// Fetch CLOs for the selected course
$clos = [];
$clo_query = "SELECT clo_id, clo, weightage FROM clo WHERE course_id = ?";
$stmt = $conn->prepare($clo_query);
$stmt->bind_param("s", $course_id); // Bind as string
$stmt->execute();
$clo_result = $stmt->get_result();

if ($clo_result->num_rows > 0) {
    while ($clo_row = $clo_result->fetch_assoc()) {
        $clo_id = $clo_row['clo_id'];
        $clos[$clo_id] = [
            "name" => $clo_row['clo'],
            "value" => floatval($clo_row['weightage']),  // CLO weightage
            "children" => [],
            "assigned_weightage" => 0  // Initialize the assigned weightage for this CLO
        ];

        error_log("Fetched CLO: " . $clo_row['clo'] . " with CLO ID: " . $clo_row['clo_id']);
    }
} else {
    error_log("No CLOs found for course_id: $course_id");
}

$stmt->close();

// Fetch Assessments & Questions (including assessment type)
// Fetch Assessments & Questions (including assessment type)
$assignment_query = "
    SELECT c.clo_id, c.clo, q.assessment_id, a.assessment_type, q.question_id, q.question_number, ca.weightage
    FROM clo_question_assignment ca
    JOIN questions q ON ca.question_id = q.question_id
    JOIN clo c ON ca.clo_id = c.clo_id
    JOIN assessments a ON a.assessment_id = q.assessment_id  -- Join with the assessments table
    WHERE c.course_id = ?
    ORDER BY c.clo_id, q.assessment_id, q.question_number
";

$stmt = $conn->prepare($assignment_query);
$stmt->bind_param("s", $course_id); // Bind as string
$stmt->execute();
$assignment_result = $stmt->get_result();

if ($assignment_result->num_rows > 0) {
    while ($row = $assignment_result->fetch_assoc()) {
        $clo_id = $row['clo_id'];
        $assessment_id = $row['assessment_id'];
        $assessment_type = $row['assessment_type']; // Get the assessment type
        $question_number = "Q" . $row['question_number'];
        $weightage = $row['weightage'];  // Corrected to fetch weightage from clo_question_assignment

        error_log("Processing assessment: " . $assessment_type . " for CLO ID: " . $clo_id);

        // Ensure the CLO exists before adding assessments
        if (!isset($clos[$clo_id])) {
            $clos[$clo_id] = [
                "name" => $row['clo'],
                "value" => 0, // default to 0 if not fetched earlier
                "children" => [],
                "assigned_weightage" => 0
            ];
            error_log("Warning: CLO ID $clo_id appeared in assignments but not in the CLO table.");
        }

        // Check if the assessment exists under the CLO
        if (!isset($clos[$clo_id]["children"][$assessment_id])) {
            $clos[$clo_id]["children"][$assessment_id] = [
                "name" => $assessment_type, // Use the assessment type as the name
                "children" => [] // To store questions
            ];
            error_log("Initialized assessment: " . $assessment_type . " for CLO ID: " . $clo_id);
        }

        // Add question under the assessment
        $clos[$clo_id]["children"][$assessment_id]["children"][] = [
            "name" => "$question_number ($weightage%)",
            "value" => $weightage // Correctly use the weightage value
        ];

        // Update assigned weightage for the CLO
        $clos[$clo_id]["assigned_weightage"] += $weightage;

        // Debugging to see if the assigned weightage is being updated
        error_log("Updated assigned_weightage for CLO ID: $clo_id to " . $clos[$clo_id]["assigned_weightage"]);
    }
} else {
    error_log("No assessments found for course_id: $course_id");
}

$stmt->close();

// Calculate unassigned weightage for each CLO
foreach ($clos as $clo_id => &$clo) {
    $clo['assigned_weightage'] = round($clo['assigned_weightage'], 2);
    $clo['unassigned_weightage'] = round(max(0, $clo['value'] - $clo['assigned_weightage']), 2);

    // Debugging to check the calculated unassigned_weightage
    error_log("For CLO ID: $clo_id, assigned_weightage: " . $clo['assigned_weightage'] . ", unassigned_weightage: " . $clo['unassigned_weightage']);
}

// Prepare final structure
$final_data = [
    "name" => "CLO",
    "children" => array_values($clos) // Return CLOs and their associated assessments and questions
];

error_log("Final data structure: " . json_encode($final_data, JSON_PRETTY_PRINT)); // Log the final data

echo json_encode($final_data, JSON_PRETTY_PRINT);
?>
