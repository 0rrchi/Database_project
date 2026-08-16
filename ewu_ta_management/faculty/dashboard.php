<?php
/*
------------------------------------------------------
EWU TA Management System

Faculty Dashboard

------------------------------------------------------
*/

include "../includes/db_conn.php";
$required_role = 'faculty';
include "../includes/session.php";


$faculty_id = $_SESSION['user_id'];


/*
        LOAD FACULTY
*/

$stmt = $conn->prepare("
    SELECT
        FirstName,
        LastName
    FROM Faculty
    WHERE FacultyID=?
");

$stmt->bind_param("s", $faculty_id);

$stmt->execute();

$result = $stmt->get_result();

$faculty = $result->fetch_assoc();

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Faculty Dashboard</title>

<link rel="stylesheet"
      href="../css/style.css">

</head>


<body>


<header>

<div class="logo">




<div>

<h1>East West University</h1>

<p>
Teaching Assistant Recruitment and Management System
</p>

</div>

</div>

</header>



<nav>


<a href="dashboard.php">
Dashboard
</a>


<a href="profile.php">
Profile
</a>


<a href="my_courses.php">
My Courses
</a>


<a href="view_applications.php">
Applications
</a>


<a href="../logout.php">
Logout
</a>


</nav>



<div class="container">


<div class="card">


<h2>

Welcome,

<?php

if ($faculty) {

    echo htmlspecialchars(
        $faculty['FirstName'] . " " .
        $faculty['LastName']
    );

}

?>

</h2>


<br>


<p>
From this dashboard you can:
</p>


<br>


<ul>


<li>
View your profile
</li>


<li>
View the courses assigned to you
</li>


<li>
Open or close TA recruitment
</li>


<li>
View TA applications submitted to you
</li>


<li>
View student information and application documents
</li>


<li>
Accept or reject TA applications
</li>


</ul>


</div>


</div>



<footer>

&copy; 2026 East West University

</footer>


</body>

</html>