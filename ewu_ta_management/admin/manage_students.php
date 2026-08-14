<?php
/*
------------------------------------------------------
EWU TA Management System

Manage Students
(Admin CRUD + Student Course CRUD)

------------------------------------------------------
*/

include "../includes/db_conn.php";
$required_role = 'admin';
include "../includes/session.php";

$message = "";


/*======================================================
        DELETE STUDENT COURSE
======================================================*/

if(isset($_GET['delete_course']))
{
    $id = intval($_GET['delete_course']);

    $stmt = $conn->prepare(
        "DELETE FROM Student_Course
         WHERE StudentCourseID=?"
    );

    $stmt->bind_param("i", $id);

    if($stmt->execute())
    {
        $message = "Student course deleted successfully.";
    }
    else
    {
        $message = "Unable to delete student course.";
    }
}


/*======================================================
        DELETE STUDENT
======================================================*/

if(isset($_GET['delete']))
{
    $id = trim($_GET['delete']);

    try
    {
        $stmt = $conn->prepare(
            "DELETE FROM Student
             WHERE StudentID=?"
        );

        $stmt->bind_param("s", $id);
        $stmt->execute();

        $message = "Student deleted successfully.";
    }
    catch(mysqli_sql_exception $e)
    {
        $message = "Unable to delete student.";
    }
}


/*======================================================
        ADD STUDENT
======================================================*/

