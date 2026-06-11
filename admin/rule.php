<?php
$page_title = 'Data Rule';
require_once __DIR__ . '/../includes/session_start.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Get rules grouped by jurusan
$rules = $conn->query("SELECT j.id_jurusan, j.nama_jurusan, 
    GROUP_CONCAT(a.nama_atribut ORDER BY a.id_atribut SEPARATOR ', ') as daftar_atribut,
    COUNT(r.id_rule) as total_atribut
    FROM jurusan j 
    LEFT JOIN rule_jurusan r ON j.id_jurusan = r.id_jurusan 
    LEFT JOIN atribut a ON r.id_atribut = a.id_atribut 
    GROUP BY j.id_jurusan, j.nama_jurusan
    ORDER BY j.id_jurusan");

$jurusan_list = $conn->query("SELECT * FROM jurusan ORDER BY id_jurusan");
$atribut_list = $conn->query("SELECT * FROM atribut ORDER BY kategori, nama_atribut");

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="section-title mb-0">Data Rule</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRule">
        <i class="fas fa-plus me-2"></i>Tambah Rule
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jurusan</th>
                        <th>Jumlah Atribut</th>
                        <th>Daftar Atribut</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($rules && $rules->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $rules->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge bg-primary fs-6"><?= htmlspecialchars($row['nama_jurusan']) ?></span></td>
                            <td><span class="badge bg-success"><?= $row['total_atribut'] ?> Atribut</span></td>
                            <td><small class="text-muted"><?= htmlspecialchars($row['daftar_atribut'] ?? '-') ?></small></td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit-rule" 
                                        data-bs-toggle="modal" data-bs-target="#modalRule"
                                        data-url="<?= base_url('admin/ajax/rule_ajax.php') ?>"
                                        data-id="<?= $row['id_jurusan'] ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete" 
                                        data-url="<?= base_url('admin/ajax/rule_ajax.php') ?>"
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

<!-- Modal Rule -->
<div class="modal fade" id="modalRule" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-cogs me-2"></i>Form Rule Jurusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="ajax-form" action="<?= base_url('admin/ajax/rule_ajax.php') ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Pilih Jurusan</label>
                        <select class="form-select" name="id_jurusan" id="selectJurusan" required>
                            <option value="">-- Pilih Jurusan --</option>
                            <?php while ($j = $jurusan_list->fetch_assoc()): ?>
                            <option value="<?= $j['id_jurusan'] ?>"><?= htmlspecialchars($j['nama_jurusan']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Atribut (Centang yang sesuai)</label>
                        <div class="row" style="max-height: 400px; overflow-y: auto;">
                            <?php 
                            $current_kategori = '';
                            $atribut_list->data_seek(0);
                            while ($a = $atribut_list->fetch_assoc()): 
                                if ($current_kategori !== $a['kategori']):
                                    $current_kategori = $a['kategori'];
                            ?>
                            <div class="col-12 mt-2">
                                <strong class="text-primary"><i class="fas fa-tag me-1"></i><?= htmlspecialchars($current_kategori) ?></strong>
                                <hr class="my-1">
                            </div>
                            <?php endif; ?>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="atribut[]" 
                                           value="<?= $a['id_atribut'] ?>" id="attr_<?= $a['id_atribut'] ?>">
                                    <label class="form-check-label" for="attr_<?= $a['id_atribut'] ?>">
                                        <?= htmlspecialchars($a['nama_atribut']) ?>
                                    </label>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
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

<script>
// Load rule data for editing
$(document).on('click', '.btn-edit-rule', function() {
    var url = $(this).data('url');
    var id = $(this).data('id');
    
    // Reset checkboxes
    $('input[name="atribut[]"]').prop('checked', false);
    
    $.ajax({
        url: url,
        type: 'POST',
        data: { id: id, action: 'get_rule' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#selectJurusan').val(id);
                $('input[name="id"]').val(id);
                $('input[name="action"]').val('edit');
                
                // Check the matching atribut
                $.each(response.atribut_ids, function(i, attr_id) {
                    $('#attr_' + attr_id).prop('checked', true);
                });
            }
        }
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
