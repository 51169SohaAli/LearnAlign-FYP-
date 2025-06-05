<?php
session_start(); // Start session to access student ID

// Replace with actual login logic
$loggedInStudentId = $_SESSION['student_id'] ?? null;

if (!$loggedInStudentId) {
    die("Student not logged in.");
}

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'obe';
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$courseId = $_GET['course_id'] ?? '';

$studentData = [];
// Get student's progress report data for the selected course
$query = "
    SELECT 
        a.assessment_type,
        SUM(q.question_marks) AS total_marks,
        SUM(COALESCE(sa.obtained_marks, 0)) AS obtained_marks
    FROM assessments a
    JOIN questions q ON a.assessment_id = q.assessment_id
    LEFT JOIN student_assessments sa ON sa.question_id = q.question_id AND sa.student_id = '$loggedInStudentId'
    WHERE a.course_id = '$courseId'
    GROUP BY a.assessment_id
    ORDER BY FIELD(a.assessment_type, 'Quiz', 'Assignment', 'Midterm', 'Project', 'Lab', 'Final Term')

";

$cloData = [];

$cloQuery = "
    SELECT 
        c.clo,
        c.weightage AS total_weightage,
        SUM(COALESCE(sa.obtained_marks, 0) * (cqa.weightage / q.question_marks)) AS obtained_weightage
    FROM clo c
    JOIN clo_question_assignment cqa ON c.clo_id = cqa.clo_id
    JOIN questions q ON cqa.question_id = q.question_id
    JOIN assessments a ON q.assessment_id = a.assessment_id
    LEFT JOIN student_assessments sa ON sa.question_id = q.question_id AND sa.student_id = '$loggedInStudentId'
    WHERE c.course_id = '$courseId'
    GROUP BY c.clo, c.weightage
";



$result = $conn->query($query);

$cloResult = $conn->query($cloQuery);

if ($cloResult->num_rows > 0) {
    while ($row = $cloResult->fetch_assoc()) {
        $cloData[] = $row;
    }
}


if ($result->num_rows > 0) {
    $studentData = [];
    while ($row = $result->fetch_assoc()) {
        $studentData[] = $row;
    }
}
?>
<script>
    const cloData = <?php echo json_encode($cloData); ?>;
</script>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Progress Report</title>
    <link rel="stylesheet" href="style.css">
     <script src="https://unpkg.com/lucide@latest"></script>
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
            <li><a href="StudentDashboard.html"><i class='bx bx-grid-alt'></i><span class="link_name">Dashboard</span></a></li>

            <li><a href="studentprogressreport.php"><i class='bx bx-line-chart'></i><span class="link_name">Progress Report</span></a></li>

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
    <div id="studentName" class="student-name">Loading name...</div>
    <input type="file" id="profileInput" accept="image/*" style="display: none" onchange="changeProfilePic(event)">
    
    <div class="divider"></div>

    <div class="logout-option" onclick="logout()">
      <i data-lucide="log-out"></i>
      <span>Log Out</span>
    </div>
  </div>
</div>              </div>
         
            </div>
        </div>
        <main>
            <div class="page-header">
                <h1>Progress Report</h1>
                <small>Home / Progress Report</small>
            </div>
            <div class="page-content">
                <div class="chart-container" style="width: 60%; max-width: 600px; margin: auto;">
    <canvas id="cloChart"></canvas>
