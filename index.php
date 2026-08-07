<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: ./admin/posts.php');
    exit();
} else if (isset($_SESSION['user_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ./admin/dashboard.php');
    exit();
} else {
    // Not logged in, redirect to login page
    header('Location: ./login.php');
    exit();
}
