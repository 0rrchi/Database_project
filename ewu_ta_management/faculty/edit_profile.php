<?php
/*
------------------------------------------------------
EWU TA Management System
 
Edit Faculty Profile
 
------------------------------------------------------
*/
 
include "../includes/db_conn.php";
include "../includes/session.php";
 
$faculty_id = $_SESSION['user_id'];
 
$message = "";
$message_type = "";
 
/*
        UPDATE PROFILE
*/
 
if (isset($_POST['update']))
{
    $email = trim($_POST['email']);
    $password = $_POST['password'];
 
    $stmt = $conn->prepare("
        UPDATE Faculty
        SET
            Email=?,
            Password=?
        WHERE FacultyID=?
    ");
 
    $stmt->bind_param(
        "sss",
        $email,
        $password,
        $faculty_id
    );
 
    if ($stmt->execute())
    {
        $message = "Profile updated successfully.";
        $message_type = "success";
    }
    else
    {
        $message = "Profile update failed.";
        $message_type = "error";
    }
 
    $stmt->close();
}
 
/*
        LOAD FACULTY
*/
 
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
 
$stmt->close();
 
?>
 
<!DOCTYPE html>
 
<html lang="en">
 
<head>
 
<meta charset="UTF-8">
 
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 
<title>Edit Faculty Profile</title>
 
<link rel="stylesheet" href="../css/style.css">
 
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
Edit Profile
</h2>
 
<br>
 
<?php
 
if ($message != "")
{
 
    if ($message_type == "success")
    {
 
        echo "<p style='color:green;'>
        <strong>" .
        htmlspecialchars($message) .
        "</strong>
        </p><br>";
 
    }
    else
    {
 
        echo "<p style='color:red;'>
        <strong>" .
        htmlspecialchars($message) .
        "</strong>
        </p><br>";
 
    }
 
}
 
?>
 
<?php
 
if ($faculty)
{
 
?>
 
<form method="POST">
    <table>
        <tr>
            <th>Faculty ID</th>
            <td>
                <input type="text" value="<?php echo htmlspecialchars($faculty['FacultyID']); ?>" readonly>
            </td>
        </tr>

        <tr>
            <th>Name</th>
            <td>
                <input type="text" value="<?php echo htmlspecialchars($faculty['FirstName'] . " " . $faculty['LastName']); ?>" readonly>
            </td>
        </tr>

        <tr>
            <th>Department</th>
            <td>
                <input type="text" value="<?php echo htmlspecialchars($faculty['DepartmentName']); ?>" readonly>
            </td>
        </tr>

        <tr>
            <th>Designation</th>
            <td>
                <input type="text" value="<?php echo htmlspecialchars($faculty['Designation']); ?>" readonly>
            </td>
        </tr>

        <tr>
            <th>Email</th>
            <td>
                <input type="email" name="email" value="<?php echo htmlspecialchars($faculty['Email']); ?>" required>
            </td>
        </tr>

        <tr>
            <th>Password</th>
            <td>
                <input type="text" name="password" value="<?php echo htmlspecialchars($faculty['Password'] ?? ''); ?>" required>
            </td>
        </tr>
    </table>
 
    <br>
 
    <input type="submit" name="update" value="Update Profile" class="btn">
 
</form>
 
<br>
 
<a href="profile.php" class="btn">
Back to Profile
</a>
 
<?php
 
}
else
{
 
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