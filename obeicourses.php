<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Courses | OBE-Incharge Dashboard</title>
    <link rel="stylesheet" href="style.css">
     <script src="https://unpkg.com/lucide@latest"></script>
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="sidebar close">
    <div class="logo-details">
        <img src="LearnAlign Logo Final.png" alt="logo">
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
        <a href="initiateobeprocess.html">
          <i class='bx bx-bullseye' ></i>
          <span class="link_name">Outcomes</span>
        </a>
        <ul class="sub-menu blank">
          <li><a class="link_name" href="initiateobeprocess.html">Initiate OBE Process</a></li>
        </ul>
      </li>
        <li>
            <a href="obeicourses.php">
                <i class='bx bx-book-alt'></i>
                <span class="link_name">Courses</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="obeicourses.php">Courses</a></li>
            </ul>
        </li>
      
    </ul>
</div>
<section class="home-section" style="background-color: #fff;">
    <div class="home-content">
        <i class='bx bx-menu'></i>
        <div class="header-menu">
                                
     <div class="user-container">
  <div class="user" onclick="toggleDropdown()">
    <div class="bg-img" id="userIcon"></div>
  </div>

  <div class="dropdown" id="userDropdown">
    <label for="profileInput">
      <div class="profile-pic" id="dropdownProfile" style="background-image: url('OIP.jpg')" title="Click to change profile picture"></div>
    </label>
    <div class="welcome-message" id="welcomeMessage">Welcome,</div>
    <div id="instructorName" class="instructor-name">Loading name...</div>
    <input type="file" id="profileInput" accept="image/*" style="display: none" onchange="changeProfilePic(event)">
    
    <div class="divider"></div>

    <div class="logout-option" onclick="logout()">
      <i data-lucide="log-out"></i>
      <span>Log Out</span>
    </div>
  </div>
</div>        </div>
    </div>

    <main>
        <div class="page-header">
            <h1>Courses</h1>
            <small>Home / Courses</small>
        </div>

        <div class="page-content">
            <div class="records table-responsive">



                <div>


                    <h2 style="color:#020143">Courses</h2>
                    <table id="cloTable" width="100%">
                        <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Credits</th>
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

// Fetch the latest semester ID
$semesterQuery = "SELECT MAX(semester_id) AS latest_semester_id FROM semester";
$semesterResult = $conn->query($semesterQuery);
$latestSemesterId = 0;

if ($semesterResult->num_rows > 0) {
    $semesterRow = $semesterResult->fetch_assoc();
    $latestSemesterId = $semesterRow["latest_semester_id"];
}

// Fetch courses associated with the latest semester
$courseQuery = "
    SELECT c.course_id, c.course_name, c.credits 
    FROM courses c
    INNER JOIN course_instructor ci ON c.course_id = ci.course_id
    WHERE ci.semester_id = '$latestSemesterId'";

$courseResult = $conn->query($courseQuery);

if ($courseResult->num_rows > 0) {
    while ($row = $courseResult->fetch_assoc()) {
        $courseCode = $row["course_id"];

        // Fetch CLOs for this course
        $cloQuery = "SELECT clo, description, plo, weightage FROM clo WHERE course_id = '$courseCode'";
        $cloResult = $conn->query($cloQuery);
        
        $cloData = [];
        while ($cloRow = $cloResult->fetch_assoc()) {
            $cloData[] = $cloRow; // Store each CLO's data as an array
        }

        // Encode CLO data as JSON for use in JavaScript
        $cloJson = htmlspecialchars(json_encode($cloData));

        echo "<tr class='course-row' data-clo='$cloJson'>
                <td>" . htmlspecialchars($row["course_id"]) . "</td>
                <td>" . htmlspecialchars($row["course_name"]) . "</td>
                <td class='center-align'>" . htmlspecialchars($row["credits"]) . "</td>
                <td>
                    <div class='clo-container'>
                        <a href='#' class='clo-view'>View</a>
                    </div>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No courses found for the latest semester.</td></tr>";
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
<div id="cloModal" class="modals">
    <div class="modal-contents">
        <span class="modalClose" id="modalClose">&times;</span>
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
</script>
<script>

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
                        <th>PLO</th>
                        <th>Weightage</th>
                    </tr>
                </thead>
                <tbody>
        `;

        // Append each CLO as a table row
        cloData.forEach(clo => {
            tableHTML += `
                <tr>
                    <td>${clo.clo}</td>
                    <td>${clo.description}</td>
                    <td>${clo.plo}</td>
                    <td>${clo.weightage}</td>
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

function toggleDropdown() {
  const dropdown = document.getElementById("userDropdown");
  dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";

  fetch('get_instructors_name.php')
    .then(response => response.json())
    .then(data => {
      if (data.name) {
        document.getElementById("instructorName").textContent = data.name;
      } else {
        document.getElementById("instructorName").textContent = "Unknown Instructor";
      }
    });

  lucide.createIcons();
}

function logout() {
  alert("Logged out!");
  window.location.href = "login.html";
}

function changeProfilePic(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const imgData = e.target.result;

      // Set the image in both UI locations
      document.getElementById("userIcon").style.backgroundImage = `url('${imgData}')`;
      document.getElementById("dropdownProfile").style.backgroundImage = `url('${imgData}')`;

      // Save the image to localStorage
      localStorage.setItem("userProfilePic", imgData);
    };
    reader.readAsDataURL(file);
  }
}

document.addEventListener("click", function (event) {
  const user = document.querySelector(".user");
  const dropdown = document.getElementById("userDropdown");
  if (!user.contains(event.target) && !dropdown.contains(event.target)) {
    dropdown.style.display = "none";
  }
});

window.addEventListener("DOMContentLoaded", function () {
  const savedPic = localStorage.getItem("userProfilePic");
  if (savedPic) {
    document.getElementById("userIcon").style.backgroundImage = `url('${savedPic}')`;
    document.getElementById("dropdownProfile").style.backgroundImage = `url('${savedPic}')`;
  }
});
</script>
</body>
</html>
