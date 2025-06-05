<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password is empty
$dbname = "obe"; // Assuming this is your database for students

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
     $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate username
   if (preg_match('/^[0-9]{5,}$/', $student_id)) {
        // Hash the password before storing it in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO students (student_id,student_name, email, password) VALUES (?,?,?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $student_id, $name, $email, $hashed_password); // Bind username and hashed password
            
            // Execute the statement
            if ($stmt->execute()) {
                echo "<script>alert('Signup successful'); window.location.href = 'StudentDashboard.html';</script>";
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Invalid username. Student Id should only be numeric";
    }
}

// Close the database connection
$conn->close();
?>
