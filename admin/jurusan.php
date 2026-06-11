<?php
$page_title = 'Data Jurusan';
require_once __DIR__ . '/../includes/session_start.php';
require_once __DIR__ . '/../includes/auth_check.php';

$jurusan = $conn->query("SELECT * FROM jurusan ORDER BY id_jurusan");

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="section-title mb-0">Data Jurusan</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalJurusan">
        <i class="fas fa-plus me-2"></i>Tambah Jurusan
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Nama Jurusan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jurusan && $jurusan->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $jurusan->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['id_jurusan'] ?></td>
                            <td><?= htmlspecialchars($row['nama_jurusan']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit" 
                                        data-bs-toggle="modal" data-bs-target="#modalJurusan"
                                        data-url="<?= base_url('admin/ajax/jurusan_ajax.php') ?>"
                                        data-id="<?= $row['id_jurusan'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete" 
                                        data-url="<?= base_url('admin/ajax/jurusan_ajax.php') ?>"
                                        data-id="<?= $row['id_jurusan'] ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Jurusan -->
<div class="modal fade" id="modalJurusan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-school me-2"></i>Form Jurusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="ajax-form" action="<?= base_url('admin/ajax/jurusan_ajax.php') ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Nama Jurusan</label>
                        <input type="text" class="form-control" name="nama_jurusan" required 
                               placeholder="Masukkan nama jurusan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
