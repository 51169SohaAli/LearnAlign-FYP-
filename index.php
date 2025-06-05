<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import and Display Course Data</title>
</head>
<body>
    <h1>Upload Course Data</h1>
    <form action="import.php" method="post" enctype="multipart/form-data">
        <label for="file">Select Excel File (.xlsx):</label>
        <input type="file" name="file" id="file" accept=".xlsx" required>
        <button type="submit" name="import">Upload and Display</button>
    </form>

    <h2>Course Data</h2>
    <table border="1">
       
        <tbody>
            <?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "obe";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch courses from database
$result = $conn->query("SELECT * FROM courses");

if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>Course ID</th><th>Course Name</th><th>Credits</th><th>Instructor ID</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["course_id"] . "</td><td>" . $row["course_name"] . "</td><td>" . $row["credits"] . "</td><td>" . $row["instructor_id"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No courses found.";
}

$conn->close();
?>

        </tbody>
    </table>
</body>
</html>
