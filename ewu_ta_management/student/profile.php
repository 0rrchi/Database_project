<?php
/*

EWU TA Management System
Student Profile
*/
include "../includes/db_conn.php";
$required_role = 'student';
include "../includes/session.php";
$student_id=$_SESSION['user_id'];
$stmt=$conn->prepare("SELECT
Student.*,
Department.DepartmentName
FROM Student
JOIN Department
ON Student.DepartmentID=Department.DepartmentID
WHERE StudentID=?");
$stmt->bind_param("s",$student_id);
$stmt->execute();
$result=$stmt->get_result();
$student=$result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Profile</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
<div class="logo">
<div>
<h1>East West University</h1>
<p>Teaching Assistant Recruitment and Management System</p>
</div>
</div>
</header>
<nav>
<a href="dashboard.php">Dashboard</a>
<a href="profile.php">Profile</a>
<a href="apply_ta.php">
Apply for TA
</a>
<a href="my_applications.php">My Applications</a>
<a href="../logout.php">Logout</a>
</nav>
<div class="container">
<div class="card">
<h2>My Profile</h2>
<br>
<table>
<tr>
<th>Student ID</th>
<td><?php echo $student['StudentID']; ?></td>
</tr>
<tr>
<th>Name</th>
<td><?php echo $student['FirstName']." ".$student['LastName']; ?></td>
</tr>
<tr>
<th>Email</th>
<td><?php echo $student['Email']; ?></td>
</tr>
<tr>
<th>Phone</th>
<td><?php echo $student['Phone']; ?></td>
</tr>
<tr>
<th>Date of Birth</th>
<td><?php echo $student['DateOfBirth']; ?></td>
</tr>
<tr>
<th>CGPA</th>
<td><?php echo $student['CGPA']; ?></td>
</tr>
<tr>
<th>Department</th>
<td><?php echo $student['DepartmentName']; ?></td>
</tr>
<tr>
<th>Account Status</th>
<td>
<?php
if($student['IsActivated'])
echo "Activated";
else
echo "Not Activated";
?>
</td>
</tr>
</table>
<br>
<a href="edit_profile.php" class="btn">
Edit Profile
</a>
</div>
</div>
<footer>
&copy; 2026 East West University
</footer>
</body>
</html>