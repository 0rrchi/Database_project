<?php
/*
EWU TA Management System
Faculty - View Applications
*/
include "../includes/db_conn.php";
$required_role = 'faculty';
include "../includes/session.php";
$faculty_id = $_SESSION['user_id'];
$message = "";
$message_type = "success";
/*
        ACCEPT / REJECT APPLICATION
*/
if (
    isset($_POST['application_id']) &&
    isset($_POST['action'])
)
{
    $application_id = intval(
        $_POST['application_id']
    );

    $action = $_POST['action'];
    /*
        Only Accepted or Rejected are allowed
    */
    if (
        $action == "Accepted" ||
        $action == "Rejected"
    )
    {
               /*
            Make sure the application belongs
            to the logged-in faculty.
        */
        $check = $conn->prepare("
            SELECT
                ApplicationID,
                StudentID
            FROM TA_Application
            WHERE ApplicationID=?
            AND FacultyID=?
            AND Status='Pending'
        ");
      $check->bind_param(
            "is",
            $application_id,
            $faculty_id
        );
        $check->execute();
        $check_result = $check->get_result();
        $application = $check_result->fetch_assoc();
        $check->close();
        if ($application)
        {
            $student_id = $application['StudentID'];
           /*
                ACCEPT APPLICATION
            */
            if ($action == "Accepted")
            {
                /*
                    Start transaction so all related
                    updates happen together.
                */
                $conn->begin_transaction();
                try
                {
                    /*
                        Accept this application.
                    */
                    $stmt = $conn->prepare("
                        UPDATE TA_Application
                        SET Status='Accepted'
                        WHERE ApplicationID=?
                        AND FacultyID=?
                    ");
                    $stmt->bind_param(
                        "is",
                        $application_id,
                        $faculty_id
                    );
                   if (!$stmt->execute())
                    {
                        throw new Exception(
                            "Unable to accept application."
                        );
                    }

                    $stmt->close();
                    /*
                        Automatically close TA recruitment
                        for this faculty.
                    */

                    $stmt = $conn->prepare("
                        UPDATE Faculty
                        SET RecruitingTA=FALSE
                        WHERE FacultyID=?
                    ");

                    $stmt->bind_param(
                        "s",
                        $faculty_id
                    );

                    if (!$stmt->execute())
                    {
                        throw new Exception(
                            "Unable to close TA recruitment."
                                                   );
                    }
                    $stmt->close();
                    /*
                        Reject all other pending applications
                        made by this student to other faculties.

                        This prevents the same student from
                        being selected by another faculty.
                    */
                    $stmt = $conn->prepare("
                        UPDATE TA_Application
                        SET Status='Rejected'
                        WHERE StudentID=?
                        AND FacultyID<>?
                        AND Status='Pending'
                    ");
                    $stmt->bind_param(
                        "ss",
                        $student_id,
                        $faculty_id
                    );
                    if (!$stmt->execute())
                    {
                        throw new Exception(
                            "Unable to update other applications."
                        );
                    }
                    $stmt->close();
                   /*
                        Everything succeeded.
                    */
                    $conn->commit();
                    $message =
                        "Application accepted successfully. "
                        . "TA recruitment has been automatically closed.";

                    $message_type = "success";
                }
                catch (Exception $e)
                {
                    $conn->rollback();

                    $message =
                        "Unable to accept application.";

                    $message_type = "error";
                }
            }
            /*
                REJECT APPLICATION
            */
            else
            {
                $stmt = $conn->prepare("
                    UPDATE TA_Application
                    SET Status='Rejected'
                    WHERE ApplicationID=?
                    AND FacultyID=?
                ");
                $stmt->bind_param(
                    "is",
                    $application_id,
                    $faculty_id
                );

                if ($stmt->execute())
                {
                    $message =
                        "Application rejected successfully.";

                    $message_type = "success";
                }
                else
                {

                    $message =
                        "Unable to reject application.";
                   $message_type = "error";

                }
                $stmt->close();
            }
        }
        else
        {

            $message =
                "Invalid application or application has already been processed.";

            $message_type = "error";

        }
    }
    else
    {

        $message =
            "Invalid application action.";
        $message_type = "error";

    }
}
/*
        TOGGLE TA RECRUITMENT

        Recruitment belongs to Faculty
*/
if (isset($_POST['toggle_recruitment']))
{
   /*
        Check whether this faculty has already
        accepted a TA.

        If yes, recruitment cannot be reopened.
    */
    $check = $conn->prepare("
        SELECT ApplicationID
        FROM TA_Application
        WHERE FacultyID=?
        AND Status='Accepted'
        LIMIT 1
    ");
    $check->bind_param(
        "s",
        $faculty_id
    );
    $check->execute();
    $accepted_result = $check->get_result();
    $has_accepted_ta =
        ($accepted_result->num_rows > 0);
    $check->close();
    if ($has_accepted_ta)
    {
       /*
            Recruitment is permanently closed
            for this faculty after selecting a TA.
        */
        $stmt = $conn->prepare("
            UPDATE Faculty
            SET RecruitingTA=FALSE
            WHERE FacultyID=?
        ");
        $stmt->bind_param(
            "s",
            $faculty_id
        );
        $stmt->execute();
        $stmt->close();
        $message =
            "TA recruitment cannot be opened again because you have already selected a TA.";

        $message_type = "error";
    }
    else
    {
        /*
            Faculty has not selected a TA yet,
            so normal open/close operation is allowed.
        */
        $stmt = $conn->prepare("
            UPDATE Faculty
            SET RecruitingTA = NOT RecruitingTA
            WHERE FacultyID=?
        ");
        $stmt->bind_param(
            "s",
            $faculty_id
        );
        if ($stmt->execute())
        {
            $message =
                "TA recruitment status updated successfully.";

            $message_type = "success";
        }
        else
        {            $message =
                "Unable to update TA recruitment status.";

            $message_type = "error";
        }
       $stmt->close();

    }
}
/*
        LOAD FACULTY RECRUITMENT STATUS
*/
$stmt = $conn->prepare("
    SELECT RecruitingTA
    FROM Faculty
    WHERE FacultyID=?
");
$stmt->bind_param(
    "s",
    $faculty_id
);
$stmt->execute();
$result = $stmt->get_result();
$faculty = $result->fetch_assoc();
$stmt->close();
$recruiting_ta = false;
if ($faculty)
{
    $recruiting_ta =
        (bool)$faculty['RecruitingTA'];
}
/*
        CHECK WHETHER FACULTY
        ALREADY SELECTED A TA
*/
$stmt = $conn->prepare("
    SELECT ApplicationID
    FROM TA_Application
    WHERE FacultyID=?
    AND Status='Accepted'
    LIMIT 1
");
$stmt->bind_param(
    "s",
    $faculty_id
);
$stmt->execute();
$accepted_ta_result = $stmt->get_result();
$has_accepted_ta =
    ($accepted_ta_result->num_rows > 0);
$stmt->close();
/*
        LOAD APPLICATIONS
*/
$stmt = $conn->prepare("
    SELECT
        TA_Application.ApplicationID,
        TA_Application.ApplicationDate,
        TA_Application.Status,
        TA_Application.ResumeFile,
        TA_Application.CoverLetter,
        Student.StudentID,
        Student.FirstName,
        Student.LastName,
       Student.Email,
        Student.Phone,
        Student.DateOfBirth,
        Student.CGPA,
        Department.DepartmentName
    FROM TA_Application
    JOIN Student
    ON Student.StudentID =
       TA_Application.StudentID
    JOIN Department
    ON Student.DepartmentID =
       Department.DepartmentID
    WHERE TA_Application.FacultyID=?
    AND
    (
        TA_Application.Status='Accepted'
        OR
        NOT EXISTS
        (
            SELECT 1
            FROM TA_Application AS OtherApplication
            WHERE OtherApplication.StudentID =
                  TA_Application.StudentID
            AND OtherApplication.Status='Accepted'
            AND OtherApplication.FacultyID<>?
        )
    )
    ORDER BY
        TA_Application.ApplicationDate DESC
");
$stmt->bind_param(   "ss", $faculty_id, $faculty_id);
$stmt->execute();
$applications = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1.0">
<title>View Applications</title>
<link rel="stylesheet"
      href="../css/style.css">
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
<a href="my_courses.php">My Courses</a>
<a href="view_applications.php">Applications</a>
<a href="../logout.php">Logout</a>
</nav>
<div class="container">
<?php
if ($message != "")
{
?>
<div class="card">
<p style="color:<?php
echo ($message_type == "error")
    ? "red"
    : "green";
?>;">
<strong><?php echo htmlspecialchars($message); ?></strong>
</p>
</div>
<br>
<?php
}
?>
<!--
        TA RECRUITMENT CONTROL
-->
<div class="card">
<h2>TA Recruitment</h2>
<br>
<?php
if ($has_accepted_ta)
{
?>
<p>
TA recruitment is currently:
<strong style="color:red;">CLOSED</strong>
</p>
<br>
<p style="color:red;">
You have already selected a TA.
TA recruitment cannot be opened again.
</p>
<?php
}
else if ($recruiting_ta)
{
?>
<p>
TA recruitment is currently:
<strong style="color:green;">OPEN</strong>
</p>
<br>
<form method="POST">
<input
    type="submit"
    name="toggle_recruitment"
    value="Close TA Recruitment"
    class="btn">
</form>
<?php
}
else
{
?>
<p>
TA recruitment is currently:
<strong style="color:red;">CLOSED</strong>
</p>
<br>
<form method="POST">
<input
    type="submit"
    name="toggle_recruitment"
    value="Open TA Recruitment"
    class="btn">

</form>
<?php
}
?>
</div>
<br>
<!--
        APPLICATIONS
-->
<div class="card">
<h2>
TA Applications
</h2>
<br>
<div class="table-container">
<table>
<tr>
<th>Student ID</th>
<th>Student Name</th>
<th>Email</th>
<th>Phone</th>
<th>Date of Birth</th>
<th>Department</th>
<th>CGPA</th>
<th>Applied Date</th>
<th>CV</th>
<th>Cover Letter</th>
<th>Status</th> 
<th>Action</th> 
</tr>
<?php
if ($applications->num_rows > 0)
{
    while (
        $row =
        $applications->fetch_assoc()
    )
    {
?>
<tr> <td>
<?php
echo htmlspecialchars(
    $row['StudentID']
);
?>
</td> <td>
<?php
echo htmlspecialchars(
    $row['FirstName']
    . " "
    . $row['LastName']
);
?>
</td> <td>
<?php
echo htmlspecialchars(
    $row['Email']
);
?>
</td> <td>
<?php
if ($row['Phone'] != "")
{
    echo htmlspecialchars(
        $row['Phone']
    );
}
else
{  echo "N/A";}
?>
</td> <td>
<?php
echo htmlspecialchars(
    $row['DateOfBirth']
);
?>
</td> <td>
<?php
echo htmlspecialchars(
    $row['DepartmentName']
);
?>
</td> <td>
<?php
echo htmlspecialchars(
    $row['CGPA']
);
?>
</td> <td>
<?php
echo htmlspecialchars(
    $row['ApplicationDate']
);
?>
</td> <td>
<?php
if ($row['ResumeFile'] != "")
{
?>
<a href="../uploads/<?php  echo rawurlencode( $row['ResumeFile']  );  ?>"target="_blank">View CV</a>
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
if ($row['CoverLetter'] != "")
{
?>
<a href="../uploads/<?php  echo rawurlencode(  $row['CoverLetter']  ); ?>" target="_blank">View Cover Letter</a>
<?php
}
else
{
echo "No Cover Letter";
}
?>
</td>
<td>
<?php
echo htmlspecialchars(
    $row['Status']
);
?>
</td>
<td>
<?php
if ($row['Status'] == "Pending")
{
    /*
        If this faculty has already selected a TA,
        no more students can be accepted.
    */
    if (!$has_accepted_ta)
    {
?>
<form method="POST"
      style="margin-bottom:5px;">
<input
    type="hidden"
    name="application_id"
    value="<?php
        echo $row['ApplicationID'];
    ?>">
<input
    type="hidden"
    name="action"
    value="Accepted">
<input
    type="submit"
    value="Accept"
    class="btn">
</form>
<form method="POST">
<input
    type="hidden"
    name="application_id"
    value="<?php
        echo $row['ApplicationID'];
    ?>">
<input
    type="hidden"
    name="action"
    value="Rejected">
<input
    type="submit"
    value="Reject"
    class="btn">
</form>
<?php
    }
    else
    {
?>
<strong style="color:red;">Recruitment Closed</strong>
<?php
    }
}
else if ($row['Status'] == "Accepted")
{
?>
<strong style="color:green;">Accepted</strong>
<?php
}
else
{
?>
<strong style="color:red;">Rejected</strong>
<?php
}
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
<td colspan="12">
No applications found.
</td>
</tr>
<?php
}
?>
</table>
</div> </div> </div>
<footer>
&copy; 2026 East West University
</footer>
</body>
</html>