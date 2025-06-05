<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Add Assessment</title>
    <link rel="stylesheet" href="testcss.css">
    <!-- Boxiocns CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="InstructorDashboard.html">
                    <i class='bx bx-grid-alt'></i>
                    <span class="link_name">Dashboard</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="InstructorDashboard.html">Dashboard</a></li>
                </ul>
            </li>
            <li>
                <a href="outcomes.html">
                    <i class='bx bx-bullseye'></i>
                    <span class="link_name">Outcomes</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="outcomes.html">Outcomes</a></li>
                </ul>
            </li>
            <li>
                <a href="courses.html">
                    <i class='bx bx-book-alt'></i>
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
                <a href="progressreport.html">
                    <i class='bx bx-line-chart'></i>
                    <span class="link_name">Progress Report</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="progressreport.html">Progress Report</a></li>
                </ul>
            </li>
            <li>
                <a href="feedback.html">
                    <i class='bx bx-comment'></i>
                    <span class="link_name">Feedback</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="feedback.html">Feedback</a></li>
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
                <h1>Add Assessment</h1>
                <small>Home / Courses / Add Assessment</small>
            </div>

        <div class="page-content">
    <div class="main-form-container">
        <div class="form-header">
            <h4 class="popup-title">Assessment Creation</h4>
        </div>
        <form class="form-container" onsubmit="return false;">
            <div class="input-group">
               <!-- Step 1: Assessment Information -->
<div class="step" id="step1">
    <h4>Step 1: Assessment Information</h4>
    
    <label for="assessmenttype">Assessment Type:</label>
    <select id="assessmenttype">
        <option value="" disabled selected>Choose Assessment Type</option>
        <option>Midterm</option>
        <option>FinalTerm</option>
        <option>Assignment</option>
        <option>Quiz</option>
        <option>Project</option>
        <option>Lab</option>
    </select>

    <label for="course_id">Course ID:</label>
    <select id="course_id">
        <option value="" disabled selected>Loading...</option>
    </select>
    <input type="hidden" id="courseId" value="101">

    <label for="totalWeightage">Weightage for Assessment:</label>
    <input type="number" id="totalWeightage" placeholder="Enter total weightage" required>

    <!-- Hidden input for storing assessment ID -->
    <input type="hidden" id="assessmentId" value="">

    <button type="button" onclick="validateStep1()">Next</button>
</div>

<!-- Step 3: Question Mapping -->
<div class="step" id="step3" style="display: none;">
    <div class="step3-container">
        <!-- Left Section: Question Input -->
        <div class="left-section">
            <h3>Step 3: Question Mapping</h3>
            <label for="numQuestions">Number of Questions:</label>
            <input type="number" id="numQuestions" placeholder="Enter number of questions" min="1" onchange="generateTable()">
            <div id="questionsTableContainer"></div>
            <button onclick="prevStep(1)">Back</button>
            <button type="button" id="saveAssessment">Save</button>
            <button type="button" id="addNewAssessment">Add New Assessment</button>
        </div>

       <!-- Right section for the pie chart -->
<div class="right-section">
    <div class="chart-box">
            <div class="progress-bar-container" id="progressBarsContainer">
        <!-- Progress bars will be dynamically generated here -->
    </div>
        <div class="chart-label">CLO Weightage Assignment</div>
    </div>
</div>

    </div>
</div>

        </form>

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
     // Store assigned weightages per question
       let questionCount = 1;


    // Clear previous CLO weightage inputs
    cloWeightsContainer.innerHTML = '';

    // Dynamically add CLO weightage fields based on the CLOs in the table
    if (cloTable.rows.length > 0) {
        Array.from(cloTable.rows).forEach((row, index) => {
            const cloName = row.cells[0].innerText; // CLO name
            cloWeightsContainer.innerHTML += `
                <div>
                    <label for="cloWeight${index}">Weightage for ${cloName}:</label>
                   <input type="number" id="cloWeight${index}" placeholder="Enter weightage" min="0" oninput="updateAssignedWeight('${cloId}', this.value)">

                </div>
            `;
        });
    } else {
        cloWeightsContainer.innerHTML = '<p>No CLOs mapped for this course.</p>';
    }
   </script> 
  <script type="text/javascript" src="script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>  
