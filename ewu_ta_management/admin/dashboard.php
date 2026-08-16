<?php
/*
EWU TA Management System
Administrator Dashboard
*/
include "../includes/db_conn.php";
$required_role = 'admin';
include "../includes/session.php";

/*
        TOTAL STUDENTS
*/
$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM Student"
);

$row = $result->fetch_assoc();

$students = $row['total'];

/*
        TOTAL FACULTY
*/
$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM Faculty"
);

$row = $result->fetch_assoc();

$faculty = $row['total'];

/*
        TOTAL COURSES
*/

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM Course"
);

$row = $result->fetch_assoc();

$courses = $row['total'];

/*
        TOTAL DEPARTMENTS
*/

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM Department"
);

$row = $result->fetch_assoc();

$departments = $row['total'];

/*
        TOTAL APPLICATIONS
*/

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM TA_Application"
);

$row = $result->fetch_assoc();

$applications = $row['total'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"content="width=device-width, initial-scale=1.0">

<title>Administrator Dashboard</title>

<link rel="stylesheet"
      href="../css/style.css">

</head>

<body>

<!-- ================= HEADER ================= -->
<header>
<div class="logo">
<div>
<h1>
East West University
</h1>
<p>
Teaching Assistant Recruitment and Management System
</p>
</div>
</div>
</header>
<!-- ================= NAVIGATION ================= -->
<nav>

<a href="manage_departments.php">
Departments
</a>
<a href="manage_courses.php">
courses
</a>

<a href="manage_students.php">
Students
</a>

<a href="manage_faculty.php">
Faculty
</a>

<a href="../logout.php">
Logout
</a>
</nav>

<!-- ================= MAIN CONTENT ================= -->
<div class="container">
<h2>
Administrator Dashboard
</h2>
<div class="dashboard-grid">
<!-- ================= STUDENTS ================= -->

<div class="dashboard-box">
<h3>
Total Students
</h3>
<h1>
<?php echo $students; ?>
</h1>
</div>
<!-- ================= FACULTY ================= -->
<div class="dashboard-box">
<h3>
Total Faculty
</h3>
<h1>
<?php echo $faculty; ?>
</h1>
</div>
<!-- ================= COURSES ================= -->
<div class="dashboard-box">
<h3>
Total Courses
</h3>
<h1>
<?php echo $courses; ?>
</h1>
</div>
<!-- ================= DEPARTMENTS ================= -->
<div class="dashboard-box">
<h3>
Total Departments
</h3>
<h1>
<?php echo $departments; ?>
</h1>
</div>
<!-- ================= APPLICATIONS ================= -->
<div class="dashboard-box">
<h3>
Total Applications
</h3>
<h1>
<?php echo $applications; ?>
</h1>
</div>
</div>
</div>
<!-- ================= FOOTER ================= -->
<footer>
&copy; 2026 East West University
</footer>
</body>
</html>