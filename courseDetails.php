<?php
$servername = "localhost";
$username = "root"; // Replace 'root' with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "obe"; // Ensure you're using the correct database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$course_id = $_GET['course_id'] ?? null;
if (!$course_id) {
    echo "Invalid course.";
    exit;
}

// Fetch course basic info
$course_query = $conn->prepare("
    SELECT course_name, course_id 
    FROM courses 
    WHERE course_id = ?
");
$course_query->bind_param("s", $course_id);
$course_query->execute();
$course_result = $course_query->get_result();
$course = $course_result->fetch_assoc();

// Fetch counts
$clo_count = $conn->query("SELECT COUNT(*) AS total FROM clo WHERE course_id = '$course_id'")->fetch_assoc()['total'];
$assessment_count = $conn->query("SELECT COUNT(*) AS total FROM assessments WHERE course_id = '$course_id'")->fetch_assoc()['total'];
$student_count = $conn->query("SELECT COUNT(DISTINCT student_id) AS total FROM students_course WHERE course_id = '$course_id'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
     <meta charset="UTF-8">
     <title>Course Details</title>
    <link rel="stylesheet" href="style.css">
    <!-- Boxiocns CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6fb;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            border: 2px solid #020143 !important;
        }

        h2 {
            color: #020143;
            margin-bottom: 10px;
        }

        .info-box {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .info-card {
            background: #e4eaff;
            padding: 20px;
            border-radius: 12px;
            width: 30%;
            text-align: center;
            transition: 0.3s;
        }

        .info-card:hover {
            background: #d2dcfd;
            transform: translateY(-3px);
        }

        .info-card h3 {
            margin: 0;
            font-size: 20px;
            color: #020143;
        }

        .info-card p {
            margin: 5px 0 0;
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .back-btn {
            display: inline-block;
            margin-top: 30px;
            background: #020143;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.2s;
        }

        .back-btn:hover {
            background: #141466;
        }
    </style>
</head>
<body>

     <div class="sidebar close">
    <div class="logo-details">
      <img src="logo.png"alt="logo">
      <span class="logo_name">LearnAlign</span>
    </div>
    <ul class="nav-links">
      <li>
        <a href="InstructorDashboard.html">
          <i class='bx bx-grid-alt' ></i>
          <span class="link_name">Dashboard</span>
        </a>
        <ul class="sub-menu blank">
          <li><a class="link_name" href="InstructorDashboard.html">Dashboard</a></li>
        </ul>
      </li>
      
       <li>
        <a href="courses.html">
          <i class='bx bx-book-alt' ></i>
          <span class="link_name">Courses</span>
        </a>
        <ul class="sub-menu blank">
          <li><a class="link_name" href="courses.html">Courses</a></li>
        </ul>
      </li>
      <li>
        <a href="assessment.html">
          <i class='bx bx-list-check'></i>
          <span class="link_name">Assessments</span>
        </a>
        <ul class="sub-menu blank">
          <li><a class="link_name" href="assessment.html">Assessments</a></li>
        </ul>
      </li>
      <li>
        <a href="progressreport.php">
          <i class='bx bx-line-chart' ></i>
          <span class="link_name">Progress Report</span>
        </a>
        <ul class="sub-menu blank">
          <li><a class="link_name" href="progressreport.php">Progress Report</a></li>
        </ul>
      </li>
      
  </li>
</ul>
  </div>
  <section class="home-section" style="background-color: #fff;">
    <div class="home-content">
      <i class='bx bx-menu' ></i>
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
</div>

                </div>
            </div>
    </div>

    <main>
            
            <div class="page-header">
                <h1>Course Details</h1>
                <small>Home / Dashboard / Course Details</small>
            </div>
    <div class="container">
        <h2><?= $course['course_name'] ?? 'Course' ?></h2>
        <p style="color:#020143"><strong>Course Code:</strong> <?= $course['course_id'] ?></p>

        <div class="info-box">
            <div class="info-card">
                <h3>CLOs</h3>
                <p><?= $clo_count ?></p>
            </div>
            <div class="info-card">
                <h3>Assessments</h3>
                <p><?= $assessment_count ?></p>
            </div>
            <div class="info-card">
                <h3>Students</h3>
                <p><?= $student_count ?></p>
            </div>
        </div>

        <a href="InstructorDashboard.html" class="back-btn">← Back to Dashboard</a>
    </div>
</body>
</html>
<script>
    let arrow = document.querySelectorAll(".arrow");
  for (var i = 0; i < arrow.length; i++) {
    arrow[i].addEventListener("click", (e)=>{
   let arrowParent = e.target.parentElement.parentElement;//selecting main parent of arrow
   arrowParent.classList.toggle("showMenu");
    });
  }
  let sidebar = document.querySelector(".sidebar");
  let sidebarBtn = document.querySelector(".bx-menu");
  console.log(sidebarBtn);
  sidebarBtn.addEventListener("click", ()=>{
    sidebar.classList.toggle("close");
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
