<?php
/*
------------------------------------------------------
EWU TA Management System

Apply for TA

------------------------------------------------------
*/

include "../includes/db_conn.php";
$required_role = 'student';
include "../includes/session.php";


$student_id = $_SESSION['user_id'];

$message = "";
$eligible_faculty = [];

$is_accepted_ta = false;


/*======================================================
        LOAD STUDENT
======================================================*/

$stmt = $conn->prepare(
    "SELECT
        StudentID,
        FirstName,
        LastName,
        CGPA
     FROM Student
     WHERE StudentID=?"
);

$stmt->bind_param(
    "s",
    $student_id
);

$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

$stmt->close();


/*======================================================
        CHECK STUDENT
======================================================*/

if (!$student)
{
    $message = "Student account not found.";
}


/*======================================================
        CHECK IF STUDENT IS ALREADY AN ACCEPTED TA
======================================================*/

if ($student)
{

    $stmt = $conn->prepare(
        "SELECT ApplicationID
         FROM TA_Application
         WHERE StudentID=?
         AND Status='Accepted'
         LIMIT 1"
    );

    $stmt->bind_param(
        "s",
        $student_id
    );

    $stmt->execute();

    $accepted_result = $stmt->get_result();


    if ($accepted_result->num_rows > 0)
    {
        $is_accepted_ta = true;
    }


    $stmt->close();
}


/*======================================================
        APPLY FOR TA
======================================================*/

if (
    isset($_POST['apply']) &&
    $student &&
    !$is_accepted_ta
)
{

    $faculty_id = trim($_POST['faculty_id']);


    /*--------------------------------------------------
            CHECK CGPA
    --------------------------------------------------*/

    if ($student['CGPA'] <= 3.50)
    {

        $message =
            "You must have a CGPA greater than 3.50 to apply.";

    }
    else
    {


        /*--------------------------------------------------
                CHECK FACULTY IS RECRUITING
        --------------------------------------------------*/

        $stmt = $conn->prepare(
            "SELECT
                FacultyID,
                FirstName,
                LastName
             FROM Faculty
             WHERE FacultyID=?
             AND RecruitingTA=TRUE"
        );

        $stmt->bind_param(
            "s",
            $faculty_id
        );

        $stmt->execute();

        $faculty =
            $stmt->get_result()->fetch_assoc();

        $stmt->close();


        if (!$faculty)
        {

            $message =
                "This faculty is not currently recruiting TAs.";

        }
        else
        {


            /*--------------------------------------------------
                    CHECK REQUIRED COURSE
            --------------------------------------------------*/

            $stmt = $conn->prepare(
                "SELECT
                    Faculty_Course.FacultyCourseID

                 FROM Faculty_Course

                 INNER JOIN Student_Course
                 ON Faculty_Course.CourseID =
                    Student_Course.CourseID

                 WHERE Faculty_Course.FacultyID=?
                 AND Student_Course.StudentID=?

                 LIMIT 1"
            );

            $stmt->bind_param(
                "ss",
                $faculty_id,
                $student_id
            );

            $stmt->execute();

            $course_result =
                $stmt->get_result();

            $stmt->close();


            if ($course_result->num_rows == 0)
            {

                $message =
                    "You have not taken any course currently taught by this faculty.";

            }
            else
            {


                /*--------------------------------------------------
                        CHECK DUPLICATE APPLICATION
                --------------------------------------------------*/

                $check = $conn->prepare(
                    "SELECT
                        ApplicationID
                     FROM TA_Application
                     WHERE StudentID=?
                     AND FacultyID=?
                     LIMIT 1"
                );

                $check->bind_param(
                    "ss",
                    $student_id,
                    $faculty_id
                );

                $check->execute();

                $existing =
                    $check->get_result();


                if ($existing->num_rows > 0)
                {

                    $message =
                        "You have already applied to this faculty.";

                }
                else
                {


                    /*==================================================
                            FILE UPLOADS
                    ==================================================*/

                    $resume = "";
                    $cover_letter = "";


                    /*--------------------------------------------------
                            CHECK UPLOAD DIRECTORY
                    --------------------------------------------------*/

                    $upload_dir = "../uploads/";


                    if (!is_dir($upload_dir))
                    {
                        mkdir(
                            $upload_dir,
                            0777,
                            true
                        );
                    }


                    /*--------------------------------------------------
                            UPLOAD CV
                    --------------------------------------------------*/

                    if (
                        isset($_FILES['resume']) &&
                        $_FILES['resume']['error'] == 0
                    )
                    {

                        $extension =
                            strtolower(
                                pathinfo(
                                    $_FILES['resume']['name'],
                                    PATHINFO_EXTENSION
                                )
                            );


                        if ($extension != "pdf")
                        {

                            $message =
                                "CV must be a PDF file.";

                        }
                        else
                        {

                            $resume =
                                time()
                                . "_CV_"
                                . uniqid()
                                . ".pdf";


                            if (
                                !move_uploaded_file(
                                    $_FILES['resume']['tmp_name'],
                                    $upload_dir . $resume
                                )
                            )
                            {

                                $message =
                                    "Unable to upload CV.";

                            }

                        }

                    }
                    else
                    {

                        $message =
                            "Please upload your CV.";

                    }


                    /*--------------------------------------------------
                            UPLOAD COVER LETTER
                    --------------------------------------------------*/

                    if ($message == "")
                    {

                        if (
                            isset($_FILES['cover_letter']) &&
                            $_FILES['cover_letter']['error'] == 0
                        )
                        {

                            $extension =
                                strtolower(
                                    pathinfo(
                                        $_FILES['cover_letter']['name'],
                                        PATHINFO_EXTENSION
                                    )
                                );


                            if ($extension != "pdf")
                            {

                                $message =
                                    "Cover Letter must be a PDF file.";

                            }
                            else
                            {

                                $cover_letter =
                                    time()
                                    . "_CoverLetter_"
                                    . uniqid()
                                    . ".pdf";


                                if (
                                    !move_uploaded_file(
                                        $_FILES['cover_letter']['tmp_name'],
                                        $upload_dir . $cover_letter
                                    )
                                )
                                {

                                    $message =
                                        "Unable to upload Cover Letter.";

                                }

                            }

                        }
                        else
                        {

                            $message =
                                "Please upload your Cover Letter.";

                        }

                    }


                    /*==================================================
                            INSERT APPLICATION
                    ==================================================*/

                    if ($message == "")
                    {

                        $stmt = $conn->prepare(
                            "INSERT INTO TA_Application
                            (
                                StudentID,
                                FacultyID,
                                ResumeFile,
                                CoverLetter,
                                ApplicationDate,
                                Status
                            )
                            VALUES
                            (
                                ?,
                                ?,
                                ?,
                                ?,
                                CURDATE(),
                                'Pending'
                            )"
                        );


                        $stmt->bind_param(
                            "ssss",
                            $student_id,
                            $faculty_id,
                            $resume,
                            $cover_letter
                        );


                        if ($stmt->execute())
                        {

                            $message =
                                "Application submitted successfully.";

                        }
                        else
                        {

                            $message =
                                "Unable to submit application.";

                        }


                        $stmt->close();

                    }

                }

                $check->close();

            }

        }

    }

}


