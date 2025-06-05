<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title>Courses |Instructor Dashboard</title>
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

    <main>
            
            <div class="page-header">
                <h1>CLO Weightage Assignment</h1>
                <small>Home / Courses/Assign CLO Weightage</small>
            </div>
            
            <div class="page-content">
        
                <div class="records table-responsive">

<div>
    <h2 class="assignclo" style="color:#020143">Assign CLO Weightage</h2>
     <!-- Step 2: CLO Table -->
        <div id="step2">
            <h4 class="clow">CLO Weightages</h4>
            <div id="cloTableContainer">
                <table id="cloTable">
                    <thead>
                        <tr>
                            <th>CLO</th>
                            <th>Description</th>
                            <th>Weightage (%)</th>
                        </tr>
                    </thead>
                    <tbody id="cloTableBody">
                        <!-- CLO rows will be dynamically inserted here -->
                    </tbody>
                </table>
                <div id="progressContainer">
                    <div id="cloProgressBar">
                        <span id="cloProgressText">0% Covered</span>
                    </div>
                </div>
            </div>
<a href="courses.html"><button>Back</button>
</a>
            <button type="button" onclick="saveWeightage()">Save</button>
        </div>
    </div>
</div>
    
</div>


<!-- Modal Structure -->
<div id="reusableModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="close-btn">&times;</span>
      <!-- No header text -->
    </div>
    <p id="modalMessage">Message content goes here.</p>
    <button id="modalCloseBtn" class="modal-close-btn">Close</button>
    <div class="modal-footer"></div>
    <div id="modalButtons">
  <button id="modalYesBtn">Yes</button>
  <button id="modalNoBtn">No</button>
</div>
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
    document.addEventListener("DOMContentLoaded", function () {
        // Retrieve course_id from URL
        const urlParams = new URLSearchParams(window.location.search);
        const courseId = urlParams.get("course_id");

        if (!courseId) {
            alert("No Course ID found in URL!");
            return;
        }

        // Fetch CLOs based on the URL courseId
        fetchCLOs(courseId);
    });

    let cloWeights = {};  
    let cloDataList = {}; // Global object to store CLOs

    function fetchCLOs(courseId) {
        if (!courseId) return;

        console.log("Fetching CLOs for courseId:", courseId);

        fetch(`getclos2.php?course_id=${courseId}`)
            .then(response => response.json())
            .then(data => {
                cloDataList = {}; // Reset CLO storage
                data.forEach(clo => {
                    cloDataList[clo.clo] = {
                        description: clo.description
                    };
                });
                generateCLOInputs(data); // Populate the CLO table in Step 2
            })
            .catch(error => console.error("Error fetching CLOs:", error));
    }

    function generateCLOInputs(clos) {
        const cloTableBody = document.getElementById("cloTableBody");
        cloTableBody.innerHTML = "";
        cloWeights = {}; // Reset stored weights

        if (clos.length === 0) {
            document.getElementById("cloTableContainer").style.display = "none";
            return;
        }

        document.getElementById("cloTableContainer").style.display = "block";

        clos.forEach((cloData) => {
            const cloName = cloData.clo.replace(/\W+/g, "_"); // Ensure safe ID
            const weightage = cloData.weightage || 0; // Default to 0 if empty
            cloWeights[cloName] = weightage; // Store weightage entered

            const rowHTML = `
                <tr data-clo-id="${cloData.clo}">
                    <td>${cloData.clo}</td>
                    <td>${cloData.description}</td>
                    <td>
                        <input type="number" id="${cloName}" min="0" max="100" value="${weightage}"
                            oninput="updateCLOWeight('${cloName}', this.value)">
                    </td>
                </tr>
            `;

            cloTableBody.insertAdjacentHTML("beforeend", rowHTML);
        });
    }

    function getCLOData(cloName) {
        console.log("Fetching CLO Data for:", cloName);
        console.log("Current CLO Data List:", cloDataList); // Debugging

        if (cloDataList[cloName]) {
            let weightage = parseFloat(cloDataList[cloName].weightage); // Ensure it's a number

            if (isNaN(weightage)) {
                console.warn(`CLO ${cloName} has an invalid weightage. Defaulting to 0.`);
                weightage = 0; // Default to 0 if weightage is missing or invalid
            }

            return {
                clo: cloName,
                description: cloDataList[cloName].description,
                weightage: weightage
            };
        } else {
            console.warn("CLO data not found for:", cloName);
            return null;
        }
    }

    function validateStep2() {
        let totalWeight = 0;
        let cloWeights = {};

        document.querySelectorAll("#cloTableBody input").forEach(input => {
            let cloId = input.id;
            let weightage = parseFloat(input.value) || 0;
            cloWeights[cloId] = weightage;
            totalWeight += weightage;
        });

        if (totalWeight !== 100) {
            alert(`⚠️ You still need to assign **${100 - totalWeight}%** to reach 100%.`);
            return;
        }

        console.log("✅ CLO coverage is 100%. Proceeding to Step 3.");
        console.log("CLOs being passed to Step 3:", cloWeights);

        showStep(3);
    }

   function saveWeightage() {
    let totalWeight = 0;
    let cloWeightages = [];
    
    document.querySelectorAll("#cloTableBody tr").forEach(row => {
        let cloId = row.dataset.cloId;
        let weightage = parseFloat(row.querySelector("input").value) || 0;
        
        // Push CLO name, description, and weightage to the array
        cloWeightages.push({
            clo: cloId,
            description: row.cells[1].innerText, // Description from the second column
            weightage: weightage
        });
        totalWeight += weightage;
    });

   if (totalWeight !== 100) {
    showModal(`⚠️ Total CLO weightage must be 100%. Currently: ${totalWeight}%`);
    return;
}

    // Save to localStorage
    localStorage.setItem("cloWeightages", JSON.stringify(cloWeightages));

    // Optionally, send data to the server via a fetch request
    const urlParams = new URLSearchParams(window.location.search);
    let courseId = urlParams.get("course_id");

    fetch("save_weightage.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ course_id: courseId, clo_weightages: cloWeightages })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModal("✅ Weightages saved successfully!");
        } else {
            showModal("❌ Error saving weightages. Try again.");
        }
    })
    .catch(error => console.error("Error:", error));
}


    function updateProgressBar() {
    console.log("Updating progress bar...");

    let total = 0;
    let cloInputs = document.querySelectorAll("#cloTableBody input[type='number']");
    console.log(`Found CLO weightage inputs for Question:`, cloInputs);

    cloInputs.forEach(input => {
        let value = parseFloat(input.value) || 0;
        total += value;
    });

    let progressBar = document.getElementById("cloProgressBar");
    let progressText = document.getElementById("cloProgressText");

    progressBar.style.width = Math.min(total, 100) + "%"; // Visually cap at 100%
    progressText.innerText = total + "% Covered"; // Show actual total

    if (total > 100) {
        progressBar.style.backgroundColor = "red"; // Indicate overflow
    } else if (total === 100) {
        progressBar.style.backgroundColor = "green";
    } else {
        progressBar.style.backgroundColor = "#f39c12"; // Orange when incomplete
    }
}