<script>
    let pieChartInstance = null; // Track the instance of the chart
    
   document.addEventListener("DOMContentLoaded", function () {
    fetchCourses();
    document.getElementById("course_id").addEventListener("change", function () {
        fetchCLOs(this.value);
    });
    
 /*   document.addEventListener("DOMContentLoaded", function () {
    const assessmentId = document.getElementById("assessmentId")?.value;
    const courseId = document.getElementById("courseId")?.value;

    if (assessmentId && courseId) {
        updateProgressBars(); // Call only when IDs exist
    } else {
        console.warn("⚠️ Assessment ID or Course ID is missing. Skipping progress bar update.");
    }
}); */

    document.getElementById("course_id").addEventListener("change", function() {
    // Update the hidden courseId input when the user selects a course
    const selectedCourseId = this.value;
    document.getElementById("courseId").value = selectedCourseId;
});



document.getElementById("addNewAssessment").addEventListener("click", function () {
    console.log("➡️ Add New Assessment button clicked!");

    // Show confirmation dialog
    if (confirm("Are you sure you want to add a new assessment? This will reset your current form but keep assigned progress.")) {
        console.log("User confirmed to add new assessment. Resetting form and navigating to Step 1...");

        // Reset all input fields in the form
        document.querySelectorAll("input, select").forEach(input => {
            if (input.type === "checkbox" || input.type === "radio") {
                input.checked = false;
            } else {
                input.value = "";
            }
        });

        // Reset dynamically generated content (like tables)
        document.getElementById("questionsTableContainer").innerHTML = "";

        // Reset CLO weightage tracking (but do NOT clear assignedWeights)
        cloWeights = {};  

        // Ensure the step is updated
        showStep(1); // Make sure the function is called here after resetting

    } else {
        console.log("User canceled. Staying on current step.");
    }
});

    // Attach Next Button Event
    document.getElementById("nextToStep2").addEventListener("click", function () {
        validateStep1();
    });
});


let cloWeights = {};
let assignedWeights = {}; 


function fetchCourses() {
    fetch("getcourses.php")
        .then(response => response.json())
        .then(data => {
            const courseSelect = document.getElementById("course_id");
            courseSelect.innerHTML = '<option value="">Select a Course</option>';
            data.forEach(course => {
                courseSelect.innerHTML += `<option value="${course.id}">${course.name}</option>`;
            });
        })
        .catch(error => console.error("Error fetching courses:", error));
}

let cloDataList = {}; // Global object to store CLOs