</div>


                <div class="browse">
                    <label for="course_id">Courses:</label>
                    <select id="course_id" onchange="window.location.href='studentprogressreport.php?course_id=' + this.value;">
                        <option value="" disabled selected>Select a Course</option>
                        <?php
                        $coursesRes = $conn->query("
                            SELECT c.course_id, c.course_name 
                            FROM students_course sc
                            JOIN courses c ON sc.course_id = c.course_id
                            WHERE sc.student_id = '$loggedInStudentId'
                            ORDER BY c.course_name
                        ");

                        while ($course = $coursesRes->fetch_assoc()) {
                            $selected = ($course['course_id'] == $courseId) ? "selected" : "";
                            echo "<option value='" . htmlspecialchars($course['course_id']) . "' $selected>" . 
                                 htmlspecialchars($course['course_id']) . ": " . htmlspecialchars($course['course_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                   <table id="report-table" border="1">
    <thead class="blue-header">
        <tr>
            <th>Assessment Type</th>
            <th>Obtained Marks</th>
            <th>Total Marks</th>
            <th>Percentage</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($studentData)): ?>
            <?php foreach ($studentData as $data): ?>
                <tr>
                    <td><?= htmlspecialchars($data['assessment_type']) ?></td>
                    <td><?= htmlspecialchars($data['obtained_marks']) ?></td>
                    <td><?= htmlspecialchars($data['total_marks']) ?></td>
                    <td>
                        <?php
                            $percentage = ($data['total_marks'] > 0) 
                                ? round(($data['obtained_marks'] / $data['total_marks']) * 100, 2)
                                : 0;
                            echo $percentage . "%";
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>

                    </table>
                    <?php if (!empty($cloData)): ?>
    <h3 style="margin-top: 40px; color: #020143;">CLO Performance</h3>
    <table id="report-table" style="margin-top: 10px;">
        <thead class="blue-header">
            <tr>
                <th>CLO</th>
                <th>Total Weightage</th>
                <th>Obtained Weightage</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cloData as $clo): ?>
                <tr>
                    <td><?= htmlspecialchars($clo['clo']) ?></td>
                    <td><?= htmlspecialchars($clo['total_weightage']) ?></td>
                    <td><?= round($clo['obtained_weightage'], 2) ?></td>
                    <td>
                        <?php
                            $percentage = ($clo['total_weightage'] > 0)
                                ? round(($clo['obtained_weightage'] / $clo['total_weightage']) * 100, 2)
                                : 0;
                            echo $percentage . "%";
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

                </div>
            </div>
        </main>
    </section>
    <script>
    const labels = <?php echo json_encode(array_column($cloData, 'clo')); ?>;
    const percentages = <?php
        echo json_encode(array_map(function ($row) {
            $total = $row['total_weightage'];
            $obtained = $row['obtained_weightage'];
            return $total > 0 ? round(($obtained / $total) * 100, 2) : 0;
        }, $cloData));
    ?>;
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Function to generate pastel colors from specific color palette
        const generatePastelColors = (count) => {
            const colors = [];
            const baseColors = [
                'hsl(200, 50%, 80%)',  // Blue
                'hsl(330, 50%, 80%)',  // Pink
                'hsl(270, 50%, 80%)',  // Purple
                'hsl(120, 50%, 80%)'   // Green
            ];

            for (let i = 0; i < count; i++) {
                // Use modulo to loop through the base colors
                const color = baseColors[i % baseColors.length];
                colors.push(color);
            }
            return colors;
        };

        // Get pastel colors for each CLO
        const backgroundColors = generatePastelColors(labels.length);
        const borderColors = generatePastelColors(labels.length);

        const ctx = document.getElementById('cloChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'CLO Achievement (%)',
                    data: percentages,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => `${context.parsed.y}%`
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Achievement (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'CLOs'
                        }
                    }
                }
            }
        });
    });

    function toggleDropdown() {
  const dropdown = document.getElementById("userDropdown");
  dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";

  fetch('get_students_name.php')
    .then(response => response.json())
    .then(data => {
      if (data.name) {
        document.getElementById("studentName").textContent = data.name;
      } else {
        document.getElementById("studentName").textContent = "Unknown Student";
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


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const labels = cloData.map(item => item.clo);
    const percentages = cloData.map(item => {
        const obtained = parseFloat(item.obtained_weightage);
        const total = parseFloat(item.total_weightage);
        const percentage = total === 0 ? 0 : ((obtained / total) * 100).toFixed(2);
        return percentage;
    });
    
    </script>
  </body>
</html>