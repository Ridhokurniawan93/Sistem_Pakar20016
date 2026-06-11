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
    case 'edit':
        $id_jurusan = intval($_POST['id_jurusan']);
        $atribut_ids = $_POST['atribut'] ?? [];
        
        if ($id_jurusan <= 0) {
            echo json_encode(['success' => false, 'message' => 'Pilih jurusan terlebih dahulu']);
            exit();
        }
        
        if (empty($atribut_ids)) {
            echo json_encode(['success' => false, 'message' => 'Pilih minimal satu atribut']);
            exit();
        }
        
        $conn->begin_transaction();
        try {
            // If editing, delete existing rules first
            if ($action === 'edit') {
                $conn->query("DELETE FROM rule_jurusan WHERE id_jurusan = $id_jurusan");
            }
            
            // Insert new rules
            foreach ($atribut_ids as $attr_id) {
                $attr_id = intval($attr_id);
                $conn->query("INSERT INTO rule_jurusan (id_jurusan, id_atribut) VALUES ($id_jurusan, $attr_id)");
            }
            
            $conn->commit();
            $msg = $action === 'add' ? 'ditambahkan' : 'diupdate';
            echo json_encode(['success' => true, 'message' => "Rule berhasil $msg"]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
        break;
        
    case 'get_rule':
        $id_jurusan = intval($_POST['id']);
        $result = $conn->query("SELECT id_atribut FROM rule_jurusan WHERE id_jurusan = $id_jurusan");
        $atribut_ids = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $atribut_ids[] = intval($row['id_atribut']);
            }
        }
        echo json_encode([
            'success' => true,
            'atribut_ids' => $atribut_ids
        ]);
        break;
        
    case 'delete':
        $id_jurusan = intval($_POST['id']);
        $sql = "DELETE FROM rule_jurusan WHERE id_jurusan = $id_jurusan";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Rule berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus: ' . $conn->error]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
}
?>
