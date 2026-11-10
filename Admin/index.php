<?php
session_start();

// Database connection (if needed)
require_once "connection/connection.php";

// Get requested page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Define allowed pages with file paths
$allowed_pages = [
    // Dashboard
    'dashboard' => 'pages/dashboard.php',

    // Admin Pages
    'instructor' => '../Admin/instructor.php',
    'insert_admin' => 'Admin/insert_admin.php',
    'teachers_info' => 'Admin/teachers_info.php',
    'pupils' => 'Admin/pupils.php',
    'display_pupils' => 'Admin/display_pupils.php',
    'attendance' => 'Admin/import_pupils.php',
    'class' => 'Admin/class.php',
    'add_subjects' => 'Admin/add_subjects.php',
    'test' => 'Admin/test.php',
    'departments' => 'Admin/departments.php',
    'teacher_departments' => 'Admin/teacher_departments.php',
    'departmental_info' => 'Admin/departmental_info.php',
    'preview_junior' => 'Admin/preview_junior.php',
    'generate_senior' => 'Admin/generate_senior.php',
    'pupils_list' => 'Admin/pupils_list.php',
    'summary' => 'Admin/summary.php',
    'generate_to_pdf' => 'Admin/generate_to_pdf.php',
    'manage_accounts' => 'Admin/manage_accounts.php',
    'reset_password' => 'Admin/reset_password.php',
    'account_status' => 'Admin/account_status.php',

    // Authentication
    'login' => 'Login/login.php',
    'logout' => 'Login/logout.php',
];

// Security: only allow whitelisted pages
if (!array_key_exists($page, $allowed_pages)) {
    http_response_code(404);
    exit('Page not found');
}

// Authentication: skip login/logout pages
if (!in_array($page, ['login', 'logout'])) {
    if (!isset($_SESSION['LoginAdmin']) && !isset($_SESSION['LoginTeacher']) && !isset($_SESSION['LoginHod'])) {
        header("Location: index.php?page=login");
        exit();
    }
}

// Include the page
$page_file = $allowed_pages[$page];

if (!file_exists($page_file)) {
    http_response_code(404);
    exit("File missing: $page_file");
}

require $page_file;
