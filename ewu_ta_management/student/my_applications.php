<?php
/*
EWU TA Management System

My Applications
*/
include "../includes/db_conn.php";
$required_role = 'student';
include "../includes/session.php";
$student_id = $_SESSION['user_id'];
/*
        LOAD APPLICATIONS
*/
$stmt = $conn->prepare(
"SELECT
  TA_Application.ApplicationID,
    TA_Application.ApplicationDate,
    TA_Application.Status,
    TA_Application.ResumeFile,
    TA_Application.CoverLetter,
    Faculty.FacultyID,
    Faculty.FirstName,
    Faculty.LastName,
    Faculty.Designation
FROM TA_Application
JOIN Faculty
ON TA_Application.FacultyID
=
Faculty.FacultyID
WHERE TA_Application.StudentID=?
ORDER BY TA_Application.ApplicationDate DESC"
);
$stmt->bind_param(
    "s",
    $student_id
);
$stmt->execute();
$applications = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">
<title>My Applications</title>
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
<div class="container">
<div class="card">
<h2>
My Applications
</h2>
<br>
<table>
<tr>
<th>
Faculty
</th>
<th>
Designation
</th>
<th>
Applied Date
</th>
<th>
Status
</th>
<th>
CV
</th>
<th>
Cover Letter
</th>
</tr>
<?php
if($applications->num_rows == 0)
{
?>
<tr>
<td colspan="6">
No applications submitted yet.
</td>
</tr>
<?php
}
while($row = $applications->fetch_assoc())
{
?>
<tr>
<td>
<?php
echo htmlspecialchars(
    $row['FirstName']
    . " "
    . $row['LastName']
);
?>
</td>
<td>
<?php
echo htmlspecialchars(
    $row['Designation']
);
?>
</td>
<td>
<?php
echo htmlspecialchars(
    $row['ApplicationDate']
);
?>
</td>
<td>
<?php
if($row['Status'] == "Pending")
{
echo "<span style='color:orange;'>
Pending
</span>";
}
elseif($row['Status'] == "Accepted")
{
echo "<span style='color:green;'>
Accepted
</span>";
}
elseif($row['Status'] == "Rejected")
{
echo "<span style='color:red;'>
Rejected
</span>";
}
else
{
echo htmlspecialchars(
    $row['Status']
);
}
?>
</td>
<td>
<?php
if($row['ResumeFile'] != "")
{
?>
<a
href="../uploads/<?php echo urlencode($row['ResumeFile']); ?>"
target="_blank">
View CV
</a>
<?php
}
else
{
echo "No CV";
}
?>
</td>
<td>
<?php
if($row['CoverLetter'] != "")
{
?>
<a
href="../uploads/<?php echo urlencode($row['CoverLetter']); ?>"
target="_blank">
View Cover Letter
</a>
<?php
}
else
{
echo "No Cover Letter";
}
?>
</td>
</tr>
<?php
}
?>
</table>
</div>
</div>
<footer>
&copy; 2026 East West University
</footer>
</body>
</html>