function fetchCLOs(courseId) {
    if (!courseId) return;

    fetch(`getclos.php?course_id=${courseId}`)
        .then(response => response.json())
        .then(data => {
            console.log("Fetched CLOs:", data); // Add logging here
            if (data.success && Array.isArray(data.clos)) {
                cloDataList = {}; // Reset CLO storage
                data.clos.forEach(clo => {
                    // Use clo.clo_name (now available) instead of clo.clo
                    cloDataList[clo.clo_id] = {
                        cloName: clo.clo_name,  // Now using clo_name
                        description: clo.description,
                        weightage: clo.weightage
                    };
                });
                console.log("Populated CLO data list:", cloDataList); // Check this
                generateTable();  // Assuming this generates the table using cloDataList
            } else {
                console.error("Error: Unable to fetch CLOs", data.message);
            }
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
 

function validateStep1() {
    let assessmentType = document.getElementById("assessmenttype").value;
    let courseId = document.getElementById("course_id").value;
    let weightage = document.getElementById("totalWeightage").value;

     console.log("Assessment Type:", assessmentType);
    console.log("Course ID:", courseId);
    console.log("Weightage:", weightage);

    if (assessmentType === "Choose Assessment Type" || courseId === "" || weightage === "") {
        alert("⚠️ Please select an assessment type, course, and weightage before proceeding.");
        return;
    }

    // Save Assessment Information
    saveAssessmentInfo(assessmentType, courseId, weightage);

    // ✅ Move to Step 2
    showStep(3);
}
function saveAssessmentInfo(assessmentType, courseId, weightage) {
    fetch("save_assessment.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            assessmentType: assessmentType,
            courseId: courseId,
            weightage: weightage
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log("✅ Assessment information saved successfully.");

            // Store the received assessment ID
            let assessmentId = data.assessment_id; 

            // Set hidden input fields (make sure these exist in your HTML)
            document.getElementById("assessmentId").value = assessmentId;
            document.getElementById("courseId").value = courseId;

            console.log("Assessment ID:", assessmentId);
            console.log("Course ID:", courseId);

            // Save to localStorage after saving to the server
            let assessmentData = {
                assessmentId: assessmentId, // Now including the ID
                assessmentType: assessmentType,
                courseCode: courseId,
                assessmentWeightage: weightage,
                questions: [] // You will populate this later
            };
            localStorage.setItem("assessmentData", JSON.stringify(assessmentData));

            // ✅ Ensure step transition happens AFTER IDs are stored
            showStep(3);
        } else {
            alert("❌ Error saving assessment information.");
        }
    })
    .catch(error => console.error("Error saving assessment info:", error));
}



function validateStep2() {
    if (skip) {
        let confirmSkip = confirm("Are you sure you want to skip this step? Make sure CLO weightages are already defined.");
        if (confirmSkip) {
            console.log("⏭️ Step 2 skipped. Proceeding to Step 3.");
            showStep(3);
        }
        return;
    }

    let totalWeight = 0;

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


// Function for Step 2: Update the overall progress bar
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


let isProgressBarsCreated = false;
let hasUserSelectedCourse = false;
let lastSelectedCourseId = null; // Track the last selected course

// Function to create progress bars
function createProgressBars() {
    if (!hasUserSelectedCourse) {
        console.log("⚠️ User has not selected a course. Skipping progress bar creation.");
        return;
    }

    const courseId = document.getElementById("course_id")?.value.trim();
    if (!courseId || courseId === "101") {
        console.log("❌ Course ID is missing or default. Cannot create progress bars.");
        return;
    }

    if (isProgressBarsCreated) {
        console.log("✅ Progress bars have already been created. Skipping...");
        return;
    }

    console.log("🔄 Creating Progress Bars...");

    const progressBarsContainer = document.getElementById("progressBarsContainer");
    if (!progressBarsContainer) {
        console.error("❌ Error: Element with ID 'progressBarsContainer' not found.");
        return;
    }

    const url = `get_assigned_clo_weight.php?courseId=${encodeURIComponent(courseId)}`;
    console.log("🌐 Fetching CLOs from URL:", url);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error("❌ Error fetching CLO progress data:", data.error);
                return;
            }

            console.log("🔍 Data fetched:", data.clos);

            const uniqueCLOs = Array.from(new Set(data.clos.map(clo => clo.clo_id)))
                .map(id => data.clos.find(clo => clo.clo_id === id));

            console.log("🔍 Unique CLOs:", uniqueCLOs);

            progressBarsContainer.innerHTML = ""; // Clear previous progress bars

            uniqueCLOs.forEach(clo => {
                let assignedWeight = parseFloat(clo.assignedWeight) || 0;
                let totalWeightage = parseFloat(clo.totalWeightage) || 1;
                let percentage = (assignedWeight / totalWeightage) * 100;

                let progressBarItem = document.createElement("div");
                progressBarItem.classList.add("progress-bar-item");

                if (percentage >= 100) {
                    progressBarItem.innerHTML = `
                        <label>${clo.name} (100%) - CLO weightage is complete, cannot assign more weightage.</label>
                    `;
                } else {
                    progressBarItem.innerHTML = `
                        <label>${clo.name} (${percentage.toFixed(2)}%)</label>
                        <div class="progress">
                            <div class="progress-fill" style="width: ${percentage}%;"></div>
                        </div>
                    `;
                }

                progressBarsContainer.appendChild(progressBarItem);
            });

            console.log("✅ Progress bars created successfully.");
            isProgressBarsCreated = true;
        })
        .catch(error => {
            console.error("❌ Error fetching CLO progress:", error);
        });
}

// Event listener for course selection
document.getElementById("course_id")?.addEventListener("change", (event) => {
    const selectedValue = event.target.value.trim();

    if (selectedValue === lastSelectedCourseId) {
        console.log("🔁 Same course reselected. Skipping...");
        return; // Prevent duplicate calls
    }

    lastSelectedCourseId = selectedValue; // Store the last selected course

    if (selectedValue && selectedValue !== "101") {
        hasUserSelectedCourse = true;
        isProgressBarsCreated = false; // Reset flag
        document.getElementById("progressBarsContainer").innerHTML = ''; // Clear previous progress bars
        createProgressBars();
    } else {
        hasUserSelectedCourse = false;
    }
});



