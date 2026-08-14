<?php
/*
------------------------------------------------------
EWU TA Management System

Activate Student Account

------------------------------------------------------
*/

include "includes/db_conn.php";

$message = "";


/*=========================================
        ACTIVATE ACCOUNT
=========================================*/

if(isset($_POST['activate']))
{

    $student_id = trim($_POST['student_id']);

    $email = trim($_POST['email']);

    $password = $_POST['password'];


    /*=========================================
            FIND STUDENT
    =========================================*/

    $stmt = $conn->prepare(

        "SELECT
            StudentID,
            Email,
            IsActivated

         FROM Student

         WHERE StudentID=?
         AND Email=?"

    );


    $stmt->bind_param(

        "ss",

        $student_id,

        $email

    );


    $stmt->execute();


    $result = $stmt->get_result();


    /*=========================================
            CHECK STUDENT
    =========================================*/

    if($result->num_rows == 1)
    {

        $student = $result->fetch_assoc();


        /*=========================================
                ALREADY ACTIVATED
        =========================================*/

        if($student['IsActivated'] == 1)
        {

            $message = "Account is already activated.";

        }


        /*=========================================
                ACTIVATE ACCOUNT
        =========================================*/

        else
        {

            $stmt = $conn->prepare(

                "UPDATE Student

                 SET
                    Password=?,
                    IsActivated=1

                 WHERE StudentID=?
                 AND Email=?"

            );


            $stmt->bind_param(

                "sss",

                $password,

                $student_id,

                $email

            );


            if($stmt->execute())
            {

                header("Location: login.php");

                exit();

            }
            else
            {

                $message = "Unable to activate account.";

            }

        }

    }
    else
    {

        $message = "Student ID and email do not match our records.";

    }

}

?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Activate Student Account</title>

<link rel="stylesheet"
      href="css/style.css">

</head>


<body>


<div class="login-container">


<div class="login-card">


<h2>
Activate Student Account
</h2>


<br>


<?php

if($message != "")
{

    echo
    "<p style='color:red;'>
        <strong>"
        . htmlspecialchars($message)
        . "</strong>
    </p>
    <br>";

}

?>


<form method="POST">


<!-- ================= STUDENT ID ================= -->

<label>
Student ID
</label>


<input
    type="text"
    name="student_id"
    placeholder="Enter University ID"
    required>


<br>


<!-- ================= EMAIL ================= -->

<label>
University Email
</label>


<input
    type="email"
    name="email"
    placeholder="Enter your university email"
    required>


<br>


<!-- ================= PASSWORD ================= -->

<label for="password">
Create Password
</label>


<input
    type="password"
    id="password"
    name="password"
    placeholder="Create your password"
    required>


<br><br>


<label>

<input
    type="checkbox"
    onclick="togglePassword()">

Show Password

</label>


<br><br>


<!-- ================= ACTIVATE ================= -->

<input
    type="submit"
    name="activate"
    value="Activate Account"
    class="btn">


</form>


<br>


<a href="login.php">

Back to Login

</a>


</div>


</div>


<script>

function togglePassword()
{

    const password =
        document.getElementById("password");


    password.type =
        password.type === "password"
        ? "text"
        : "password";

}

</script>


</body>

</html>