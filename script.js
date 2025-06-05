document.addEventListener("DOMContentLoaded", () => {
    const courseSelect = document.getElementById('course_id');

    // Fetch courses for the logged-in instructor
    fetch('addassessment.php') // Adjust this to the correct path to your PHP script
        .then(response => response.json())
        .then(courses => {
            courseSelect.innerHTML = ''; // Clear existing options

            // Check if the response contains an error
            if (courses.error) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = courses.error; // Display error message in dropdown
                courseSelect.appendChild(option);
                return; // Stop further execution
            }

            // Populate the dropdown with courses
            if (courses.length > 0) {
                courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course; // Use course ID as the value
                    option.textContent = course; // Display course ID in the dropdown
                    courseSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No courses available'; // No courses found message
                courseSelect.appendChild(option);
            }
        })
        .catch(error => {
            console.error('Error fetching courses:', error);
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Failed to fetch courses. Please try again later.'; // Error message
            courseSelect.appendChild(option);
        });

    // Handle course selection to fetch and display CLOs
    courseSelect.addEventListener('change', function () {
        const course_id = this.value;
        const cloTableBody = document.getElementById('cloTableBody');
        const headerRow = document.getElementById('questionsHeaderRow');

        cloTableBody.innerHTML = ''; // Clear existing CLOs
        while (headerRow.children.length > 3) {
            headerRow.removeChild(headerRow.lastChild); // Remove any previous CLO headers
        }

        if (course_id) {
            fetch(`getclos.php?course_id=${course_id}`)
                .then(response => response.json())
                .then(clo => {
                    if (clo.length > 0) {
                        // Update CLO headers
                        updateCLOHeaders(clo);

                        clo.forEach(clo => {
                            // Add each CLO as a row in the CLO table for selection and weightage
                            const row = document.createElement('tr');
                            const cloCell = document.createElement('td');
                            cloCell.textContent = `CLO ${clo.clo}`;
                            row.appendChild(cloCell);

                            const descCell = document.createElement('td');
                            descCell.textContent = clo.description;
                            row.appendChild(descCell);

                            const weightageCell = document.createElement('td');
                            const input = document.createElement('input');
                            input.type = 'number';
                            input.placeholder = 'Enter weightage';
                            input.name = `clo${clo.clo}-weightage`;
                            input.oninput = () => updateCLOWeightage(`clo${clo.clo}`, input.value);
                            weightageCell.appendChild(input);
                            row.appendChild(weightageCell);

                            cloTableBody.appendChild(row);
                        });
                        document.getElementById('cloTableContainer').style.display = 'block';
                    } else {
                        cloTableBody.innerHTML = '<tr><td colspan="3">No CLOs found for this course</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching CLOs:', error);
                    cloTableBody.innerHTML = '<tr><td colspan="3">Error fetching CLOs</td></tr>';
                });
        } else {
            document.getElementById('cloTableContainer').style.display = 'none';
        }
    });
});

// Function to update CLO headers based on fetched CLOs
function updateCLOHeaders(cloArray) {
    const headerRow = document.getElementById('questionsHeaderRow');
    cloArray.forEach(clo => {
        const headerCell = document.createElement('th');
        headerCell.textContent = `CLO ${clo.clo}`; // Use template literals
        headerRow.appendChild(headerCell);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const courseId = 123; // Replace with your course ID dynamically (e.g., from a dropdown)
    fetchCLOs(courseId);
});

function fetchCLOs(courseId) {
    fetch(`getclos.php?course_id=${courseId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch CLOs: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error('Error:', data.error);
            } else {
                populateCLODropdown(data);
            }
        })
        .catch(error => console.error('Error fetching CLOs:', error));
}

function populateCLODropdown(data) {
    const cloDropdown = document.getElementById('cloDropdown');
    cloDropdown.innerHTML = '<option value="" disabled selected>Select a CLO</option>'; // Reset options
    data.forEach(clo => {
        const option = document.createElement('option');
        option.value = clo.clo; // Use the CLO field as the value
        option.textContent = `${clo.clo}: ${clo.description}`;
        cloDropdown.appendChild(option);
    });
}
