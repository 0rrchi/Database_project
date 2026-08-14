<?php
/*
------------------------------------------------------
EWU TA Management System

Manage Courses
(Admin CRUD)

------------------------------------------------------
*/

include "../includes/db_conn.php";
$required_role = 'admin';
include "../includes/session.php";

$message = "";


/*=========================================
            ADD COURSE
=========================================*/

if(isset($_POST['add']))
{

    $course_code = trim($_POST['course_code']);
    $course_title = trim($_POST['course_title']);
    $credit = $_POST['credit'];
    $department_id = intval($_POST['department_id']);


    /* Check duplicate course code */

    $check = $conn->prepare(
        "SELECT CourseID
         FROM Course
         WHERE CourseCode=?"
    );

    $check->bind_param(
        "s",
        $course_code
    );

    $check->execute();

    $result = $check->get_result();


    if($result->num_rows > 0)
    {

        $message = "Course code already exists.";

    }
    else
    {

        $stmt = $conn->prepare(
            "INSERT INTO Course
            (
                CourseCode,
                CourseTitle,
                Credit,
                DepartmentID
            )
            VALUES
            (?,?,?,?)"
        );


        $stmt->bind_param(
            "ssdi",
            $course_code,
            $course_title,
            $credit,
            $department_id
        );


        if($stmt->execute())
        {

            $message = "Course added successfully.";

        }
        else
        {

            $message = "Unable to add course.";

        }

    }

}


/*=========================================
            DELETE COURSE
=========================================*/

if(isset($_GET['delete']))
{

    $course_id = intval($_GET['delete']);


    try
    {

        $stmt = $conn->prepare(
            "DELETE FROM Course
             WHERE CourseID=?"
        );


        $stmt->bind_param(
            "i",
            $course_id
        );


        if($stmt->execute())
        {

            $message = "Course deleted successfully.";

        }

    }
    catch(mysqli_sql_exception $e)
    {

        $message =
        "Unable to delete course due to a database error.";

    }

}


/*=========================================
            LOAD COURSE FOR EDIT
=========================================*/

$edit = false;

$course_id = "";

$course_code = "";

$course_title = "";

$credit = "";

$department_id = "";


if(isset($_GET['edit']))
{

    $course_id = intval($_GET['edit']);


    $stmt = $conn->prepare(
        "SELECT *
         FROM Course
         WHERE CourseID=?"
    );


    $stmt->bind_param(
        "i",
        $course_id
    );


    $stmt->execute();


    $result = $stmt->get_result();


    if($result->num_rows == 1)
    {

        $row = $result->fetch_assoc();


        $course_code =
            $row['CourseCode'];


        $course_title =
            $row['CourseTitle'];


        $credit =
            $row['Credit'];


        $department_id =
            $row['DepartmentID'];


        $edit = true;

    }

}


/*=========================================
            UPDATE COURSE
=========================================*/

if(isset($_POST['update']))
{

    $course_id =
        intval($_POST['course_id']);


    $course_code =
        trim($_POST['course_code']);


    $course_title =
        trim($_POST['course_title']);


    $credit =
        $_POST['credit'];


    $department_id =
        intval($_POST['department_id']);


    /* Check duplicate course code
       excluding current course */

    $check = $conn->prepare(
        "SELECT CourseID
         FROM Course
         WHERE CourseCode=?
         AND CourseID<>?"
    );


    $check->bind_param(
        "si",
        $course_code,
        $course_id
    );


    $check->execute();


    $result = $check->get_result();


    if($result->num_rows > 0)
    {

        $message =
            "Another course already uses this course code.";

    }
    else
    {

        $stmt = $conn->prepare(
            "UPDATE Course
             SET
                CourseCode=?,
                CourseTitle=?,
                Credit=?,
                DepartmentID=?
             WHERE CourseID=?"
        );


        $stmt->bind_param(
            "ssdii",
            $course_code,
            $course_title,
            $credit,
            $department_id,
            $course_id
        );


        if($stmt->execute())
        {

            $message =
                "Course updated successfully.";

        }
        else
        {

            $message =
                "Unable to update course.";

        }

    }

}


/*=========================================
            LOAD DEPARTMENTS
=========================================*/

