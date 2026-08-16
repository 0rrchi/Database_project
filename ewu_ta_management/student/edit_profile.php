<?php
/*
EWU TA Management System
Edit Student Profile
*/
 
include "../includes/db_conn.php";
include "../includes/session.php";
 
$student_id = $_SESSION['user_id'];
 
$message = "";
/*
        UPDATE PROFILE
*/
if(isset($_POST['update']))
{
 
    $email = trim($_POST['email']);
 
    $phone = trim($_POST['phone']);
 
    $password = trim($_POST['password']);
 
    /*-----------------------------------------
            UPDATE WITH NEW PASSWORD
    -----------------------------------------*/
    if($password != "")
    {
 
        $stmt = $conn->prepare(
            "UPDATE Student
             SET Email=?,
                 Phone=?,
                 Password=?
             WHERE StudentID=?"
        );
 
        $stmt->bind_param(
            "ssss",
            $email,
            $phone,
            $password,
            $student_id
        );
    }
 
    /*-----------------------------------------
            UPDATE WITHOUT PASSWORD
    -----------------------------------------*/
    else
    {
        $stmt = $conn->prepare(
            "UPDATE Student
             SET Email=?,
                 Phone=?
             WHERE StudentID=?"
        );
       $stmt->bind_param(
            "sss",
            $email,
            $phone,
            $student_id
        );
    }
    if($stmt->execute())
    {
        $message = "Profile updated successfully.";
    }
    else
    {
        $message = "Unable to update profile."; 
    }
} 
/*
        LOAD STUDENT
*/
$stmt = $conn->prepare(
    "SELECT
        Student.*,
        Department.DepartmentName
     FROM Student
     JOIN Department
     ON Student.DepartmentID=
        Department.DepartmentID
     WHERE Student.StudentID=?"
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
      content="width=device-width,
initial-scale=1.0">
<title>Edit Profile</title> 
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
Teaching Assistant Recruitment and Management
System
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
<h2>
Edit Profile
</h2>
<br>
<?php
if($message != "")
{
    echo
    "<p style='color:green;'>
        <strong>"
        . htmlspecialchars($message) .
        "</strong>
    </p>
    <br>";
}

?>
<form method="POST">
    <table>
        <tr>
            <th>Student ID</th>
            <td>
                <input type="text" value="<?php echo htmlspecialchars($student['StudentID']); ?>" readonly>
            </td>
        </tr>
        <tr>
            <th>Name</th>
            <td>
                <input type="text" value="<?php echo htmlspecialchars($student['FirstName'] . " " . $student['LastName']); ?>" readonly>
            </td>
        </tr>
        <tr>
            <th>Department</th>
            <td>
                <input type="text" value="<?php echo htmlspecialchars($student['DepartmentName']); ?>" readonly>
            </td>
        </tr>
        <tr>
            <th>CGPA</th>
            <td>
                <input type="text" value="<?php echo htmlspecialchars($student['CGPA']); ?>" readonly>
            </td>
        </tr>
        <tr>
            <th>Email</th>
            <td>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['Email']); ?>" required>
            </td>
        </tr>
        <tr>
            <th>Phone</th>
            <td>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($student['Phone']); ?>">
            </td>
        </tr>
        <tr>
            <th>New Password</th>
            <td>
                <input type="password" name="password" placeholder="Leave blank to keep current password">
            </td>
        </tr>
    </table>
    <br>
    <!-- ================= UPDATE ================= -->
    <input type="submit" name="update" value="Update Profile" class="btn">
</form>
<br>
<a href="profile.php" class="btn">
Back to Profile
</a>
</div>
</div>
<!-- ================= FOOTER ================= -->
<footer>
&copy; 2026 East West University
</footer>
</body>
</html>