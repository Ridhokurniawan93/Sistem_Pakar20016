<?php
session_start();
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('auth/login.php');
}

$username = $conn->real_escape_string(trim($_POST['username']));
$password = md5(trim($_POST['password']));

$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = $conn->query($sql);

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    $_SESSION['logged_in'] = true;
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['level'] = $user['level'];
    
    // If siswa, get siswa data
    if ($user['level'] === 'siswa') {
        $sql_siswa = "SELECT * FROM siswa WHERE id_user = " . $user['id_user'];
        $result_siswa = $conn->query($sql_siswa);
        if ($result_siswa && $result_siswa->num_rows === 1) {
            $siswa = $result_siswa->fetch_assoc();
            $_SESSION['nisn'] = $siswa['nisn'];
            $_SESSION['nama'] = $siswa['nama'];
        }
    }
    
    if ($user['level'] === 'admin') {
        redirect('admin/dashboard.php');
    } else {
        redirect('siswa/dashboard.php');
    }
} else {
    $_SESSION['flash_message'] = showAlert('Username atau password salah!', 'danger');
    redirect('auth/login.php');
}
?>
