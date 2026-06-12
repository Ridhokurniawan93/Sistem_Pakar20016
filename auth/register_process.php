<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('auth/register.php');
}

$nisn = $conn->real_escape_string(trim($_POST['nisn']));
$username = $conn->real_escape_string(trim($_POST['username']));
$nama = $conn->real_escape_string(trim($_POST['nama']));
$alamat = $conn->real_escape_string(trim($_POST['alamat']));
$password = trim($_POST['password']);
$konfirmasi = trim($_POST['konfirmasi_password']);

// Validation
if (empty($username)) {
    $_SESSION['flash_message'] = showAlert('Username tidak boleh kosong!', 'danger');
    redirect('auth/register.php');
}

if ($password !== $konfirmasi) {
    $_SESSION['flash_message'] = showAlert('Password dan konfirmasi password tidak sama!', 'danger');
    redirect('auth/register.php');
}

if (strlen($password) < 6) {
    $_SESSION['flash_message'] = showAlert('Password minimal 6 karakter!', 'danger');
    redirect('auth/register.php');
}

// Check NISN already exists
$check = $conn->query("SELECT nisn FROM siswa WHERE nisn = '$nisn'");
if ($check && $check->num_rows > 0) {
    $_SESSION['flash_message'] = showAlert('NISN sudah terdaftar!', 'danger');
    redirect('auth/register.php');
}

// Check username already exists
$check_user = $conn->query("SELECT id_user FROM users WHERE username = '$username'");
if ($check_user && $check_user->num_rows > 0) {
    $_SESSION['flash_message'] = showAlert('Username sudah digunakan! Pilih username lain.', 'danger');
    redirect('auth/register.php');
}

$hashed_password = md5($password);

$conn->begin_transaction();

try {
    // Insert into users (with custom username)
    $sql_user = "INSERT INTO users (username, password, level) VALUES ('$username', '$hashed_password', 'siswa')";
    $conn->query($sql_user);
    $id_user = $conn->insert_id;
    
    // Insert into siswa
    $sql_siswa = "INSERT INTO siswa (nisn, nama, alamat, password, id_user) VALUES ('$nisn', '$nama', '$alamat', '$hashed_password', $id_user)";
    $conn->query($sql_siswa);
    
    $conn->commit();
    
    $_SESSION['flash_message'] = showAlert('Registrasi berhasil! Silakan login dengan username: ' . htmlspecialchars($username), 'success');
    redirect('auth/login.php');
    
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['flash_message'] = showAlert('Terjadi kesalahan: ' . $e->getMessage(), 'danger');
    redirect('auth/register.php');
}
?>