/*======================================================
        RECHECK ACCEPTED STATUS

        This is important after submitting an application.
======================================================*/

if ($student)
{

    $stmt = $conn->prepare(
        "SELECT ApplicationID
         FROM TA_Application
         WHERE StudentID=?
         AND Status='Accepted'
         LIMIT 1"
    );

    $stmt->bind_param(
        "s",
        $student_id
    );

    $stmt->execute();

    $accepted_result =
        $stmt->get_result();


    if ($accepted_result->num_rows > 0)
    {
        $is_accepted_ta = true;
    }


    $stmt->close();

}


/*======================================================
        LOAD ELIGIBLE FACULTY
======================================================*/

/*
    Faculty is shown only if:

    1. RecruitingTA = TRUE
    2. Student CGPA > 3.50
    3. Student has taken a course taught by that faculty
    4. Student has NOT already applied to that faculty
    5. Student is NOT already an accepted TA
*/


if (
    $student &&
    $student['CGPA'] > 3.50 &&
    !$is_accepted_ta
)
{

   $faculty_result = $conn->prepare(
    "SELECT
        f.FacultyID,
        f.FirstName,
        f.LastName,
        f.Designation

     FROM Faculty f

     JOIN Faculty_Course fc
     ON f.FacultyID = fc.FacultyID

     JOIN Student_Course sc
     ON fc.CourseID = sc.CourseID

     WHERE sc.StudentID = ?
     AND f.RecruitingTA = TRUE

     AND f.FacultyID NOT IN
     (
         SELECT FacultyID
         FROM TA_Application
         WHERE StudentID = ?
     )

     GROUP BY
        f.FacultyID,
        f.FirstName,
        f.LastName,
        f.Designation

     HAVING COUNT(DISTINCT fc.CourseID) =
     (
         SELECT COUNT(DISTINCT fc2.CourseID)
         FROM Faculty_Course fc2
         WHERE fc2.FacultyID = f.FacultyID
     )

     ORDER BY
        f.FirstName,
        f.LastName"
);


    $faculty_result->bind_param(
        "ss",
        $student_id,
        $student_id
    );


    $faculty_result->execute();


    $faculty_data =
        $faculty_result->get_result();


    while (
        $row =
        $faculty_data->fetch_assoc()
    )
    {

        $eligible_faculty[] = $row;

    }


    $faculty_result->close();

}


/*======================================================
        LOAD PREVIOUS APPLICATIONS
======================================================*/