if(isset($_POST['add']))
{
    $student_id    = trim($_POST['student_id']);
    $first_name    = trim($_POST['first_name']);
    $last_name     = trim($_POST['last_name']);
    $email         = trim($_POST['email']);
    $phone         = trim($_POST['phone']);
    $date_of_birth = $_POST['date_of_birth'];
    $cgpa          = floatval($_POST['cgpa']);
    $department_id = intval($_POST['department_id']);
    $is_activated  = intval($_POST['is_activated']);

    /* Check Student ID */
    $check = $conn->prepare(
        "SELECT StudentID
         FROM Student
         WHERE StudentID=?"
    );

    $check->bind_param("s", $student_id);
    $check->execute();

    if($check->get_result()->num_rows > 0)
    {
        $message = "Student ID already exists.";
    }
    else
    {
        /* Check Email */
        $check = $conn->prepare(
            "SELECT StudentID
             FROM Student
             WHERE Email=?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        if($check->get_result()->num_rows > 0)
        {
            $message = "Email already exists.";
        }
        else
        {
            $stmt = $conn->prepare(
                "INSERT INTO Student
                (
                    StudentID,
                    FirstName,
                    LastName,
                    Email,
                    Phone,
                    DateOfBirth,
                    CGPA,
                    DepartmentID,
                    IsActivated
                )
                VALUES(?,?,?,?,?,?,?,?,?)"
            );

            $stmt->bind_param(
                "ssssssdii",
                $student_id,
                $first_name,
                $last_name,
                $email,
                $phone,
                $date_of_birth,
                $cgpa,
                $department_id,
                $is_activated
            );

            if($stmt->execute())
            {
                $message = "Student added successfully.";
            }
            else
            {
                $message = "Unable to add student.";
            }
        }
    }
}


/*======================================================
        UPDATE STUDENT
======================================================*/

if(isset($_POST['update']))
{
    $student_id    = trim($_POST['student_id']);
    $first_name    = trim($_POST['first_name']);
    $last_name     = trim($_POST['last_name']);
    $email         = trim($_POST['email']);
    $phone         = trim($_POST['phone']);
    $date_of_birth = $_POST['date_of_birth'];
    $cgpa          = floatval($_POST['cgpa']);
    $department_id = intval($_POST['department_id']);
    $is_activated  = intval($_POST['is_activated']);

    /* Check email */
    $check = $conn->prepare(
        "SELECT StudentID
         FROM Student
         WHERE Email=?
         AND StudentID<>?"
    );

    $check->bind_param(
        "ss",
        $email,
        $student_id
    );

    $check->execute();

    if($check->get_result()->num_rows > 0)
    {
        $message = "Email already belongs to another student.";
    }
    else
    {
        $stmt = $conn->prepare(
            "UPDATE Student
             SET
                FirstName=?,
                LastName=?,
                Email=?,
                Phone=?,
                DateOfBirth=?,
                CGPA=?,
                DepartmentID=?,
                IsActivated=?
             WHERE StudentID=?"
        );

        $stmt->bind_param(
            "sssssdiss",
            $first_name,
            $last_name,
            $email,
            $phone,
            $date_of_birth,
            $cgpa,
            $department_id,
            $is_activated,
            $student_id
        );

        if($stmt->execute())
        {
            $message = "Student updated successfully.";
        }
        else
        {
            $message = "Unable to update student.";
        }
    }
}


/*======================================================
        ADD STUDENT COURSE
======================================================*/

if(isset($_POST['add_course']))
{
    $student_id   = trim($_POST['course_student_id']);
    $course_id    = intval($_POST['course_id']);
    $semester     = $_POST['semester_name'];
    $year         = intval($_POST['year']);
    $grade        = trim($_POST['grade']);

    /* Check duplicate */
    $check = $conn->prepare(
        "SELECT StudentCourseID
         FROM Student_Course
         WHERE StudentID=?
         AND CourseID=?
         AND SemesterName=?
         AND Year=?"
    );

    $check->bind_param(
        "sisi",
        $student_id,
        $course_id,
        $semester,
        $year
    );

    $check->execute();

    if($check->get_result()->num_rows > 0)
    {
        $message = "This course is already recorded for this student.";
    }
    else
    {
        $stmt = $conn->prepare(
            "INSERT INTO Student_Course
            (
                StudentID,
                CourseID,
                SemesterName,
                Year,
                Grade
            )
            VALUES(?,?,?,?,?)"
        );

        $stmt->bind_param(
            "sisis",
            $student_id,
            $course_id,
            $semester,
            $year,
            $grade
        );

        if($stmt->execute())
        {
            $message = "Course added to student successfully.";
        }
        else
        {
            $message = "Unable to add course.";
        }
    }
}


/*======================================================
        UPDATE STUDENT COURSE
======================================================*/

if(isset($_POST['update_course']))
{
    $student_course_id = intval($_POST['student_course_id']);
    $course_id         = intval($_POST['course_id']);
    $semester          = $_POST['semester_name'];
    $year              = intval($_POST['year']);
    $grade             = trim($_POST['grade']);

    /* Get student ID */
    $check_student = $conn->prepare(
        "SELECT StudentID
         FROM Student_Course
         WHERE StudentCourseID=?"
    );

    $check_student->bind_param(
        "i",
        $student_course_id
    );

    $check_student->execute();

    $course_data = $check_student->get_result()->fetch_assoc();

    if($course_data)
    {
        $student_id = $course_data['StudentID'];

        /* Check duplicate */
        $check = $conn->prepare(
            "SELECT StudentCourseID
             FROM Student_Course
             WHERE StudentID=?
             AND CourseID=?
             AND SemesterName=?
             AND Year=?
             AND StudentCourseID<>?"
        );

        $check->bind_param(
            "sisii",
            $student_id,
            $course_id,
            $semester,
            $year,
            $student_course_id
        );

        $check->execute();

        if($check->get_result()->num_rows > 0)
        {
            $message = "This course is already recorded for this student.";
        }
        else
        {
            $stmt = $conn->prepare(
                "UPDATE Student_Course
                 SET
                    CourseID=?,
                    SemesterName=?,
                    Year=?,
                    Grade=?
                 WHERE StudentCourseID=?"
            );

            $stmt->bind_param(
                "isisi",
                $course_id,
                $semester,
                $year,
                $grade,
                $student_course_id
            );

            if($stmt->execute())
            {
                $message = "Student course updated successfully.";
            }
            else
            {
                $message = "Unable to update student course.";
            }
        }
    }
}


/*======================================================
        EDIT STUDENT
======================================================*/

$edit = false;

$student_id    = "";
$first_name    = "";
$last_name     = "";
$email         = "";
$phone         = "";
$date_of_birth = "";
$cgpa          = "";
$department_id = "";
$is_activated  = 0;


/* Course edit variables */

$course_edit = false;

$edit_course_id = "";
$edit_course_student_id = "";
$edit_course = "";
$edit_semester = "";
$edit_year = "";
$edit_grade = "";


if(isset($_GET['edit']))
{
    $student_id = trim($_GET['edit']);

    $stmt = $conn->prepare(
        "SELECT *
         FROM Student
         WHERE StudentID=?"
    );

    $stmt->bind_param("s", $student_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows == 1)
    {
        $row = $result->fetch_assoc();

        $first_name    = $row['FirstName'];
        $last_name     = $row['LastName'];
        $email         = $row['Email'];
        $phone         = $row['Phone'];
        $date_of_birth = $row['DateOfBirth'];
        $cgpa          = $row['CGPA'];
        $department_id = $row['DepartmentID'];
        $is_activated  = $row['IsActivated'];

        $edit = true;
    }
}


/*======================================================
        EDIT STUDENT COURSE
======================================================*/

if(isset($_GET['edit_course']))
{
    $course_edit = true;

    $id = intval($_GET['edit_course']);

    $stmt = $conn->prepare(
        "SELECT *
         FROM Student_Course
         WHERE StudentCourseID=?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows == 1)
    {
        $row = $result->fetch_assoc();

        $edit_course_id        = $row['StudentCourseID'];
        $edit_course_student_id = $row['StudentID'];
        $edit_course           = $row['CourseID'];
        $edit_semester         = $row['SemesterName'];
        $edit_year             = $row['Year'];
        $edit_grade            = $row['Grade'];

        /* Load student information */
        $student_id = $row['StudentID'];

        $stmt2 = $conn->prepare(
            "SELECT *
             FROM Student
             WHERE StudentID=?"
        );

        $stmt2->bind_param("s", $student_id);
        $stmt2->execute();

        $student_data = $stmt2->get_result()->fetch_assoc();

        if($student_data)
        {
            $first_name    = $student_data['FirstName'];
            $last_name     = $student_data['LastName'];
            $email         = $student_data['Email'];
            $phone         = $student_data['Phone'];
            $date_of_birth = $student_data['DateOfBirth'];
            $cgpa          = $student_data['CGPA'];
            $department_id = $student_data['DepartmentID'];
            $is_activated  = $student_data['IsActivated'];

            $edit = true;
        }
    }
}


/*======================================================
        LOAD DEPARTMENTS
======================================================*/

$department_result = $conn->query(
    "SELECT DepartmentID, DepartmentName
     FROM Department
     ORDER BY DepartmentName"
);


/*======================================================
        LOAD COURSES
======================================================*/

$course_result = $conn->query(
    "SELECT CourseID, CourseCode, CourseTitle
     FROM Course
     ORDER BY CourseCode"
);


/*======================================================
        LOAD STUDENTS
======================================================*/

$students = $conn->query(
    "SELECT
        Student.StudentID,
        Student.FirstName,
        Student.LastName,
        Student.Email,
        Student.Phone,
        Student.DateOfBirth,
        Student.CGPA,
        Student.IsActivated,
        Department.DepartmentName
     FROM Student
     JOIN Department
     ON Student.DepartmentID = Department.DepartmentID
     ORDER BY Student.StudentID"
);


/*======================================================
        LOAD STUDENT COURSES
======================================================*/

$student_courses = $conn->query(
    "SELECT
        Student_Course.StudentCourseID,
        Student_Course.StudentID,
        Student_Course.SemesterName,
        Student_Course.Year,
        Student_Course.Grade,
        Course.CourseCode,
        Course.CourseTitle
     FROM Student_Course
     JOIN Course
     ON Student_Course.CourseID = Course.CourseID
     ORDER BY
        Student_Course.StudentID,
        Student_Course.Year DESC"
);

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Manage Students</title>

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

<a href="dashboard.php">Dashboard</a>

<a href="manage_departments.php">Departments</a>

<a href="manage_courses.php">Courses</a>


<a href="manage_faculty.php">Faculty</a>

<a href="../logout.php">Logout</a>

</nav>


<div class="container">


<!-- ================= MESSAGE ================= -->

<?php

if($message != "")
{
    echo "<p style='color:green;text-align:center;'>
            <strong>" .
            htmlspecialchars($message) .
          "</strong>
          </p><br>";
}

?>


<!-- ================= STUDENT FORM ================= -->

<div class="card">

<h2>
<?php echo $edit ? "Edit Student" : "Add Student"; ?>
</h2>

<br>

<form method="POST" class="crud-form">


<label>Student ID</label>

<input
    type="text"
    name="student_id"
    value="<?php echo htmlspecialchars($student_id); ?>"
    <?php echo $edit ? "readonly" : ""; ?>
    required>


<label>First Name</label>

<input
    type="text"
    name="first_name"
    value="<?php echo htmlspecialchars($first_name); ?>"
    required>


<label>Last Name</label>

<input
    type="text"
    name="last_name"
    value="<?php echo htmlspecialchars($last_name); ?>"
    required>


<label>Email</label>

<input
    type="email"
    name="email"
    value="<?php echo htmlspecialchars($email); ?>"
    required>


<label>Phone</label>

<input
    type="text"
    name="phone"
    value="<?php echo htmlspecialchars($phone); ?>">


<label>Date of Birth</label>

<input
    type="date"
    name="date_of_birth"
    value="<?php echo htmlspecialchars($date_of_birth); ?>"
    required>


<label>CGPA</label>

<input
    type="number"
    name="cgpa"
    step="0.01"
    min="0"
    max="4"
    value="<?php echo htmlspecialchars($cgpa); ?>"
    required>


<label>Department</label>

<select name="department_id" required>

<option value="">Select Department</option>

<?php

while($dept = $department_result->fetch_assoc())
{

?>

<option
    value="<?php echo $dept['DepartmentID']; ?>"
    <?php
    echo ($department_id == $dept['DepartmentID'])
        ? "selected"
        : "";
    ?>>

<?php echo htmlspecialchars($dept['DepartmentName']); ?>

</option>

<?php

}

?>

</select>


<label>Account Activated</label>

<select name="is_activated">

<option
    value="0"
    <?php echo ($is_activated == 0) ? "selected" : ""; ?>>

No

</option>

<option
    value="1"
    <?php echo ($is_activated == 1) ? "selected" : ""; ?>>

Yes

</option>

</select>


<?php

if($edit)
{

?>

<input
    type="submit"
    name="update"
    value="Update Student"
    class="btn">

<?php

}
else
{

?>

<input
    type="submit"
    name="add"
    value="Add Student"
    class="btn">

<?php

}

?>

</form>

</div>


<br>


<!-- ==================================================
        STUDENT COURSE FORM
=================================================== -->

<?php

if($edit)
{

?>

<div class="card">

<h2>
<?php echo $course_edit ? "Edit Student Course" : "Add Student Course"; ?>
</h2>

<br>

<form method="POST" class="crud-form">


<input
    type="hidden"
    name="course_student_id"
    value="<?php echo htmlspecialchars($student_id); ?>">


<?php

if($course_edit)
{

?>

<input
    type="hidden"
    name="student_course_id"
    value="<?php echo $edit_course_id; ?>">

<?php

}

?>


<label>Course</label>

<select name="course_id" required>

<option value="">
Select Course
</option>

<?php

mysqli_data_seek($course_result, 0);

while($course = $course_result->fetch_assoc())
{

?>

<option
    value="<?php echo $course['CourseID']; ?>"
    <?php

    if(
        $course_edit &&
        $edit_course == $course['CourseID']
    )
    {
        echo "selected";
    }

    ?>>

<?php

echo htmlspecialchars(
    $course['CourseCode'] .
    " - " .
    $course['CourseTitle']
);

?>

</option>

<?php

}

?>

</select>


<label>Semester</label>

<select name="semester_name" required>

<option value="">
Select Semester
</option>

<option
    value="Spring"
    <?php
    echo ($edit_semester == "Spring")
        ? "selected"
        : "";
    ?>>

Spring

</option>

<option
    value="Summer"
    <?php
    echo ($edit_semester == "Summer")
        ? "selected"
        : "";
    ?>>

Summer

</option>

<option
    value="Fall"
    <?php
    echo ($edit_semester == "Fall")
        ? "selected"
        : "";
    ?>>

Fall

</option>

</select>


<label>Year</label>

<input
    type="number"
    name="year"
    min="2000"
    max="2100"
    value="<?php echo htmlspecialchars($edit_year); ?>"
    required>


<label>Grade</label>

<input
    type="text"
    name="grade"
    maxlength="5"
    value="<?php echo htmlspecialchars($edit_grade); ?>">


<?php

if($course_edit)
{

?>

<input
    type="submit"
    name="update_course"
    value="Update Student Course"
    class="btn">

<?php

}
else
{

?>

<input
    type="submit"
    name="add_course"
    value="Add Student Course"
    class="btn">

<?php

}

?>

</form>

</div>

<br>

<?php

}

?>


<!-- ================= STUDENT LIST ================= -->

<div class="card">

<h2>Student List</h2>

<br>
<div class="table-container">
<table>

<tr>

<th>Student ID</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>CGPA</th>
<th>Department</th>
<th>Activated</th>
<th>Manage Courses</th>
<th>Edit</th>
<th>Delete</th>

</tr>


<?php

if($students->num_rows > 0)
{

    while($row = $students->fetch_assoc())
    {

?>

<tr>

<td>
<?php echo htmlspecialchars($row['StudentID']); ?>
</td>

<td>
<?php
echo htmlspecialchars(
    $row['FirstName'] . " " . $row['LastName']
);
?>
</td>

<td>
<?php echo htmlspecialchars($row['Email']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['Phone']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['CGPA']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['DepartmentName']); ?>
</td>

<td>
<?php echo $row['IsActivated'] ? "Yes" : "No"; ?>
</td>

<td>

<a href="manage_students.php?edit=<?php
echo urlencode($row['StudentID']);
?>">

Manage Courses

</a>

</td>

<td>

<a href="manage_students.php?edit=<?php
echo urlencode($row['StudentID']);
?>">

Edit

</a>

</td>

<td>

<a
href="manage_students.php?delete=<?php
echo urlencode($row['StudentID']);
?>"
onclick="return confirm('Delete this student?');">

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

<td colspan="10">
No students found.
</td>

</tr>

<?php

}

?>

</table>
</div>
</div>


<br>


<!-- ================= STUDENT COURSE LIST ================= -->

<div class="card">

<h2>Student Course Records</h2>

<br>

<table>

<tr>

<th>Student ID</th>
<th>Course</th>
<th>Semester</th>
<th>Year</th>
<th>Grade</th>
<th>Edit</th>
<th>Delete</th>

</tr>


<?php

if($student_courses->num_rows > 0)
{

    while($row = $student_courses->fetch_assoc())
    {

?>

<tr>

<td>
<?php echo htmlspecialchars($row['StudentID']); ?>
</td>

<td>

<?php

echo htmlspecialchars(
    $row['CourseCode'] .
    " - " .
    $row['CourseTitle']
);

?>

</td>

<td>
<?php echo htmlspecialchars($row['SemesterName']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['Year']); ?>
</td>

<td>

<?php

echo $row['Grade'] != ""
    ? htmlspecialchars($row['Grade'])
    : "N/A";

?>

</td>

<td>

<a
href="manage_students.php?edit_course=<?php
echo $row['StudentCourseID'];
?>">

Edit

</a>

</td>

<td>

<a
href="manage_students.php?delete_course=<?php
echo $row['StudentCourseID'];
?>"
onclick="return confirm('Delete this course record?');">

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
No student course records found.
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