<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Check if the user is logged in at all
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: ../login.php");
    exit();
}

// 2. Check if a specific role is required for this page and matches the user's role
if (isset($required_role) && $_SESSION['user_type'] !== $required_role) {
    // Redirect unauthorized users back to their respective valid dashboard
    if ($_SESSION['user_type'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else if ($_SESSION['user_type'] === 'faculty') {
        header("Location: ../faculty/dashboard.php");
    } else if ($_SESSION['user_type'] === 'student') {
        header("Location: ../student/dashboard.php");
    } else {
        header("Location: ../login.php");
    }
    exit();
}
?>