/*
    IMPORTANT:

    If the student has already been accepted,
    previous applications are NOT shown.

    This also means the student only sees
    applications while they are still applying.
*/


$applications = null;


if (
    $student &&
    !$is_accepted_ta
)
{

    $applications = $conn->prepare(
        "SELECT
            TA_Application.ApplicationID,
            TA_Application.FacultyID,
            TA_Application.ApplicationDate,
            TA_Application.Status,
            Faculty.FirstName,
            Faculty.LastName

         FROM TA_Application

         INNER JOIN Faculty
         ON TA_Application.FacultyID =
            Faculty.FacultyID

         WHERE TA_Application.StudentID=?

         ORDER BY
            TA_Application.ApplicationID DESC"
    );


    $applications->bind_param(
        "s",
        $student_id
    );


    $applications->execute();


    $application_result =
        $applications->get_result();

}
else
{

    $application_result = null;

}

?>


<!DOCTYPE html>


<html lang="en">


<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>
Apply for TA
</title>

<link
    rel="stylesheet"
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


<!-- ================= MAIN ================= -->


<div class="container">


<!-- ================= APPLY CARD ================= -->


<div class="card">


<h2>
Apply for TA
</h2>

<br>


<?php

if ($message != "")
{

?>

<p
    style="
        color:
        <?php
        echo (
            strpos(
                $message,
                "successfully"
            ) !== false
        )
        ? "green"
        : "red";
        ?>;
    "
>

<strong>

<?php

echo htmlspecialchars(
    $message
);

?>

</strong>

</p>

<br>

<?php

}

?>


<?php

if (!$student)
{

?>

<p style="color:red;">

Student account not found.

</p>

<?php

}
else
{

?>


<p>

<strong>
Student:
</strong>

<?php

echo htmlspecialchars(
    $student['FirstName']
    . " "
    . $student['LastName']
);

?>

</p>


<p>

<strong>
CGPA:
</strong>

<?php

echo htmlspecialchars(
    $student['CGPA']
);

?>

</p>


<br>


<?php

/*======================================================
        ACCEPTED TA MESSAGE
======================================================*/

if ($is_accepted_ta)
{

?>

<p style="color:green;">

<strong>
You are already accepted as a Teaching Assistant.
</strong>

</p>

<br>

<p>

You cannot submit any more TA applications.

</p>

<?php

}


/*======================================================
        CGPA CHECK
======================================================*/

else if ($student['CGPA'] <= 3.50)
{

?>

<p style="color:red;">

You must have a CGPA greater than 3.50 to apply.

</p>

<?php

}


/*======================================================
        NO FACULTY
======================================================*/

else if (count($eligible_faculty) == 0)
{

?>

<p>

No eligible faculty is currently available.

</p>

<br>

<p>

You may have already applied to all eligible faculty
members, or no faculty is currently recruiting.

</p>

<?php

}


/*======================================================
        SHOW APPLICATION FORM
======================================================*/

else
{

?>


<form
    method="POST"
    enctype="multipart/form-data"
    class="crud-form">


<!-- ================= FACULTY ================= -->


<label>
Faculty
</label>


<select
    name="faculty_id"
    required>


<option value="">
Select Faculty
</option>


<?php

foreach (
    $eligible_faculty
    as $faculty
)
{

?>


<option
    value="<?php

        echo htmlspecialchars(
            $faculty['FacultyID']
        );

    ?>">


<?php

echo htmlspecialchars(
    $faculty['FirstName']
    . " "
    . $faculty['LastName']
);


if (
    $faculty['Designation'] != ""
)
{

    echo
        " - "
        . htmlspecialchars(
            $faculty['Designation']
        );

}

?>


</option>


<?php

}

?>


</select>


<br>


<!-- ================= CV ================= -->


<label>
Upload CV (PDF)
</label>


<input
    type="file"
    name="resume"
    accept=".pdf"
    required>


<br>


<!-- ================= COVER LETTER ================= -->


<label>
Upload Cover Letter (PDF)
</label>


<input
    type="file"
    name="cover_letter"
    accept=".pdf"
    required>


<br>


<input
    type="submit"
    name="apply"
    value="Submit Application"
    class="btn">


</form>


<?php

}

?>

<?php

}

?>


</div>


<br>


<!-- ================= PREVIOUS APPLICATIONS ================= -->


<?php

if (
    $student &&
    !$is_accepted_ta
)
{

?>


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
Application Date
</th>

<th>
Status
</th>

</tr>


<?php

if (
    $application_result &&
    $application_result->num_rows > 0
)
{

    while (
        $row =
        $application_result->fetch_assoc()
    )
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
    $row['ApplicationDate']
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['Status']
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

No applications submitted.

</td>

</tr>


<?php

}

?>


</table>


</div>


<?php

}

?>


</div>


<!-- ================= FOOTER ================= -->


<footer>

&copy; 2026 East West University

</footer>


</body>

</html>