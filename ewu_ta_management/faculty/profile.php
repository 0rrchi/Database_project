<?php
/*
------------------------------------------------------
EWU TA Management System

Faculty Profile

------------------------------------------------------
*/

include "../includes/db_conn.php";
$required_role = 'faculty';
include "../includes/session.php";


$faculty_id = $_SESSION['user_id'];


/*=========================================
        LOAD FACULTY
=========================================*/

$stmt = $conn->prepare("
    SELECT
        Faculty.*,
        Department.DepartmentName
    FROM Faculty
    JOIN Department
    ON Faculty.DepartmentID = Department.DepartmentID
    WHERE Faculty.FacultyID=?
");


$stmt->bind_param(
    "s",
    $faculty_id
);


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

<title>Faculty Profile</title>

<link rel="stylesheet"
      href="../css/style.css">

</head>


<body>


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
My Profile
</h2>


<br>


<?php

if ($faculty) {

?>


<table>


<tr>

<th>
Faculty ID
</th>

<td>

<?php

echo htmlspecialchars(
    $faculty['FacultyID']
);

?>

</td>

</tr>



<tr>

<th>
Name
</th>

<td>

<?php

echo htmlspecialchars(
    $faculty['FirstName'] . " " .
    $faculty['LastName']
);

?>

</td>

</tr>



<tr>

<th>
Email
</th>

<td>

<?php

echo htmlspecialchars(
    $faculty['Email']
);

?>

</td>

</tr>



<tr>

<th>
Designation
</th>

<td>

<?php

echo htmlspecialchars(
    $faculty['Designation']
);

?>

</td>

</tr>



<tr>

<th>
Department
</th>

<td>

<?php

echo htmlspecialchars(
    $faculty['DepartmentName']
);

?>

</td>

</tr>



<tr>

<th>
TA Recruitment
</th>

<td>

<?php

if ($faculty['RecruitingTA']) {

    echo "Open";

} else {

    echo "Closed";

}

?>

</td>

</tr>



<tr>

<th>
Account Status
</th>

<td>

<?php

if ($faculty['IsActivated']) {

    echo "Activated";

} else {

    echo "Not Activated";

}

?>

</td>

</tr>


</table>


<br>


<a href="edit_profile.php"
   class="btn">

Edit Profile

</a>


<?php

} else {

?>


<p style="color:red;">

Faculty information could not be found.

</p>


<?php

}

?>


</div>


</div>



<footer>

&copy; 2026 East West University

</footer>


</body>

</html>