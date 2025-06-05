<?php
// get_remaining_weightage.php

// Database connection setup (replace with your actual database credentials)
$host = 'localhost';  // Database host
$dbname = 'obe';  // Your database name
$username = 'root';  // Your database username
$password = '';  // Your database password

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if courseId and cloId are provided in the request
    if (isset($_GET['courseId']) && isset($_GET['cloId'])) {
        $courseId = $_GET['courseId'];
        $cloId = $_GET['cloId'];

        // Log the received courseId and cloId for debugging
        error_log("Received courseId: $courseId, cloId: $cloId");

        // Fetch the total weightage and the assigned weightage for the CLO
        $stmt = $pdo->prepare("
    SELECT 
        c.weightage AS total_weightage, 
        COALESCE(SUM(q.weightage), 0) AS assigned_weightage
    FROM 
        clo c
    LEFT JOIN 
        clo_question_assignment q ON q.clo_id = c.clo_id
    LEFT JOIN 
        questions qu ON qu.question_id = q.question_id
    LEFT JOIN 
        assessments a ON a.assessment_id = qu.assessment_id AND a.course_id = :courseId
    WHERE 
        c.clo_id = :cloId
    GROUP BY 
        c.clo_id
");

        $stmt->execute(['courseId' => $courseId, 'cloId' => $cloId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Calculate remaining weightage if assigned weightage is found
            $assignedWeightage = $result['assigned_weightage'];
            $totalWeightage = $result['total_weightage'];
            $remainingWeightage = $totalWeightage - $assignedWeightage;

            // Return the data as a JSON response
            echo json_encode([
                'success' => true,
                'totalWeightage' => $totalWeightage,
                'assignedWeightage' => $assignedWeightage,
                'remainingWeightage' => $remainingWeightage
            ]);
        } else {
            error_log("No data found for courseId: $courseId, cloId: $cloId");
            echo json_encode(['success' => false, 'error' => 'CLO not found or data missing']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    }
} catch (PDOException $e) {
    // Return an error if something goes wrong with the database connection
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
