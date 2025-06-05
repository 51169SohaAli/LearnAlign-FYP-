<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password is empty
$dbname = "obe";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_POST["submit"])) {
    $instructor_id = $_POST['instructor_id'];
    $password = $_POST['password'];

    // Check if the entered credentials match the special credentials
    if ($instructor_id === "f1718" && $password === "Riphahf1718") {
        // Set the instructor ID in the session for the OBE In-charge
        $_SESSION['instructor_id'] = $instructor_id;
        $_SESSION['instructor_name'] = "Sumera Saleem";

        // Redirect to the OBE In-charge dashboard
        header("Location: obeinchargedashboard.html");
        exit(); // Ensure no further code is executed after redirect
    }

    // If special credentials do not match, check the credentials in the database
    // Prepare the SQL statement to prevent SQL injection for general users
    $stmt = $conn->prepare("SELECT name, password FROM logininstructor WHERE instructor_id = ?");
$stmt->bind_param("s", $instructor_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($name, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        $_SESSION['instructor_id'] = $instructor_id;
        $_SESSION['instructor_name'] = $name;

        header("Location: InstructorDashboard.html");
        exit;
    } else {
        echo "<script>alert('Invalid password');</script>";
    }
} else {
    echo "<script>alert('Invalid instructor ID');</script>";
}

    // Close the statement and connection
    $stmt->close();
}

$conn->close();
?>