function updateProgressBars() {
    console.log("🔄 Updating Progress Bars...");

    const progressBarsContainer = document.getElementById("progressBarsContainer");
    if (!progressBarsContainer) {
        console.error("❌ Error: Element with ID 'progressBarsContainer' not found.");
        return;
    }

    const courseId = document.getElementById("courseId")?.value.trim();
    if (!courseId) {
        console.error("❌ Error: Course ID is missing. Cannot update progress bars.");
        return;
    }

    const url = `get_assigned_clo_weight.php?courseId=${encodeURIComponent(courseId)}`;
    console.log("🌐 Fetching CLOs from URL:", url); // Log the URL

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error("❌ Error fetching CLO progress data:", data.error);
                return;
            }

            console.log("🔍 Data fetched:", data.clos);

            // Remove duplicates by creating a unique set of CLOs (e.g., based on CLO clo_id)
            const uniqueCLOs = Array.from(new Set(data.clos.map(clo => clo.clo_id)))
                .map(id => data.clos.find(clo => clo.clo_id === id));

            console.log("🔍 Unique CLOs:", uniqueCLOs);

            uniqueCLOs.forEach(clo => {
                let assignedWeight = parseFloat(clo.assignedWeight) || 0;
                let totalWeightage = parseFloat(clo.totalWeightage) || 1;
                let percentage = (assignedWeight / totalWeightage) * 100;

                let progressBarItems = progressBarsContainer.querySelectorAll(".progress-bar-item");
                progressBarItems.forEach(item => {
                    const label = item.querySelector("label");
                    if (label && label.textContent.includes(clo.name)) {
                        const progressFill = item.querySelector(".progress-fill");
                        if (progressFill) {
                            progressFill.style.width = `${percentage}%`;
                        }
                    }
                });
            });

            console.log("✅ Progress bars updated successfully.");
        })
        .catch(error => {
            console.error("❌ Error fetching CLO progress:", error);
        });
}

function updateRemainingCLOWeightage(cloId, questionId) {
    let weightageInput = document.getElementById(`weight_${questionId}_${cloId}`);
    let remainingWeightElement = document.getElementById(`remainingWeight_${questionId}_${cloId}`);

    // Handle empty or invalid values gracefully
    let newWeightage = parseFloat(weightageInput.value) || 0;

    // Fetch the remaining weightage from the database
    const courseId = document.getElementById("courseId")?.value.trim();
    if (!courseId) {
        console.error("❌ Error: Course ID is missing. Cannot update remaining weightage.");
        return;
    }

    const url = `get_remaining_weightage.php?courseId=${encodeURIComponent(courseId)}&cloId=${encodeURIComponent(cloId)}`;
    console.log("🌐 Fetching remaining weightage from URL:", url); // Log the URL

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error("❌ Error fetching remaining weightage:", data.error);
                return;
            }

            let totalWeightage = parseFloat(data.totalWeightage) || 0; // Ensure it's a valid number
            let assignedWeightage = parseFloat(data.assignedWeightage) || 0; // Ensure it's a valid number

            // Calculate the remaining weightage
            let remainingWeightage = totalWeightage - assignedWeightage;

            // If newWeightage is added (input is filled), adjust the remaining weightage
            remainingWeightage -= newWeightage;

            // Ensure the remaining weightage does not go below 0
            if (remainingWeightage < 0) remainingWeightage = 0;

            // Update the remaining weightage display immediately after input change
            remainingWeightElement.textContent = `Remaining: ${remainingWeightage}%`;

            // Save the new weightage in the database (optional step)
            // sendUpdatedWeightageToDatabase(cloId, questionId, newWeightage);
        })
        .catch(error => {
            console.error("❌ Error fetching remaining weightage:", error);
        });

    // Update the progress bars after the weightage change
    updateProgressBars();
}