$department_result = $conn->query(
    "SELECT
        DepartmentID,
        DepartmentName
     FROM Department
     ORDER BY DepartmentName"
);


/*=========================================
            LOAD COURSES
=========================================*/

$courses = $conn->query(
    "SELECT
        Course.CourseID,
        Course.CourseCode,
        Course.CourseTitle,
        Course.Credit,
        Department.DepartmentName

     FROM Course

     JOIN Department
     ON Course.DepartmentID =
        Department.DepartmentID

     ORDER BY Course.CourseCode"
);

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Manage Courses</title>

<link rel="stylesheet"
      href="../css/style.css">

</head>


<body>


<!-- ================= HEADER ================= -->

<header>

<div class="logo">

    

    <div>

        <h1>East West University</h1>

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


<!-- ================= MAIN CONTENT ================= -->

<div class="container">


<!-- ================= COURSE FORM ================= -->

<div class="card">

    <h2>Manage Courses</h2>

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


    <form method="POST"
          class="crud-form">


        <input
        type="hidden"
        name="course_id"
        value="<?php echo htmlspecialchars($course_id); ?>">


        <!-- COURSE CODE -->

        <label>

            Course Code

        </label>

        <input
        type="text"
        name="course_code"
        value="<?php echo htmlspecialchars($course_code); ?>"
        required>


        <br>


        <!-- COURSE TITLE -->

        <label>

            Course Title

        </label>

        <input
        type="text"
        name="course_title"
        value="<?php echo htmlspecialchars($course_title); ?>"
        required>


        <br>


        <!-- CREDIT -->

        <label>

            Credit

        </label>

        <input
        type="number"
        step="0.5"
        min="1"
        max="5"
        name="credit"
        value="<?php echo htmlspecialchars($credit); ?>"
        required>


        <br>


        <!-- DEPARTMENT -->

        <label>

            Department

        </label>

        <select
        name="department_id"
        required>

            <option value="">

                Select Department

            </option>


            <?php

            while(
                $dept =
                $department_result->fetch_assoc()
            )
            {

            ?>

            <option
            value="<?php
                echo $dept['DepartmentID'];
            ?>"

            <?php

            if(
                $department_id ==
                $dept['DepartmentID']
            )
            {

                echo "selected";

            }

            ?>>

                <?php

                echo htmlspecialchars(
                    $dept['DepartmentName']
                );

                ?>

            </option>

            <?php

            }

            ?>

        </select>


        <br>


        <?php

        if($edit)
        {

        ?>

            <input
            type="submit"
            name="update"
            value="Update Course"
            class="btn">

        <?php

        }
        else
        {

        ?>

            <input
            type="submit"
            name="add"
            value="Add Course"
            class="btn">

        <?php

        }

        ?>

    </form>

</div>


<br>


<!-- ================= COURSE TABLE ================= -->

<div class="card">

    <h2>Course List</h2>

    <br>


    <table>

        <tr>

            <th>ID</th>

            <th>Course Code</th>

            <th>Course Title</th>

            <th>Credit</th>

            <th>Department</th>

            <th>Edit</th>

            <th>Delete</th>

        </tr>


        <?php

        if($courses->num_rows > 0)
        {

            while(
                $row =
                $courses->fetch_assoc()
            )
            {

        ?>

        <tr>

            <td>

                <?php
                echo $row['CourseID'];
                ?>

            </td>


            <td>

                <?php
                echo htmlspecialchars(
                    $row['CourseCode']
                );
                ?>

            </td>


            <td>

                <?php
                echo htmlspecialchars(
                    $row['CourseTitle']
                );
                ?>

            </td>


            <td>

                <?php
                echo htmlspecialchars(
                    $row['Credit']
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

                <a
                href="manage_courses.php?edit=<?php
                    echo $row['CourseID'];
                ?>">

                    Edit

                </a>

            </td>


            <td>

                <a
                href="manage_courses.php?delete=<?php
                    echo $row['CourseID'];
                ?>"
                onclick="return confirm(
                    'Are you sure you want to delete this course?'
                );">

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

            <td colspan="7">

                No courses added yet.

            </td>

        </tr>

        <?php

        }

        ?>

    </table>

</div>


</div>


<!-- ================= FOOTER ================= -->

<footer>

    &copy; 2026 East West University

</footer>


</body>

</html>