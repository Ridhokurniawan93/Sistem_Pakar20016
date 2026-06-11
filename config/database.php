<?php
// ============================================
// Database Configuration
// ============================================
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_sistem_pakar_jurusan';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// ============================================
// Path Configuration (Auto-detect)
// ============================================
define('ROOT_PATH', dirname(__DIR__));

// Auto-detect base URL
// PHP built-in server: document root = project folder → base_url = '/'
// Apache/XAMPP: project is subfolder of htdocs → base_url = '/folder_name/'
$doc_root = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
$proj_root = str_replace('\\', '/', ROOT_PATH);

if ($doc_root === $proj_root) {
    // PHP built-in server or project IS the document root
    $base_url = '/';
} else {
    // Project is a subfolder of document root
    $base_url = '/' . basename(ROOT_PATH) . '/';
}

// ============================================
// Helper Functions
// ============================================
function base_url($path = '') {
    global $base_url;
    return $base_url . ltrim($path, '/');
}

function redirect($path) {
    $url = base_url($path);
    // Fix spaces in URL for proper redirect
    $url = str_replace(' ', '%20', $url);
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function checkRole($role) {
    return isset($_SESSION['level']) && $_SESSION['level'] === $role;
}

function showAlert($message, $type = 'danger') {
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
        ' . htmlspecialchars($message) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}
?>