function handleCLOSelection(selectElement) {
    let selectedCLO = selectElement.value; // The selected CLO ID
    let questionId = selectElement.getAttribute("data-question"); // The question ID
    let cloDetailsContainer = document.getElementById(`cloDetails_${questionId}`); // The container for displaying CLO details

    if (!selectedCLO) {
        cloDetailsContainer.innerHTML = ""; // Clear details if no CLO is selected
        return;
    }

    // Fetch CLO details from getclos.php
    const url = `handlecloselection.php?cloId=${encodeURIComponent(selectedCLO)}`;
    console.log("🌐 Fetching CLO details from URL:", url); // Log the URL

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert("❌ Error fetching CLO data: " + data.error);
                return;
            }

            let totalWeightage = data.weightage || 0; // Retrieve weightage from the response
            let description = data.description || ""; // Retrieve description from the response
            let cloName = data.cloName || ""; // Retrieve CLO name from the response

            let savedWeightageData = JSON.parse(localStorage.getItem("cloWeights")) || {}; // Retrieve saved weightage data from localStorage
            let savedWeightage = (savedWeightageData[questionId] && savedWeightageData[questionId][selectedCLO]) || 0; // Get saved weightage for the specific question and CLO

            // Calculate remaining weightage for this CLO
            let remainingWeightage = totalWeightage - savedWeightage;

            // Generate the HTML content for the CLO details
            let detailsHTML = `
                <table class="cloInfoTable">
                    <tr>
                        <td><strong>CLO Name:</strong></td>
                        <td>${cloName}</td>
                    </tr>
                    <tr>
                        <td><strong>Description:</strong></td>
                        <td>${description}</td>
                    </tr>
                    <tr>
                        <td><strong>Weightage:</strong></td>
                        <td>
                            <input type="number" 
                                   id="weight_${questionId}_${selectedCLO}" 
                                   min="0" 
                                   max="${remainingWeightage}" 
                                   value="${savedWeightage}" 
                                   oninput="updateProgressBars();updateRemainingCLOWeightage('${selectedCLO}', '${questionId}')">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span id="remainingWeight_${questionId}_${selectedCLO}">Remaining: ${remainingWeightage}%</span>
                        </td>
                    </tr>
                </table>
                <button onclick="saveCLOWeight('${questionId}', '${selectedCLO}', '${description}')">Save</button>
            `;

            // Update the CLO details container with the generated HTML
            cloDetailsContainer.innerHTML = detailsHTML;
        })
        .catch(error => {
            console.error("❌ Error fetching CLO data:", error);
        });
}


// Update CLO weight (Step 2)
function updateCLOWeight(clo, value) {
    cloWeights[clo] = parseFloat(value) || 0; // Parse the input value for CLO weight

    console.log("Updated CLO Weights:", cloWeights);

    // Call the function to update progress bar in Step 2 after the weights are updated
    updateProgressBar();

    // Call the function to update individual CLO progress bars in Step 3
   /* updateProgressBars(); */
}



function showStep(step) {
    // Hide all steps
    document.querySelectorAll(".step").forEach(div => div.style.display = "none");
    
    // Show the selected step
    const targetStep = document.getElementById(`step${step}`);
    if (targetStep) {
        targetStep.style.display = "block";
    }

    // Special handling for Step 3
    if (step === 3) {
        console.log("🔄 Moving to Step 3. Using CLOs:", cloWeights);
        generateTable();
    }
}

function prevStep(step) {
    showStep(step);
}

document.getElementById('numQuestions').addEventListener('input', function() {
    generateTable(); // Call function instantly when typing
});

document.getElementById('numQuestions').addEventListener('keydown', function(event) {
    if (event.key === "Enter") {
        generateTable(); // Also trigger on pressing Enter
    }
});

