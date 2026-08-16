<?php
// Database connection

$host = "localhost"; //This tells PHP where the MySQL server is located.
$username = "root";
$password = "";
$database = "ewu_ta_management"; //This tells PHP which database to use.

$conn = new mysqli($host, $username, $password, $database); //Create the database connection
if ($conn->connect_error) //Check for connection errors
{
    die("Database Connection Failed: " . $conn->connect_error);
}
?>