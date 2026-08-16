<?php
/*
EWU TA Management System
Manage Departments
(Admin CRUD)
*/
include "../includes/db_conn.php";
$required_role = 'admin';
include "../includes/session.php";
$message = "";

/*
            ADD DEPARTMENT
*/
if(isset($_POST['add']))
{
    $department_name = trim($_POST['department_name']);
    $floor_no = trim($_POST['floor_no']);

    $check = $conn->prepare(
        "SELECT DepartmentID
         FROM Department
         WHERE DepartmentName=?"
    );

    $check->bind_param(
        "s",
        $department_name
    );

    $check->execute();

    $result = $check->get_result();

    if($result->num_rows > 0)
    {
        $message = "Department already exists.";
    }
    else
    {
        $stmt = $conn->prepare(
            "INSERT INTO Department
            (DepartmentName, FloorNo)
            VALUES (?, ?)"
        );

        $stmt->bind_param(
            "ss",
            $department_name,
            $floor_no
        );

        if($stmt->execute())
        {
            $message = "Department added successfully.";
        }
        else
        {
            $message = "Unable to add department.";
        }
    }
}
/*
            DELETE DEPARTMENT
*/

if(isset($_GET['delete']))
{
    $id = intval($_GET['delete']);

    try
    {
        $stmt = $conn->prepare(
            "DELETE FROM Department
             WHERE DepartmentID=?"
        );

        $stmt->bind_param(
            "i",
            $id
        );

        $stmt->execute();

        $message = "Department deleted successfully.";
    }
    catch(mysqli_sql_exception $e)
    {
        $message = "Unable to delete department due to a database error.";
    }
}
/*
            LOAD DATA FOR UPDATE
*/
$edit = false;
$id = "";
$department = "";
$floor_no = "";
if(isset($_GET['edit']))
{
    $id = intval($_GET['edit']);
   $stmt = $conn->prepare(
        "SELECT *
         FROM Department
         WHERE DepartmentID=?"
    );
    $stmt->bind_param(
        "i",
        $id
    );
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows == 1)
    {
        $row = $result->fetch_assoc();
        $department = $row['DepartmentName'];
        $floor_no = $row['FloorNo'];
        $edit = true;
    }
}
/*
            UPDATE DEPARTMENT
*/
if(isset($_POST['update']))
{
    $id = intval($_POST['department_id']);

    $department_name = trim($_POST['department_name']);

    $floor_no = trim($_POST['floor_no']);
  $stmt = $conn->prepare(
        "UPDATE Department

         SET
         DepartmentName=?,
         FloorNo=?

         WHERE DepartmentID=?"
    );
    $stmt->bind_param(
        "ssi",
        $department_name,
        $floor_no,
        $id
    );
    if($stmt->execute())
    {
        $message = "Department updated successfully.";
    }
    else
    {
        $message = "Unable to update department.";
    }
}
/*
            LOAD DEPARTMENTS
*/
$departments = $conn->query(
    "SELECT *
     FROM Department
     ORDER BY DepartmentName"
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1.0">
<title>Manage Departments</title>
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
<!-- ================= MAIN CONTENT ================= -->
<div class="container">
<!-- ================= ADD / UPDATE ================= -->
<div class="card">
<h2>
Manage Departments
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
<form method="POST"
      class="crud-form">
<input
    type="hidden"
    name="department_id"
    value="<?php echo htmlspecialchars($id); ?>">
<label>
Department Name
</label>
<input
    type="text"
    name="department_name"
    value="<?php echo htmlspecialchars($department); ?>"
    required>
<label>
Floor No
</label>

<input
    type="text"
    name="floor_no"
    value="<?php echo htmlspecialchars($floor_no); ?>">
<?php
if($edit)
{
?>
<input
    type="submit"
    name="update"
    value="Update Department"
    class="btn">
<?php
}
else
{
?>
<input
    type="submit"
    name="add"
    value="Add Department"
    class="btn">
<?php
}
?>
</form>
</div>
<br>
<!-- ================= DEPARTMENT LIST ================= -->
<div class="card">
<h2>
Department List
</h2>
<br>
<table>
<tr>
<th>ID</th>
<th>Department</th>
<th>Floor No</th>
<th>Edit</th>
<th>Delete</th>
</tr>
<?php
while($row = $departments->fetch_assoc())
{
?>
<tr>
<td>
<?php
echo htmlspecialchars(
    $row['DepartmentID']
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
echo htmlspecialchars(
    $row['FloorNo']
);

?>
</td>
<td>
<a
href="manage_departments.php?edit=<?php
echo urlencode($row['DepartmentID']);
?>">
Edit
</a>
</td>
<td>
<a
href="manage_departments.php?delete=<?php
echo urlencode($row['DepartmentID']);
?>"
onclick="return confirm('Are you sure you want to delete this department?');">
Delete
</a>
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