<?php
session_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['level'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $nisn = $conn->real_escape_string(trim($_POST['nisn']));
        $username = $conn->real_escape_string(trim($_POST['username']));
        $nama = $conn->real_escape_string(trim($_POST['nama']));
        $alamat = $conn->real_escape_string(trim($_POST['alamat']));
        $password = md5(trim($_POST['password']));
        
        if (empty($nisn) || empty($username) || empty($nama) || empty($alamat)) {
            echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
            exit();
        }
        
        // Check if NISN exists
        $check = $conn->query("SELECT nisn FROM siswa WHERE nisn = '$nisn'");
        if ($check && $check->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'NISN sudah terdaftar']);
            exit();
        }
        
        // Check if username exists
        $check_user = $conn->query("SELECT id_user FROM users WHERE username = '$username'");
        if ($check_user && $check_user->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Username sudah digunakan']);
            exit();
        }
        
        $conn->begin_transaction();
        try {
            $conn->query("INSERT INTO users (username, password, level) VALUES ('$username', '$password', 'siswa')");
            $id_user = $conn->insert_id;
            $conn->query("INSERT INTO siswa (nisn, nama, alamat, password, id_user) VALUES ('$nisn', '$nama', '$alamat', '$password', $id_user)");
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Data siswa berhasil ditambahkan']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan: ' . $e->getMessage()]);
        }
        break;
        
    case 'edit':
        $nisn_old = $conn->real_escape_string(trim($_POST['nisn_old']));
        $nisn = $conn->real_escape_string(trim($_POST['nisn']));
        $username = $conn->real_escape_string(trim($_POST['username']));
        $nama = $conn->real_escape_string(trim($_POST['nama']));
        $alamat = $conn->real_escape_string(trim($_POST['alamat']));
        
        if (empty($nisn) || empty($username) || empty($nama) || empty($alamat)) {
            echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
            exit();
        }
        
        $conn->begin_transaction();
        try {
            // Update password if provided
            if (!empty(trim($_POST['password']))) {
                $password = md5(trim($_POST['password']));
                $conn->query("UPDATE siswa SET nisn = '$nisn', nama = '$nama', alamat = '$alamat', password = '$password' WHERE nisn = '$nisn_old'");
                $conn->query("UPDATE users u JOIN siswa s ON u.id_user = s.id_user SET u.username = '$username', u.password = '$password' WHERE s.nisn = '$nisn_old'");
            } else {
                $conn->query("UPDATE siswa SET nisn = '$nisn', nama = '$nama', alamat = '$alamat' WHERE nisn = '$nisn_old'");
                $conn->query("UPDATE users u JOIN siswa s ON u.id_user = s.id_user SET u.username = '$username' WHERE s.nisn = '$nisn_old'");
            }
            
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Data siswa berhasil diupdate']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate: ' . $e->getMessage()]);
        }
        break;
        
    case 'get':
        $nisn = $conn->real_escape_string(trim($_POST['id']));
        $result = $conn->query("SELECT s.*, u.username FROM siswa s JOIN users u ON s.id_user = u.id_user WHERE s.nisn = '$nisn'");
        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'data' => [
                    'nisn_old' => $data['nisn'],
                    'nisn' => $data['nisn'],
                    'username' => $data['username'],
                    'nama' => $data['nama'],
                    'alamat' => $data['alamat'],
                    'action' => 'edit'
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        break;
        
    case 'delete':
        $nisn = $conn->real_escape_string(trim($_POST['id']));
        $conn->begin_transaction();
        try {
            $result = $conn->query("SELECT id_user FROM siswa WHERE nisn = '$nisn'");
            if ($result && $result->num_rows > 0) {
                $id_user = $result->fetch_assoc()['id_user'];
                $conn->query("DELETE FROM siswa WHERE nisn = '$nisn'");
                $conn->query("DELETE FROM users WHERE id_user = $id_user");
            }
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Data siswa berhasil dihapus']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}
?>