function generateTable() {
    console.log("Generating Table...");

    createProgressBars();
    const numQuestionsInput = document.getElementById('numQuestions');
    const numQuestions = parseInt(numQuestionsInput.value);

    if (isNaN(numQuestions) || numQuestions <= 0) {
        document.getElementById('questionsTableContainer').innerHTML = ""; // Clear table if invalid input
        return;
    }

    let clos = Object.keys(cloDataList); // Use cloDataList populated by fetchCLOs
    console.log("Retrieved CLOs:", clos);

    // Remove duplicates right here
    clos = [...new Set(clos)];

    if (clos.length === 0) {
        alert("⚠️ No CLOs found. Please go back and select a course!");
        return;
    }

    let tableHTML = `<table>
        <thead>
            <tr>
                <th>Questions</th>
                <th>Mapped CLO</th>
                <th>Marks</th> <!-- Added Marks Column -->
            </tr>
        </thead>
        <tbody>`;

    for (let i = 1; i <= numQuestions; i++) {
        tableHTML += `<tr>
            <td>Q${i}</td>
            <td>
                <select class="cloDropdown" data-question="Q${i}" onchange="handleCLOSelection(this)">
                    <option value="">Select CLO</option>`;

        // Populate the CLO dropdown with CLO names
        clos.forEach(cloId => {
            const clo = cloDataList[cloId];
            tableHTML += `<option value="${cloId}">${clo.cloName}</option>`; // Use clo.cloName to display the CLO name
        });

        tableHTML += `</select>
                <div id="cloDetails_Q${i}" class="cloDetailsContainer"></div>
            </td>
            <td>
                <input type="number" class="questionMarks" id="marks_Q${i}" min="1" placeholder="Enter Marks">
            </td>
        </tr>`;
    }

    tableHTML += `</tbody></table>`;

    let container = document.getElementById('questionsTableContainer');
    container.innerHTML = tableHTML;
    container.style.display = "block";

    console.log("✅ Table fully generated with CLO dropdowns and marks input.");
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

// Function to handle saving CLO weightage

// Global variable to store questions and CLO weightages
let assessmentQuestions = [];

function saveCLOWeight(questionId, cloId) {
    let weightInput = document.getElementById(`weight_${questionId}_${cloId}`);
    let weightage = parseFloat(weightInput.value) || 0;

    if (weightage <= 0) {
        alert("⚠️ Please enter a valid weightage.");
        return;
    }

    let marksInput = document.getElementById(`marks_${questionId}`);
    let questionMarks = parseInt(marksInput.value) || 0;

    if (questionMarks <= 0) {
        alert("⚠️ Please enter valid marks for this question.");
        return;
    }

    let assessmentIdField = document.getElementById("assessmentId");
    if (!assessmentIdField) {
        alert("⚠️ Assessment ID not found. Make sure Step 1 is completed.");
        return;
    }

    let assessmentId = assessmentIdField.value.trim();
    if (!assessmentId) {
        alert("⚠️ Assessment ID is missing. Save Step 1 first.");
        return;
    }

    // Convert Q1, Q2, etc. to numeric IDs (e.g., 1, 2, etc.)
    let numericQuestionId = questionId.replace(/\D/g, ''); 

    // Fetch CLO name and description
    fetch("get_clo_id.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ clo_id: cloId })  // Sending CLO ID to fetch name and description
    })
    .then(response => response.json())
    .then(cloData => {
        console.log("CLO Data:", cloData);

        if (cloData.success && cloData.clo_name) {
            let cloName = cloData.clo_name; // Get the CLO name

            // Store the weightage and marks for this question
            let questionIndex = assessmentQuestions.findIndex(q => q.questionNumber === numericQuestionId);

            if (questionIndex === -1) {
                // If the question doesn't exist, create it
                assessmentQuestions.push({
                    questionNumber: numericQuestionId,
                    questionMarks: questionMarks,
                    cloWeightages: [{ cloId, cloName, weightage }]
                });
            } else {
                // If the question exists, push the CLO weightage to it
                assessmentQuestions[questionIndex].cloWeightages.push({ cloId, cloName, weightage });
                assessmentQuestions[questionIndex].questionMarks = questionMarks;
            }

            // Save to localStorage
            localStorage.setItem("assessmentQuestions", JSON.stringify(assessmentQuestions));

            // Save the question first
            return fetch("save_question_data.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    assessment_id: assessmentId,         // Correctly send assessment ID
                    question_number: numericQuestionId,  // Correctly send question number (numeric)
                    question_marks: questionMarks       // Correctly send question marks
                })
            });
        } else {
            throw new Error("CLO not found.");
        }
    })
    .then(response => response.json())
    .then(saveResponse => {
        if (saveResponse.success) {
            console.log("Question saved successfully.");

            // Now save the CLO assignment for the question
            return fetch("save_clo_assignment.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    question_id: saveResponse.question_id, // Use the returned question_id
                    clo_id: cloId,
                    weightage: weightage
                })
            });
        } else {
            throw new Error("Error saving question.");
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`✅ Weightage saved for CLO in Question ${numericQuestionId}`);
            assignedWeights[cloId] = (assignedWeights[cloId] || 0) + weightage;
            localStorage.setItem("assignedWeights", JSON.stringify(assignedWeights));
        } else {
            throw new Error(`Error saving CLO assignment: ${data.error}`);
        }
    })
    .catch(error => {
        console.error(error);
        alert(`❌ ${error.message}`);
    });
}


