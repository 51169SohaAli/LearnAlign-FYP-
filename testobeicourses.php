<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Courses | OBE-Incharge Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <!-- Boxiocns CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="sidebar close">
    <div class="logo-details">
        <img src="logo.png" alt="logo">
        <span class="logo_name">LearnAlign</span>
    </div>
    <ul class="nav-links">
        <li>
            <a href="obeinchargedashboard.html">
                <i class='bx bx-grid-alt'></i>
                <span class="link_name">Dashboard</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="obeinchargedashboard.html">Dashboard</a></li>
            </ul>
        </li>
        <li>
            <a href="testobeioutcomes.php">
                <i class='bx bx-bullseye'></i>
                <span class="link_name">Outcomes</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="testobeioutcomes.php">Outcomes</a></li>
            </ul>
        </li>
        <li>
            <a href="testobeicourses.html">
                <i class='bx bx-book-alt'></i>
                <span class="link_name">Courses</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="testobeicourses.html">Courses</a></li>
            </ul>
        </li>
        <li>
            <a href="obeiprograms.html">
                <i class='bx bx-bullseye'></i>
                <span class="link_name">Programs</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="obeiprograms.html">Programs</a></li>
            </ul>
        </li>
    </ul>
</div>
<section class="home-section" style="background-color: #fff;">
    <div class="home-content">
        <i class='bx bx-menu'></i>
        <div class="header-menu">
            <div class="notify-icon">
                <span class="bx bx-envelope"></span>
                <span class="notify">4</span>
            </div>

            <div class="notify-icon">
                <span class="bx bx-bell"></span>
                <span class="notify">3</span>
            </div>

            <div class="user">
                <div class="bg-img" style="background-image: url(1.jpeg)"></div>
            </div>
        </div>
    </div>

    <main>
        <div class="page-header">
            <h1>Courses</h1>
            <small>Home / Courses</small>
        </div>

        <div class="page-content">
            <div class="records table-responsive">
                <div class="record-header">
                    <div class="add">
                        <span>Entries</span>
                        <select name="" id="">
                            <option value="">1-25</option>
                            <option value="">1-100</option>
                            <option value="">1-500</option>
                        </select>
                        <a href="obeiaddcourses.html"><button class="btn-open-popup"><i class='bx bx-plus'></i>Add Courses</button></a>
                    </div>

                    <div class="browse">
                        <form action="test_import.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file" id="file" accept=".xlsx" required>
    <button type="submit" name="import" class="btn-open-popup">Import Data</button>
</form>

                    </div>
                </div>

                <div>


                    <h2 style="color:#020143">Courses</h2>
                    <table id="cloTable" width="100%">
                        <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Credits</th>
                            <th>Category</th>
                            <th>Semester</th>
                            <th>CLO</th>
                        </tr>
                        </thead>
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
$result = $conn->query("SELECT * FROM test_courses");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courseCode = $row["Course_Code"];

        // Fetch CLOs for this course
        $cloQuery = "SELECT CLONUM, CLODESC, LEVEL, CLODOMAIN, PLO FROM test_clo WHERE Course_Code = '$courseCode'";
        $cloResult = $conn->query($cloQuery);
        
        $cloData = [];
        while ($cloRow = $cloResult->fetch_assoc()) {
            $cloData[] = $cloRow; // Store each CLO's data as an array
        }

        // Encode CLO data as JSON for use in JavaScript
        $cloJson = htmlspecialchars(json_encode($cloData));

        echo "<tr class='course-row' data-clo='$cloJson'>
                <td>" . htmlspecialchars($row["Course_Code"]) . "</td>
                <td>" . htmlspecialchars($row["Course_Title"]) . "</td>
                <td class='center-align'>" . htmlspecialchars($row["Credits"]) . "</td>
                <td>" . htmlspecialchars($row["Category"]) . "</td>
                <td class='center-align'>" . htmlspecialchars($row["Semester"]) . "</td>
                <td>
                    <div class='clo-container'>
                        <span class='clo-number'>" . htmlspecialchars($row["CLO"]) . "</span>
                        <a href='#' class='clo-view'>View</a>
                    </div>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No courses found.</td></tr>";
}

$conn->close();
?>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</section>
<!-- Modal to show CLOs -->
<div id="cloModal" class="modal">
    <div class="modal-content">
        <span class="close" id="modalClose">&times;</span>
        <h3>Course CLOs</h3>
        <ul id="cloList"></ul>
    </div>
</div>

<script>
    let arrow = document.querySelectorAll(".arrow");
    for (var i = 0; i < arrow.length; i++) {
        arrow[i].addEventListener("click", (e) => {
            let arrowParent = e.target.parentElement.parentElement; // selecting main parent of arrow
            arrowParent.classList.toggle("showMenu");
        });
    }
    let sidebar = document.querySelector(".sidebar");
    let sidebarBtn = document.querySelector(".bx-menu");
    console.log(sidebarBtn);
    sidebarBtn.addEventListener("click", () => {
        sidebar.classList.toggle("close");
    });


   // Modal and CLOs handling
const modal = document.getElementById('cloModal');
const closeBtn = document.getElementById('modalClose');
const cloList = document.getElementById('cloList');

// Modify this part to display CLOs in a table format
document.querySelectorAll('.course-row').forEach(row => {
    row.addEventListener('click', function() {
        const cloData = JSON.parse(this.getAttribute('data-clo'));

        // Create table structure
        let tableHTML = `
            <table id="cloTable">
                <thead>
                    <tr>
                        <th>CLO Number</th>
                        <th>Description</th>
                        <th>Level</th>
                        <th>Domain</th>
                        <th>PLO</th>
                    </tr>
                </thead>
                <tbody>
        `;

        // Append each CLO as a table row
        cloData.forEach(clo => {
            tableHTML += `
                <tr>
                    <td>${clo.CLONUM}</td>
                    <td>${clo.CLODESC}</td>
                    <td>${clo.LEVEL}</td>
                    <td>${clo.CLODOMAIN}</td>
                    <td>${clo.PLO}</td>
                </tr>
            `;
        });

        tableHTML += `</tbody></table>`;

        // Set the modal content
        cloList.innerHTML = tableHTML;

        // Show the modal
        modal.style.display = 'block';
    });
});

// Close modal event listeners
closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
});

window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});

</script>
</body>
</html>
