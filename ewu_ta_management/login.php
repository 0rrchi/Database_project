<?php
/*
EWU TA Management System
Login

*/
include "includes/db_conn.php";
session_start();
$message = "";
/*
        LOGIN
*/
if(isset($_POST['login']))
{
   $user_id = trim($_POST['user_id']);

    $password = trim($_POST['password']);
    /*
            CHECK ADMIN
    */
    $stmt = $conn->prepare(

        "SELECT
            AdminID,
            FirstName,
            LastName

         FROM Admin

         WHERE AdminID=?
         AND Password=?"
    );
    $stmt->bind_param(
        "ss",
        $user_id,
        $password
    );
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows == 1)
    {
      $admin = $result->fetch_assoc();
        $_SESSION['user_id'] =
            $admin['AdminID'];
        $_SESSION['user_type'] =
            "admin";
        header("Location: admin/dashboard.php");
       exit();
    }
    /*
            CHECK STUDENT
    */
    $stmt = $conn->prepare(
       "SELECT
            StudentID,
            FirstName,
            LastName,
            IsActivated
         FROM Student
         WHERE StudentID=?
         AND Password=?"
    );
    $stmt->bind_param(
        "ss",
        $user_id,
        $password
    );
    $stmt->execute();
    $result = $stmt->get_result();
   if($result->num_rows == 1)
    {
      $student = $result->fetch_assoc();
        if($student['IsActivated'] == 0)
        {
            $message =
                "Your student account has not been activated yet.";
        }
        else
        {
            $_SESSION['user_id'] =
                $student['StudentID'];
            $_SESSION['user_type'] =
                "student";
            header("Location: student/dashboard.php");
            exit();
        }
    }
    else
    {
        /*
                CHECK FACULTY
        */
        $stmt = $conn->prepare(
          "SELECT
                FacultyID,
                FirstName,
                LastName,
                IsActivated
             FROM Faculty
             WHERE FacultyID=?
             AND Password=?"
        );
        $stmt->bind_param(
            "ss",
            $user_id,
            $password
        );
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 1)
        {
           $faculty = $result->fetch_assoc();
            if($faculty['IsActivated'] == 0)
            {
                $message =
                    "Your faculty account has not been activated yet.";
            }
            else
            {
               $_SESSION['user_id'] =
                    $faculty['FacultyID'];

                $_SESSION['user_type'] =
                    "faculty";
                header("Location: faculty/dashboard.php");
               exit();
            }
        }
        else
        {
            $message =
                "Invalid University ID or password.";

        }
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="stylesheet" href="css/style.css">
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
<div class="login-container">
<div class="login-card">
<h2>Login</h2>
<br>
<?php
if($message != "")
{
?>
<p style="color:red;">
<strong>
<?php
echo htmlspecialchars($message);
?>
</strong>
</p>
<br>
<?php
}
?>
<form method="POST">
<label>University ID</label>
<input
    type="text"
    name="user_id"
    placeholder="Enter University ID"
    required>
<br><br>
<label for="password">
Password
</label>
<input
    type="password"
    id="password"
    name="password"
    placeholder="Enter Password"
    required>
<br><br>
<input
    type="submit"
    name="login"
    value="Login"
    class="btn">

</form>
<br>
<hr>
<br>
<p>
<strong>First time using the system?</strong>
</p>
<br>
<a href="activate_student.php">
    Activate Student Account
</a>
<br><br>
<a href="activate_faculty.php">
    Activate Faculty Account
</a>
<br><br>
<a href="index.php">
    ← Back to Home
</a>
</div>
</div>
<footer>
&copy; 2026 East West University
</footer>
<script>
const password =
document.getElementById("password");
password.addEventListener(
    "dblclick",
    function()
    {
       if(password.type === "password")
        {
            password.type = "text";
        }
        else
        {
            password.type = "password";
        }

    }
);
</script>
</body>
</html>