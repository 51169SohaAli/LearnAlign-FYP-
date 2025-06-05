<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'obe';
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get course ID
$courseId = $_GET['course_id'] ?? '';

if (!$courseId) {
    $courseId = null;
}

// Fetch students
$students = [];
$res = $conn->query("
    SELECT sc.student_id, s.student_name 
    FROM students_course sc
    JOIN students s ON sc.student_id = s.student_id
    WHERE sc.course_id = '$courseId'
");
while ($row = $res->fetch_assoc()) {
    $students[$row['student_id']] = $row['student_name'];
}

// Fetch CLOs
$clos = [];
$res = $conn->query("SELECT clo_id, clo, weightage FROM clo WHERE course_id = '$courseId'");
while ($row = $res->fetch_assoc()) {
    $clos[$row['clo_id']] = [
        'clo' => $row['clo'],
        'weightage' => $row['weightage'],
        'assessments' => []
    ];
}

// Fetch question mappings and student marks
$sql = "
SELECT 
    cqa.clo_id, cqa.weightage AS clo_question_weightage,
    q.question_id, q.assessment_id, q.question_number, q.question_marks,
    a.assessment_type,
    sm.student_id, sm.obtained_marks
FROM clo_question_assignment cqa
JOIN questions q ON cqa.question_id = q.question_id
JOIN assessments a ON q.assessment_id = a.assessment_id
JOIN student_assessments sm ON sm.question_id = q.question_id
WHERE a.course_id = '$courseId'
";

// Fetch course name and semester




$res = $conn->query($sql);

// Organize data
// Organize data
$data = [];
$assessmentCounts = [];
$assessmentTypeCounts = []; // Moved outside the loop to persist

while ($row = $res->fetch_assoc()) {
    $studentId = $row['student_id'];
    $cloId = $row['clo_id'];
    $assessmentId = $row['assessment_id'];
    $assessmentType = $row['assessment_type'];

    // Proper indexing
    if (!isset($assessmentTypeCounts[$assessmentType])) {
        $assessmentTypeCounts[$assessmentType] = [];
    }
    if (!isset($assessmentTypeCounts[$assessmentType][$assessmentId])) {
        $assessmentTypeCounts[$assessmentType][$assessmentId] = count($assessmentTypeCounts[$assessmentType]) + 1;
    }
    $assessmentIndex = $assessmentTypeCounts[$assessmentType][$assessmentId];
    $assessmentLabel = $assessmentType . ' ' . $assessmentIndex;

    $questionId = $row['question_id'];
    $obtained = $row['obtained_marks'];
    $total = $row['question_marks'];
    $weightage = $row['clo_question_weightage'];

    // ✅ Apply the correct formula
    $obtainedWeight = ($total > 0) ? ($obtained / $total) * $weightage : 0;

    // Track by student and CLO
    if (!isset($data[$studentId][$cloId])) {
        $data[$studentId][$cloId] = [
            'obtained' => 0,
            'assessments' => []
        ];
    }
    $data[$studentId][$cloId]['obtained'] += $obtainedWeight;

    if (!isset($data[$studentId][$cloId]['assessments'][$assessmentLabel])) {
        $data[$studentId][$cloId]['assessments'][$assessmentLabel] = 0;
    }
    $data[$studentId][$cloId]['assessments'][$assessmentLabel] += $obtainedWeight;

    // Track CLO assessments
    if (!isset($assessmentCounts[$cloId])) {
        $assessmentCounts[$cloId] = [];
    }

    if (!isset($assessmentCounts[$cloId][$assessmentLabel])) {
        $assessmentCounts[$cloId][$assessmentLabel] = 1;
    } else {
        $assessmentCounts[$cloId][$assessmentLabel]++;
    }

    $questionLabel = 'Q' . $row['question_number'] . ' (' . $weightage . '%)';
    if (!isset($clos[$cloId]['assessments'][$assessmentLabel])) {
        $clos[$cloId]['assessments'][$assessmentLabel] = [];
    }

    $clos[$cloId]['assessments'][$assessmentLabel][$questionLabel] = $weightage;
}

// Final transformation for report data
$reportData = [];
foreach ($data as $studentId => $cloData) {
    $reportData[$studentId] = [
        'student_name' => $students[$studentId] ?? 'Unknown',
        'clos' => []
    ];

    foreach ($clos as $cloId => $cloInfo) {
        $obtained = isset($cloData[$cloId]) ? $cloData[$cloId]['obtained'] : 0;
        $total = $cloInfo['weightage'];

        // ✅ Apply final percentage formula
        $percentage = ($total > 0) ? round(($obtained / $total) * 100, 2) : 0;

        $reportData[$studentId]['clos'][$cloId] = [
            'obtainedCloWeightage' => round($obtained, 2),
            'totalCloWeightage' => $total,
            'percentage' => $percentage
        ];
    }
}


// Fetch course name and semester
$courseInfo = null;
if ($courseId) {
    $res = $conn->query("
        SELECT ci.course_id, co.course_name, s.semester_name
        FROM course_instructor ci
        JOIN courses co ON ci.course_id = co.course_id
        JOIN semester s ON ci.semester_id = s.semester_id
        WHERE ci.course_id = '$courseId'
    ");
    $courseInfo = $res->fetch_assoc();
}

?>






<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title>Instructor Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <!-- Boxiocns CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <h1>Progress Report</h1>
                <small>Home / Dashboard</small>
            </div>
            
            <div class="page-content">

                <div class="browse">
 <label for="course_id">Courses:</label>
<select id="course_id">
  <option value="" disabled>Select a Course</option>
  <?php
  // Fetch courses
  $coursesRes = $conn->query("SELECT course_id, course_name FROM courses");
  while ($course = $coursesRes->fetch_assoc()) {
      $selected = ($course['course_id'] == $courseId) ? "selected" : "";
      echo "<option value='{$course['course_id']}' $selected>{$course['course_id']}: {$course['course_name']}</option>";
  }
  ?>
</select>


</div>
<?php if ($courseInfo): ?>
  <div style="margin: 20px 0; padding: 10px; background-color: #f0f8ff; border: 1px solid #ccc;">
    <strong>Course ID:</strong> <?php echo $courseInfo['course_id']; ?> <br>
    <strong>Course Name:</strong> <?php echo $courseInfo['course_name']; ?> <br>
    <strong>Semester:</strong> <?php echo $courseInfo['semester_name']; ?>
  </div>
<?php endif; ?>

<table id="report-table" border="1">
  <thead class="blue-header">
  <tr>
    <th rowspan="2">Student ID</th>
    <th rowspan="2">Student Name</th>
    <?php foreach($clos as $clo): ?>
      <th colspan="2">
        <?php echo $clo['clo']; ?> <br>(Total Weightage: <?php echo $clo['weightage']; ?>)
  <div class="assessment-box">
    <strong>Assessments:</strong>
  <?php foreach ($clo['assessments'] as $assessmentName => $questions): ?>
    <div style="margin-bottom: 2px;">
      <strong><?php echo $assessmentName; ?>:</strong>
      <?php
        $questionStrings = [];
        foreach ($questions as $questionLabel => $qWeight) {
          $questionStrings[] = $questionLabel;
        }
        echo implode(', ', $questionStrings);
      ?>
    </div>
  <?php endforeach; ?>
</div>

      </th>
    <?php endforeach; ?>
  </tr>
  <tr>
    <?php foreach($clos as $clo): ?>
      <th>Obtained CLO Weightage</th>
      <th>Obtained CLO Percentage (%)</th>
    <?php endforeach; ?>
  </tr>
</thead>
  <tbody>
  <?php foreach($reportData as $studentId => $data): ?>
    <tr>
      <td><?php echo $studentId; ?></td>
      <td><?php echo $data['student_name']; ?></td>
      <?php 
        $overallObtained = 0;
        $overallWeightage = 0;
        foreach($clos as $cloId => $clo):
          $cloData = $data['clos'][$cloId] ?? ['obtainedCloWeightage' => 0, 'percentage' => 0, 'totalCloWeightage' => $clo['weightage']];
          $obtained = $cloData['obtainedCloWeightage'];
          $percentage = $cloData['percentage'];
          $overallObtained += $obtained;
          $overallWeightage += $cloData['totalCloWeightage'];
      ?>
        <td><?php echo round($obtained, 2); ?></td>
        <td><?php echo $percentage; ?>%</td>
      <?php endforeach; ?>
    </tr>
  <?php endforeach; ?>
</tbody>
</table>




            </div>
        </div> 
        </main>
      </section>
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
  </script>
 <script>
document.getElementById("course_id").addEventListener("change", function() {
    const selectedCourseId = this.value;
    document.getElementById("courseId").value = selectedCourseId;
});

function fetchCourses() {
    fetch("fetchcourses.php")
        .then(response => response.json())
        .then(data => {
            const courseSelect = document.getElementById("course_id");
            courseSelect.innerHTML = '<option value="">Select a Course</option>';
            data.forEach(course => {
    courseSelect.innerHTML += `<option value="${course.course_id}">${course.course_id}:${course.course_name}</option>`;
});

        })
        .catch(error => console.error("Error fetching courses:", error));
}

document.addEventListener("DOMContentLoaded", function () {
    fetchCourses();
});

document.getElementById("course_id").addEventListener("change", function () {
    const courseId = this.value;
    if (courseId) {
        window.location.href = `progressreport.php?course_id=${courseId}`;
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