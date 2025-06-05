<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title>Outcomes|Instructor Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <!-- Boxicons CDN Link -->
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
          <a href="testobeicourses.php">
            <i class='bx bx-book-alt'></i>
            <span class="link_name">Courses</span>
          </a>
          <ul class="sub-menu blank">
            <li><a class="link_name" href="testobeicourses.php">Courses</a></li>
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
          <h1>Outcomes</h1>
          <small>Home / Outcomes</small>
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
                <a href="obeiaddoutcomes.html"><button class="btn-open-popup"><i class='bx bx-plus'></i>Add Outcomes</button></a>
              </div>
              <div class="browse">
                <input type="search" placeholder="Search" class="record-search">
                <button class="btn-open-popup">Import Data</button>
              </div>
            </div>
          </div>

          <h2 style="color:#020143">Course Learning Outcomes (CLOs)</h2>
          <table id="cloTable" width="100%">
            <thead>
              <tr>
                <th>Course Code</th>
                <th>CLONUM</th>
                <th>CLODESC</th>
                <th>LEVEL</th>
                <th>CLODOMAIN</th>
                <th>PLO</th>
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

                $sql = "SELECT Course_Code, CLONUM, CLODESC, LEVEL, CLODOMAIN, PLO FROM test_clo";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row["Course_Code"]. "</td>
                                <td>" . $row["CLONUM"]. "</td>
                                <td>" . $row["CLODESC"]. "</td>
                                <td>" . $row["LEVEL"]. "</td>
                                <td>" . $row["CLODOMAIN"]. "</td>
                                <td>" . $row["PLO"]. "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No outcomes found</td></tr>";
                }

                $conn->close();
              ?>
            </tbody>
          </table>
        </div>
      </main>
    </section>

    <script>
      let arrow = document.querySelectorAll(".arrow");
      for (var i = 0; i < arrow.length; i++) {
        arrow[i].addEventListener("click", (e) => {
          let arrowParent = e.target.parentElement.parentElement;
          arrowParent.classList.toggle("showMenu");
        });
      }

      let sidebar = document.querySelector(".sidebar");
      let sidebarBtn = document.querySelector(".bx-menu");
      sidebarBtn.addEventListener("click", () => {
        sidebar.classList.toggle("close");
      });
    </script>
  </body>
</html>