function updateCLOWeight(clo, value) {
    cloWeights[clo] = parseFloat(value) || 0; // Parse the input value for CLO weight

    console.log("Updated CLO Weights:", cloWeights);

    // Call the function to update progress bar in Step 2 after the weights are updated
    updateProgressBar();
}

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
  showModal("Logged out!");
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


function showModal(message, type = "info") {
    return new Promise((resolve) => {
        // Set the message dynamically
        document.getElementById("modalMessage").innerText = message;

        // Show modal
        document.querySelector('.modal').classList.add('show');

        const yesBtn = document.getElementById("modalYesBtn");
        const noBtn = document.getElementById("modalNoBtn");
        const closeBtn = document.getElementById("modalCloseBtn");
        const headerCloseIcon = document.querySelector(".close-btn");

        if (type === "confirm") {
            // Show Yes/No buttons
            yesBtn.style.display = "inline-block";
            noBtn.style.display = "inline-block";

            // Hide close button/icon
            closeBtn.style.display = "none";
            headerCloseIcon.style.display = "none";

            yesBtn.onclick = function () {
                closeModal();
                resolve(true);
            };

            noBtn.onclick = function () {
                closeModal();
                resolve(false);
            };
        } else {
            // Hide Yes/No buttons
            yesBtn.style.display = "none";
            noBtn.style.display = "none";

            // Show close button/icon
            closeBtn.style.display = "inline-block";
            headerCloseIcon.style.display = "inline-block";

            // Auto-close after 2 seconds
            setTimeout(() => {
                closeModal();
                resolve();
            }, 2000);
        }

        // Close modal button
        closeBtn.onclick = function () {
            closeModal();
            resolve(false); // Treat as cancel
        };

        // Close icon in header
        headerCloseIcon.onclick = function () {
            closeModal();
            resolve(false); // Treat as cancel
        };

        // Clicking outside modal closes it
        window.onclick = function (event) {
            if (event.target == document.getElementById("reusableModal")) {
                closeModal();
                resolve(false);
            }
        };

        function closeModal() {
            document.querySelector('.modal').classList.remove('show');

            // Reset all buttons to visible for next time
            yesBtn.style.display = "inline-block";
            noBtn.style.display = "inline-block";
            closeBtn.style.display = "inline-block";
            headerCloseIcon.style.display = "inline-block";
        }
    });
}
</script>
</body>
</html>