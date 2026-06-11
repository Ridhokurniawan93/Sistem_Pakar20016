<?php
if (!isLoggedIn()) {
    redirect('auth/login.php');
}

// Role-based access control
$current_page = basename($_SERVER['PHP_SELF']);
$script_path = $_SERVER['PHP_SELF'];

if (strpos($script_path, '/admin/') !== false && !checkRole('admin')) {
    redirect('siswa/dashboard.php');
}

if (strpos($script_path, '/siswa/') !== false && !checkRole('siswa')) {
    redirect('admin/dashboard.php');
}
?>
