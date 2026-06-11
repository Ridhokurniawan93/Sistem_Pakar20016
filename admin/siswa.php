<?php
$page_title = 'Data Siswa';
require_once __DIR__ . '/../includes/session_start.php';
require_once __DIR__ . '/../includes/auth_check.php';

$siswa = $conn->query("SELECT s.*, u.username FROM siswa s JOIN users u ON s.id_user = u.id_user ORDER BY s.nisn");

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="section-title mb-0">Data Siswa</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSiswa">
        <i class="fas fa-plus me-2"></i>Tambah Siswa
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($siswa && $siswa->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $siswa->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nisn']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['alamat']) ?></td>
                            <td><span class="badge bg-info"><?= htmlspecialchars($row['username']) ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit" 
                                        data-bs-toggle="modal" data-bs-target="#modalSiswa"
                                        data-url="<?= base_url('admin/ajax/siswa_ajax.php') ?>"
                                        data-id="<?= $row['nisn'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete" 
                                        data-url="<?= base_url('admin/ajax/siswa_ajax.php') ?>"
                                        data-id="<?= $row['nisn'] ?>">
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

<!-- Modal Siswa -->
<div class="modal fade" id="modalSiswa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-users me-2"></i>Form Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="ajax-form" action="<?= base_url('admin/ajax/siswa_ajax.php') ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="nisn_old" value="">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">NISN</label>
                        <input type="text" class="form-control" name="nisn" required 
                               placeholder="Masukkan NISN">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" required 
                               placeholder="Masukkan nama lengkap">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3" required 
                                  placeholder="Masukkan alamat"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                        <input type="password" class="form-control" name="password" 
                               placeholder="Masukkan password baru">
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
