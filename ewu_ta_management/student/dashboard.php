<?php
/*
EWU TA Management System

Student Dashboard
*/
include "../includes/db_conn.php";
$required_role = 'student';
include "../includes/session.php";
$student_id = $_SESSION['user_id'];
$stmt = $conn->prepare(
    "SELECT FirstName, LastName
     FROM Student
     WHERE StudentID=?"
);
$stmt->bind_param(
    "s",
    $student_id
);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1.0">
<title>Student Dashboard</title>
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
<a href="dashboard.php">
Dashboard
</a>
<a href="profile.php">
Profile
</a>
<a href="apply_ta.php">
Apply for TA
</a>
<a href="my_applications.php">
My Applications
</a>
<a href="../logout.php">
Logout
</a>
</nav>
<!-- ================= MAIN CONTENT ================= -->
<div class="container">
<div class="card">
<?php
if($student)
{
?>
<h2>
Welcome,
<?php
echo htmlspecialchars(
    $student['FirstName']
    . " "
    . $student['LastName']
);

?>
</h2>
<br>
<p>
Use the menu above to:
</p>
<br>
<ul>
<li>
View your profile
</li>
<li>
View available TA courses
</li>
<li>
Apply for TA positions
</li>
<li>
Track application status
</li>
<li>
View TA offers
</li>
</ul>
<?php
}
else
{
?>
<h2>
Student Not Found
</h2>
<br>
<p>
Your student account could not be found.
</p>
<?php
}
?>
</div>
</div>
<!-- ================= FOOTER ================= -->
<footer>
&copy; 2026 East West University
</footer>
</body>
</html>