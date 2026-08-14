<?php
/*
------------------------------------------------------
EWU TA Management System

Admin - Manage Faculty
Full CRUD

------------------------------------------------------
*/

include "../includes/db_conn.php";
$required_role = 'admin';
include "../includes/session.php";

$message = "";
$message_type = "success";


/*======================================================
        ADD FACULTY
======================================================*/

if (isset($_POST['add_faculty']))
{
    $faculty_id  = trim($_POST['faculty_id']);
    $first_name  = trim($_POST['first_name']);
    $last_name   = trim($_POST['last_name']);
    $email       = trim($_POST['email']);
    $designation = trim($_POST['designation']);
    $department  = intval($_POST['department_id']);

    $check = $conn->prepare("
        SELECT FacultyID
        FROM Faculty
        WHERE FacultyID=? OR Email=?
    ");

    $check->bind_param(
        "ss",
        $faculty_id,
        $email
    );

    $check->execute();

    if ($check->get_result()->num_rows > 0)
    {
        $message = "Faculty ID or Email already exists.";
        $message_type = "error";
    }
    else
    {
        $stmt = $conn->prepare("
            INSERT INTO Faculty
            (
                FacultyID,
                FirstName,
                LastName,
                Email,
                Designation,
                IsActivated,
                RecruitingTA,
                DepartmentID
            )
            VALUES
            (?,?,?,?,?,FALSE,FALSE,?)
        ");

        $stmt->bind_param(
            "sssssi",
            $faculty_id,
            $first_name,
            $last_name,
            $email,
            $designation,
            $department
        );

        if ($stmt->execute())
        {
            $message = "Faculty added successfully.";
        }
        else
        {
            $message = "Unable to add faculty.";
            $message_type = "error";
        }

        $stmt->close();
    }

    $check->close();
}


/*======================================================
        UPDATE FACULTY
======================================================*/

if (isset($_POST['update_faculty']))
{
    $faculty_id  = trim($_POST['faculty_id']);
    $first_name  = trim($_POST['first_name']);
    $last_name   = trim($_POST['last_name']);
    $email       = trim($_POST['email']);
    $designation = trim($_POST['designation']);
    $department  = intval($_POST['department_id']);

    $check = $conn->prepare("
        SELECT FacultyID
        FROM Faculty
        WHERE Email=?
        AND FacultyID<>?
    ");

    $check->bind_param(
        "ss",
        $email,
        $faculty_id
    );

    $check->execute();

    if ($check->get_result()->num_rows > 0)
    {
        $message =
            "Another faculty already uses this email.";

        $message_type = "error";
    }
    else
    {
        $stmt = $conn->prepare("
            UPDATE Faculty
            SET
                FirstName=?,
                LastName=?,
                Email=?,
                Designation=?,
                DepartmentID=?
            WHERE FacultyID=?
        ");

        $stmt->bind_param(
            "ssssis",
            $first_name,
            $last_name,
            $email,
            $designation,
            $department,
            $faculty_id
        );

        if ($stmt->execute())
        {
            $message =
                "Faculty updated successfully.";
        }
        else
        {
            $message =
                "Unable to update faculty.";

            $message_type = "error";
        }

        $stmt->close();
    }

    $check->close();
}


/*======================================================
        DELETE FACULTY
======================================================*/

if (isset($_GET['delete_faculty']))
{
    $faculty_id =
        trim($_GET['delete_faculty']);

    /*
        Faculty_Course records will be deleted
        automatically because of ON DELETE CASCADE.
    */

    $stmt = $conn->prepare("
        DELETE FROM Faculty
        WHERE FacultyID=?
    ");

    $stmt->bind_param(
        "s",
        $faculty_id
    );

    if ($stmt->execute())
    {
        $message =
            "Faculty deleted successfully.";
    }
    else
    {
        $message =
            "Unable to delete faculty.";

        $message_type = "error";
    }

    $stmt->close();
}


/*======================================================
        ADD COURSE ASSIGNMENT
======================================================*/

if (isset($_POST['add_assignment']))
{
    $faculty_id =
        trim($_POST['assignment_faculty']);

    $course_id =
        intval($_POST['assignment_course']);

    $check = $conn->prepare("
        SELECT FacultyCourseID
        FROM Faculty_Course
        WHERE FacultyID=?
        AND CourseID=?
    ");

    $check->bind_param(
        "si",
        $faculty_id,
        $course_id
    );

    $check->execute();

    if ($check->get_result()->num_rows > 0)
    {
        $message =
            "This course is already assigned to this faculty.";

        $message_type = "error";
    }
    else
    {
        $stmt = $conn->prepare("
            INSERT INTO Faculty_Course
            (
                FacultyID,
                CourseID
            )
            VALUES
            (?,?)
        ");

        $stmt->bind_param(
            "si",
            $faculty_id,
            $course_id
        );

        if ($stmt->execute())
        {
            $message =
                "Course assigned successfully.";
        }
        else
        {
            $message =
                "Unable to assign course.";

            $message_type = "error";
        }

        $stmt->close();
    }

    $check->close();
}


/*======================================================
        UPDATE COURSE ASSIGNMENT
======================================================*/

if (isset($_POST['update_assignment']))
{
    $faculty_course_id =
        intval($_POST['faculty_course_id']);

    $faculty_id =
        trim($_POST['assignment_faculty']);

    $course_id =
        intval($_POST['assignment_course']);

    $check = $conn->prepare("
        SELECT FacultyCourseID
        FROM Faculty_Course
        WHERE FacultyID=?
        AND CourseID=?
        AND FacultyCourseID<>?
    ");

    $check->bind_param(
        "sii",
        $faculty_id,
        $course_id,
        $faculty_course_id
    );

    $check->execute();

    if ($check->get_result()->num_rows > 0)
    {
        $message =
            "This course is already assigned to this faculty.";

        $message_type = "error";
    }
    else
    {
        $stmt = $conn->prepare("
            UPDATE Faculty_Course
            SET
                FacultyID=?,
                CourseID=?
            WHERE FacultyCourseID=?
        ");

        $stmt->bind_param(
            "sii",
            $faculty_id,
            $course_id,
            $faculty_course_id
        );

        if ($stmt->execute())
        {
            $message =
                "Course assignment updated successfully.";
        }
        else
        {
            $message =
                "Unable to update course assignment.";

            $message_type = "error";
        }

        $stmt->close();
    }

    $check->close();
}


/*======================================================
        DELETE COURSE ASSIGNMENT
======================================================*/

if (isset($_GET['delete_assignment']))
{
    $faculty_course_id =
        intval($_GET['delete_assignment']);

    $stmt = $conn->prepare("
        DELETE FROM Faculty_Course
        WHERE FacultyCourseID=?
    ");

    $stmt->bind_param(
        "i",
        $faculty_course_id
    );

    if ($stmt->execute())
    {
        $message =
            "Course assignment removed successfully.";
    }
    else
    {
        $message =
            "Unable to remove course assignment.";

        $message_type = "error";
    }

    $stmt->close();
}


/*======================================================
        EDIT FACULTY
======================================================*/

$edit_faculty = false;

$edit_id = "";
$edit_first = "";
$edit_last = "";
$edit_email = "";
$edit_designation = "";
$edit_department = "";

if (isset($_GET['edit_faculty']))
{
    $id =
        trim($_GET['edit_faculty']);

    $stmt = $conn->prepare("
        SELECT *
        FROM Faculty
        WHERE FacultyID=?
    ");

    $stmt->bind_param(
        "s",
        $id
    );

    $stmt->execute();

    $result =
        $stmt->get_result();

    if ($result->num_rows == 1)
    {
        $row =
            $result->fetch_assoc();

        $edit_faculty = true;

        $edit_id =
            $row['FacultyID'];

        $edit_first =
            $row['FirstName'];

        $edit_last =
            $row['LastName'];

        $edit_email =
            $row['Email'];

        $edit_designation =
            $row['Designation'];

        $edit_department =
            $row['DepartmentID'];
    }

    $stmt->close();
}


/*======================================================
        EDIT COURSE ASSIGNMENT
======================================================*/

$edit_assignment = false;

$edit_assignment_id = "";
$edit_assignment_faculty = "";
$edit_assignment_course = "";

if (isset($_GET['edit_assignment']))
{
    $id =
        intval($_GET['edit_assignment']);

    $stmt = $conn->prepare("
        SELECT *
        FROM Faculty_Course
        WHERE FacultyCourseID=?
    ");

    $stmt->bind_param(
        "i",
        $id
    );

    $stmt->execute();

    $result =
        $stmt->get_result();

    if ($result->num_rows == 1)
    {
        $row =
            $result->fetch_assoc();

        $edit_assignment = true;

        $edit_assignment_id =
            $row['FacultyCourseID'];

        $edit_assignment_faculty =
            $row['FacultyID'];

        $edit_assignment_course =
            $row['CourseID'];
    }

    $stmt->close();
}


/*======================================================
        LOAD DATA
======================================================*/

$departments = $conn->query("
    SELECT
        DepartmentID,
        DepartmentName
    FROM Department
    ORDER BY DepartmentName
");


$courses = $conn->query("
    SELECT
        CourseID,
        CourseCode,
        CourseTitle
    FROM Course
    ORDER BY CourseCode
");


$faculty_list = $conn->query("
    SELECT
        Faculty.FacultyID,
        Faculty.FirstName,
        Faculty.LastName,
        Faculty.Email,
        Faculty.Designation,
        Faculty.IsActivated,
        Faculty.RecruitingTA,
        Department.DepartmentName

    FROM Faculty

    JOIN Department
        ON Faculty.DepartmentID =
           Department.DepartmentID

    ORDER BY Faculty.FacultyID
");


$assignments = $conn->query("
    SELECT
        Faculty_Course.FacultyCourseID,
        Faculty_Course.FacultyID,
        Faculty_Course.CourseID,

        Faculty.FirstName,
        Faculty.LastName,

        Course.CourseCode,
        Course.CourseTitle

    FROM Faculty_Course

    JOIN Faculty
        ON Faculty_Course.FacultyID =
           Faculty.FacultyID

    JOIN Course
        ON Faculty_Course.CourseID =
           Course.CourseID

    ORDER BY
        Faculty.FacultyID,
        Course.CourseCode
");

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
Manage Faculty
</title>

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

<a href="manage_departments.php">
Departments
</a>

<a href="manage_courses.php">
Courses
</a>

<a href="manage_students.php">
Students
</a>

<a href="manage_faculty.php">
Faculty
</a>

<a href="../logout.php">
Logout
</a>

</nav>


<div class="container">


<!-- ================= MESSAGE ================= -->

<?php

if ($message != "")
{

?>

<div class="card">

<p
style="color:<?php
echo ($message_type == "error")
    ? "red"
    : "green";
?>;"
>

<strong>

<?php
echo htmlspecialchars($message);
?>

</strong>

</p>

</div>

<br>

<?php

}

?>


<!-- ================= FACULTY FORM ================= -->

<div class="card">

<h2>

<?php

echo $edit_faculty
    ? "Edit Faculty"
    : "Add Faculty";

?>

</h2>

<br>


<form
method="POST"
class="crud-form"
>


<label>
Faculty ID
</label>

<input
type="text"
name="faculty_id"
value="<?php
echo htmlspecialchars($edit_id);
?>"

<?php

echo $edit_faculty
    ? "readonly"
    : "required";

?>
>


<label>
First Name
</label>

<input
type="text"
name="first_name"
value="<?php
echo htmlspecialchars($edit_first);
?>"
required
>


<label>
Last Name
</label>

<input
type="text"
name="last_name"
value="<?php
echo htmlspecialchars($edit_last);
?>"
required
>


<label>
University Email
</label>

<input
type="email"
name="email"
value="<?php
echo htmlspecialchars($edit_email);
?>"
required
>


<label>
Designation
</label>

<input
type="text"
name="designation"
value="<?php
echo htmlspecialchars($edit_designation);
?>"
>


<label>
Department
</label>

<select
name="department_id"
required
>

<option value="">
Select Department
</option>

<?php

while (
    $department =
    $departments->fetch_assoc()
)
{

?>

<option
value="<?php
echo $department['DepartmentID'];
?>"

<?php

if (
    $edit_department ==
    $department['DepartmentID']
)
{
    echo "selected";
}

?>
>

<?php

echo htmlspecialchars(
    $department['DepartmentName']
);

?>

</option>

<?php

}

?>

</select>


<?php

if ($edit_faculty)
{

?>

<input
type="submit"
name="update_faculty"
value="Update Faculty"
class="btn"
>

<?php

}
else
{

?>

<input
type="submit"
name="add_faculty"
value="Add Faculty"
class="btn"
>

<?php

}

?>

</form>

</div>


<br>


<!-- ================= FACULTY LIST ================= -->

<div class="card">

<h2>
Faculty List
</h2>

<br>


<div class="table-container">

<table>

<tr>

<th>
Faculty ID
</th>

<th>
Name
</th>

<th>
Email
</th>

<th>
Designation
</th>

<th>
Department
</th>

<th>
Activation
</th>

<th>
Recruitment
</th>

<th>
Edit
</th>

<th>
Delete
</th>

</tr>


<?php

if ($faculty_list->num_rows > 0)
{

while (
    $row =
    $faculty_list->fetch_assoc()
)
{

?>

<tr>

<td>

<?php

echo htmlspecialchars(
    $row['FacultyID']
);

?>

</td>


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
    $row['Email']
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
    $row['DepartmentName']
);

?>

</td>


<td>

<?php

echo $row['IsActivated']
    ? "Activated"
    : "Not Activated";

?>

</td>


<td>

<?php

echo $row['RecruitingTA']
    ? "OPEN"
    : "CLOSED";

?>

</td>


<!-- EDIT -->

<td>

<a
href="manage_faculty.php?edit_faculty=<?php
echo urlencode(
    $row['FacultyID']
);
?>"
>

Edit

</a>

</td>


<!-- DELETE -->

<td>

<a
href="manage_faculty.php?delete_faculty=<?php
echo urlencode(
    $row['FacultyID']
);
?>"

onclick="return confirm(
    'Are you sure you want to delete this faculty?'
);"
>

Delete

</a>

</td>


</tr>

<?php

}

}
else
{

?>

<tr>

<td colspan="9">

No faculty found.

</td>

</tr>

<?php

}

?>

</table>

</div>

</div>


<br>


<!-- ================= COURSE ASSIGNMENT FORM ================= -->

<div class="card">

<h2>

<?php

echo $edit_assignment
    ? "Edit Course Assignment"
    : "Assign Course to Faculty";

?>

</h2>

<br>


<form
method="POST"
class="crud-form"
>


<input
type="hidden"
name="faculty_course_id"
value="<?php
echo htmlspecialchars(
    $edit_assignment_id
);
?>"
>


<label>
Faculty
</label>


<select
name="assignment_faculty"
required
>

<option value="">
Select Faculty
</option>


<?php

$faculty_options = $conn->query("
    SELECT
        FacultyID,
        FirstName,
        LastName
    FROM Faculty
    ORDER BY FacultyID
");


while (
    $faculty =
    $faculty_options->fetch_assoc()
)
{

?>

<option
value="<?php
echo htmlspecialchars(
    $faculty['FacultyID']
);
?>"

<?php

if (
    $edit_assignment_faculty ==
    $faculty['FacultyID']
)
{
    echo "selected";
}

?>
>

<?php

echo htmlspecialchars(
    $faculty['FacultyID']
    . " - "
    . $faculty['FirstName']
    . " "
    . $faculty['LastName']
);

?>

</option>

<?php

}

?>

</select>


<label>
Course
</label>


<select
name="assignment_course"
required
>

<option value="">
Select Course
</option>


<?php

while (
    $course =
    $courses->fetch_assoc()
)
{

?>

<option
value="<?php
echo $course['CourseID'];
?>"

<?php

if (
    $edit_assignment_course ==
    $course['CourseID']
)
{
    echo "selected";
}

?>
>

<?php

echo htmlspecialchars(
    $course['CourseCode']
    . " - "
    . $course['CourseTitle']
);

?>

</option>

<?php

}

?>

</select>


<?php

if ($edit_assignment)
{

?>

<input
type="submit"
name="update_assignment"
value="Update Assignment"
class="btn"
>

<?php

}
else
{

?>

<input
type="submit"
name="add_assignment"
value="Assign Course"
class="btn"
>

<?php

}

?>

</form>

</div>


<br>


<!-- ================= COURSE ASSIGNMENTS ================= -->

<div class="card">

<h2>
Faculty Course Assignments
</h2>

<br>


<div class="table-container">

<table>

<tr>

<th>
Faculty
</th>

<th>
Course
</th>

<th>
Edit
</th>

<th>
Delete
</th>

</tr>


<?php

if ($assignments->num_rows > 0)
{

while (
    $row =
    $assignments->fetch_assoc()
)
{

?>

<tr>


<td>

<?php

echo htmlspecialchars(
    $row['FacultyID']
    . " - "
    . $row['FirstName']
    . " "
    . $row['LastName']
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['CourseCode']
    . " - "
    . $row['CourseTitle']
);

?>

</td>


<!-- EDIT -->

<td>

<a
href="manage_faculty.php?edit_assignment=<?php
echo $row['FacultyCourseID'];
?>"
>

Edit

</a>

</td>


<!-- DELETE -->

<td>

<a
href="manage_faculty.php?delete_assignment=<?php
echo $row['FacultyCourseID'];
?>"

onclick="return confirm(
    'Remove this course assignment?'
);"
>

Delete

</a>

</td>


</tr>

<?php

}

}
else
{

?>

<tr>

<td colspan="4">

No course assignments found.

</td>

</tr>

<?php

}

?>

</table>

</div>

</div>


</div>


<footer>

&copy; 2026 East West University

</footer>


</body>

</html>