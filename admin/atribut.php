<?php
$page_title = 'Data Atribut';
require_once __DIR__ . '/../includes/session_start.php';
require_once __DIR__ . '/../includes/auth_check.php';

$atribut = $conn->query("SELECT * FROM atribut ORDER BY kategori, id_atribut");

$kategoris = ['Minat','Cita-cita','Kesukaan','Keingintahuan','Kebutuhan','Motivasi',
              'Bakat','Pengetahuan','Mudah Memahami','Mampu Mengenal Masalah','Kreatif','Inovatif'];

$kategori_colors = [
    'Minat' => 'primary', 'Cita-cita' => 'success', 'Kesukaan' => 'info',
    'Keingintahuan' => 'warning', 'Kebutuhan' => 'danger', 'Motivasi' => 'secondary',
    'Bakat' => 'dark', 'Pengetahuan' => 'primary', 'Mudah Memahami' => 'success',
    'Mampu Mengenal Masalah' => 'info', 'Kreatif' => 'warning', 'Inovatif' => 'danger'
];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="section-title mb-0">Data Atribut</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAtribut">
        <i class="fas fa-plus me-2"></i>Tambah Atribut
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
                        <th>Kategori</th>
                        <th>Nama Atribut</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($atribut && $atribut->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $atribut->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['id_atribut'] ?></td>
                            <td>
                                <span class="badge bg-<?= $kategori_colors[$row['kategori']] ?? 'secondary' ?>">
                                    <?= htmlspecialchars($row['kategori']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['nama_atribut']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit" 
                                        data-bs-toggle="modal" data-bs-target="#modalAtribut"
                                        data-url="<?= base_url('admin/ajax/atribut_ajax.php') ?>"
                                        data-id="<?= $row['id_atribut'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete" 
                                        data-url="<?= base_url('admin/ajax/atribut_ajax.php') ?>"
                                        data-id="<?= $row['id_atribut'] ?>">
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

<!-- Modal Atribut -->
<div class="modal fade" id="modalAtribut" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-list-check me-2"></i>Form Atribut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="ajax-form" action="<?= base_url('admin/ajax/atribut_ajax.php') ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategoris as $kat): ?>
                            <option value="<?= $kat ?>"><?= $kat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Atribut</label>
                        <input type="text" class="form-control" name="nama_atribut" required 
                               placeholder="Masukkan nama atribut">
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
