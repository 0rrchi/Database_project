<?php
/*
------------------------------------------------------
EWU TA Management System

Logout
------------------------------------------------------
*/

session_start();

session_destroy();

header("Location: login.php");

exit();

?>