<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (isLoggedIn()) {
    if (checkRole('admin')) {
        redirect('admin/dashboard.php');
    } else {
        redirect('siswa/dashboard.php');
    }
} else {
    redirect('auth/login.php');
}
?>