function saveWeightage() {
    let courseId = document.getElementById("course_id").value;
    if (!courseId) {
        alert("⚠️ Please select a course before saving.");
        return;
    }

    let cloWeightages = [];
    document.querySelectorAll("#cloTableBody tr").forEach(row => {
        let cloId = row.dataset.cloId; // Assuming we have a data attribute storing CLO ID
        let weightage = row.querySelector("input").value || 0;

        cloWeightages.push({
            clo: cloId,
            weightage: weightage
        });
    });

    fetch("save_weightage.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ course_id: courseId, clo_weightages: cloWeightages })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("✅ Weightages saved successfully!");
        } else {
            alert("❌ Error saving weightages. Try again.");
        }
    })
    .catch(error => console.error("Error:", error));
}

document.addEventListener("DOMContentLoaded", function () {
    // Save values to localStorage when Step 1 is completed
    document.querySelector("#step1 button").addEventListener("click", function () {
        let assessmentType = document.getElementById("assessmenttype").value;
        let courseCode = document.getElementById("course_id").value;
        let assessmentWeightage = parseFloat(document.getElementById("totalWeightage").value);

        // Check if assessmentWeightage is a valid number
        if (isNaN(assessmentWeightage) || assessmentWeightage <= 0) {
            alert("⚠️ Please enter a valid assessment weightage.");
            return;
        }

        // Store Step 1 values to localStorage
        localStorage.setItem("assessmenttype", assessmentType);
        localStorage.setItem("course_id", courseCode);
        localStorage.setItem("totalWeightage", assessmentWeightage);

        console.log("Step 1 data saved:", { assessmentType, courseCode, assessmentWeightage });
        console.log("Local Storage after Step 1:", {
            assessmentType: localStorage.getItem("assessmenttype"),
            courseCode: localStorage.getItem("course_id"),
            assessmentWeightage: localStorage.getItem("totalWeightage")
        });
    });

    // Saving values to Step 3
    document.querySelector("#step3 button:nth-of-type(2)").addEventListener("click", function () {
        // Retrieve Step 1 details from localStorage
        let assessmentType = localStorage.getItem("assessmenttype") || "";
        let courseCode = localStorage.getItem("course_id") || "";
        let assessmentWeightage = parseFloat(localStorage.getItem("totalWeightage") || "0");

        console.log("Assessment Type from localStorage:", assessmentType);
        console.log("Course Code from localStorage:", courseCode);
        console.log("Assessment Weightage from localStorage:", assessmentWeightage);

        // Validate Step 1 data
        if (!assessmentType || !courseCode || isNaN(assessmentWeightage) || assessmentWeightage <= 0) {
            alert("⚠️ Please complete Step 1 first and ensure all fields are filled.");
            return;
        }

        // Retrieve saved questions and CLO weightages from localStorage
        let questionsData = JSON.parse(localStorage.getItem("assessmentQuestions")) || [];

        console.log("Questions Data from localStorage:", questionsData);

        // Validation for number of questions
        let numQuestions = questionsData.length;
        if (numQuestions <= 0) {
            alert("Please enter at least one question with CLO weightages.");
            return;
        }

        // Merge Step 1 and Step 3 data into the final assessment object
        let assessmentData = {
            assessmentType,
            courseCode,
            assessmentWeightage,
            questions: questionsData
        };

        console.log("Final Assessment Data:", assessmentData);

        // Save the merged assessmentData to localStorage
        localStorage.setItem("assessmentData", JSON.stringify(assessmentData));

        // Log the saved data from localStorage
        console.log("Saved Assessment Data from localStorage:", localStorage.getItem("assessmentData"));

        // Redirect to assessment.html
        window.location.href = "assessment.html";
    });
});
</script>
</body>
</html>