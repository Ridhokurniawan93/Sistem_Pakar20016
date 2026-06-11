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
        $kategori = $conn->real_escape_string(trim($_POST['kategori']));
        $nama = $conn->real_escape_string(trim($_POST['nama_atribut']));
        if (empty($kategori) || empty($nama)) {
            echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
            exit();
        }
        $sql = "INSERT INTO atribut (kategori, nama_atribut) VALUES ('$kategori', '$nama')";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Data atribut berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan data: ' . $conn->error]);
        }
        break;
        
    case 'edit':
        $id = intval($_POST['id']);
        $kategori = $conn->real_escape_string(trim($_POST['kategori']));
        $nama = $conn->real_escape_string(trim($_POST['nama_atribut']));
        if (empty($kategori) || empty($nama) || $id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
            exit();
        }
        $sql = "UPDATE atribut SET kategori = '$kategori', nama_atribut = '$nama' WHERE id_atribut = $id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Data atribut berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate data: ' . $conn->error]);
        }
        break;
        
    case 'get':
        $id = intval($_POST['id']);
        $result = $conn->query("SELECT * FROM atribut WHERE id_atribut = $id");
        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $data['id_atribut'],
                    'kategori' => $data['kategori'],
                    'nama_atribut' => $data['nama_atribut'],
                    'action' => 'edit'
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
        break;
        
    case 'delete':
        $id = intval($_POST['id']);
        $sql = "DELETE FROM atribut WHERE id_atribut = $id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Data atribut berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus data: ' . $conn->error]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}
?>
