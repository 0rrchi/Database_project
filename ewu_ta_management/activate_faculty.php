<?php
/*
------------------------------------------------------
EWU TA Management System

Activate Faculty Account

------------------------------------------------------
*/

include "includes/db_conn.php";

$message = "";


/*=========================================
        ACTIVATE ACCOUNT
=========================================*/

if(isset($_POST['activate']))
{

    $faculty_id = trim($_POST['faculty_id']);

    $email = trim($_POST['email']);

    $password = $_POST['password'];


    /*=========================================
            FIND FACULTY
    =========================================*/

    $stmt = $conn->prepare(

        "SELECT
            FacultyID,
            Email,
            IsActivated

         FROM Faculty

         WHERE FacultyID=?
         AND Email=?"

    );


    $stmt->bind_param(

        "ss",

        $faculty_id,

        $email

    );


    $stmt->execute();


    $result = $stmt->get_result();


    /*=========================================
            CHECK FACULTY
    =========================================*/

    if($result->num_rows == 1)
    {

        $faculty = $result->fetch_assoc();


        /*=========================================
                ALREADY ACTIVATED
        =========================================*/

        if($faculty['IsActivated'] == 1)
        {

            $message = "Account is already activated.";

        }


        /*=========================================
                ACTIVATE ACCOUNT
        =========================================*/

        else
        {

            $stmt = $conn->prepare(

                "UPDATE Faculty

                 SET
                    Password=?,
                    IsActivated=1

                 WHERE FacultyID=?
                 AND Email=?"

            );


            $stmt->bind_param(

                "sss",

                $password,

                $faculty_id,

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

        $message = "Faculty ID and email do not match our records.";

    }

}

?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Activate Faculty Account</title>

<link rel="stylesheet"
      href="css/style.css">

</head>


<body>


<div class="login-container">


<div class="login-card">


<h2>
Activate Faculty Account
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


<!-- ================= FACULTY ID ================= -->

<label>
Faculty ID
</label>


<input
    type="text"
    name="faculty_id"
    placeholder="Enter Faculty ID"
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

← Back to Login

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