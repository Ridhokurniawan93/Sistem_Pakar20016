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
        $nama = $conn->real_escape_string(trim($_POST['nama_jurusan']));
        if (empty($nama)) {
            echo json_encode(['success' => false, 'message' => 'Nama jurusan tidak boleh kosong']);
            exit();
        }
        $sql = "INSERT INTO jurusan (nama_jurusan) VALUES ('$nama')";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Data jurusan berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan data: ' . $conn->error]);
        }
        break;
        
    case 'edit':
        $id = intval($_POST['id']);
        $nama = $conn->real_escape_string(trim($_POST['nama_jurusan']));
        if (empty($nama) || $id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
            exit();
        }
        $sql = "UPDATE jurusan SET nama_jurusan = '$nama' WHERE id_jurusan = $id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Data jurusan berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate data: ' . $conn->error]);
        }
        break;
        
    case 'get':
        $id = intval($_POST['id']);
        $result = $conn->query("SELECT * FROM jurusan WHERE id_jurusan = $id");
        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $data['id_jurusan'],
                    'nama_jurusan' => $data['nama_jurusan'],
                    'action' => 'edit'
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        break;
        
    case 'delete':
        $id = intval($_POST['id']);
        $sql = "DELETE FROM jurusan WHERE id_jurusan = $id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Data jurusan berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus data: ' . $conn->error]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}
?>
