<?php
/*
------------------------------------------------------
EWU TA Management System

Faculty - My Courses

------------------------------------------------------
*/

include "../includes/db_conn.php";
$required_role = 'faculty';
include "../includes/session.php";

$faculty_id = $_SESSION['user_id'];


/*
        LOAD MY COURSES
*/

$stmt = $conn->prepare("
    SELECT
        Faculty_Course.FacultyCourseID,
        Course.CourseCode,
        Course.CourseTitle,
        Course.Credit

    FROM Faculty_Course

    JOIN Course
    ON Faculty_Course.CourseID = Course.CourseID

    WHERE Faculty_Course.FacultyID = ?

    ORDER BY
        Course.CourseCode
");


$stmt->bind_param(
    "s",
    $faculty_id
);


$stmt->execute();


$courses = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>My Courses</title>

<link
    rel="stylesheet"
    href="../css/style.css"
>

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


<!-- ================= MAIN ================= -->

<div class="container">


<div class="card">

<h2>
My Assigned Courses
</h2>

<br>


<table>

<tr>

<th>
Course Code
</th>

<th>
Course Title
</th>

<th>
Credit
</th>

</tr>


<?php

if ($courses->num_rows > 0)
{

    while ($row = $courses->fetch_assoc())
    {

?>

<tr>

<td>

<?php

echo htmlspecialchars(
    $row['CourseCode']
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['CourseTitle']
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['Credit']
);

?>

</td>

</tr>

<?php

    }

}
else
{

?>

<tr>

<td colspan="3">

No courses assigned.

</td>

</tr>

<?php

}

?>

</table>

</div>

</div>


<!-- ================= FOOTER ================= -->

<footer>

&copy; 2026 East West University

</footer>


</body>

</html>