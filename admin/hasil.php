<?php
$page_title = 'Hasil Penentuan Jurusan';
require_once __DIR__ . '/../includes/session_start.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Get all consultation results
$hasil = $conn->query("SELECT k.id_konsultasi, s.nama, s.nisn, k.tanggal, 
    j.nama_jurusan, hp.persentase,
    (SELECT COUNT(*) FROM hasil_penentuan WHERE id_konsultasi = k.id_konsultasi AND persentase > 0) as total_match
    FROM konsultasi k
    JOIN siswa s ON k.nisn = s.nisn
    JOIN hasil_penentuan hp ON k.id_konsultasi = hp.id_konsultasi
    JOIN jurusan j ON hp.id_jurusan = j.id_jurusan
    WHERE hp.persentase = (SELECT MAX(persentase) FROM hasil_penentuan WHERE id_konsultasi = k.id_konsultasi)
    ORDER BY k.created_at DESC");

// Detail view
$show_detail = false;
if (isset($_GET['id'])) {
    $id_konsultasi = intval($_GET['id']);
    $konsultasi_detail = $conn->query("SELECT k.*, s.nama, s.nisn, s.alamat 
        FROM konsultasi k JOIN siswa s ON k.nisn = s.nisn 
        WHERE k.id_konsultasi = $id_konsultasi");
    
    if ($konsultasi_detail && $konsultasi_detail->num_rows > 0) {
        $k_detail = $konsultasi_detail->fetch_assoc();
        $hasil_detail = $conn->query("SELECT hp.*, j.nama_jurusan 
            FROM hasil_penentuan hp JOIN jurusan j ON hp.id_jurusan = j.id_jurusan 
            WHERE hp.id_konsultasi = $id_konsultasi ORDER BY hp.persentase DESC");
        $detail_jawaban = $conn->query("SELECT dk.*, a.nama_atribut, a.kategori 
            FROM detail_konsultasi dk JOIN atribut a ON dk.id_atribut = a.id_atribut 
            WHERE dk.id_konsultasi = $id_konsultasi ORDER BY a.kategori");
        $show_detail = true;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<?php if ($show_detail): ?>
<!-- Detail View -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="<?= base_url('admin/hasil.php') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Student Info -->
<div class="card mb-4">
    <div class="card-body" style="background: linear-gradient(135deg, #0d6efd, #0a58ca); border-radius: 15px;">
        <div class="row text-white">
            <div class="col-md-3">
                <h6 class="opacity-75 mb-1">Nama Siswa</h6>
                <h5 class="mb-0"><?= htmlspecialchars($k_detail['nama']) ?></h5>
            </div>
            <div class="col-md-3">
                <h6 class="opacity-75 mb-1">NISN</h6>
                <h5 class="mb-0"><?= htmlspecialchars($k_detail['nisn']) ?></h5>
            </div>
            <div class="col-md-3">
                <h6 class="opacity-75 mb-1">Alamat</h6>
                <h5 class="mb-0"><?= htmlspecialchars($k_detail['alamat']) ?></h5>
            </div>
            <div class="col-md-3">
                <h6 class="opacity-75 mb-1">Tanggal</h6>
                <h5 class="mb-0"><?= date('d F Y', strtotime($k_detail['tanggal'])) ?></h5>
            </div>
        </div>
    </div>
</div>

<!-- Hasil Analisis -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Hasil Analisis Forward Chaining</h5>
    </div>
    <div class="card-body">
        <?php 
        $hasil_arr = [];
        if ($hasil_detail) {
            while ($row = $hasil_detail->fetch_assoc()) {
                $hasil_arr[] = $row;
            }
        }
        foreach ($hasil_arr as $index => $h): 
        ?>
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span>
                    <strong><?= ($index + 1) ?>.</strong> 
                    <?= htmlspecialchars($h['nama_jurusan']) ?>
                    <?php if ($index === 0): ?>
                    <span class="badge bg-success ms-2">Rekomendasi Terbaik</span>
                    <?php endif; ?>
                </span>
                <span class="badge bg-primary fs-6"><?= number_format($h['persentase'], 1) ?>%</span>
            </div>
            <div class="progress" style="height: 25px;">
                <div class="progress-bar <?= $index === 0 ? 'bg-success' : 'bg-primary' ?>" 
                     style="width: <?= $h['persentase'] ?>%">
                    <?= number_format($h['persentase'], 1) ?>%
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Jawaban -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Jawaban Siswa</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Jawaban</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($detail_jawaban && $detail_jawaban->num_rows > 0): ?>
                    <?php $no = 1; while ($d = $detail_jawaban->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><span class="badge bg-info"><?= htmlspecialchars($d['kategori']) ?></span></td>
                        <td><?= htmlspecialchars($d['jawaban']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<!-- List View -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Data Hasil Penentuan Jurusan</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Tanggal</th>
                        <th>Jurusan Rekomendasi</th>
                        <th>Persentase</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($hasil && $hasil->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $hasil->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['nisn']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><span class="badge bg-primary"><?= htmlspecialchars($row['nama_jurusan']) ?></span></td>
                            <td><span class="badge bg-success"><?= number_format($row['persentase'], 1) ?>%</span></td>
                            <td>
                                <a href="<?= base_url('admin/hasil.php?id=' . $row['id_konsultasi']) ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada data hasil penentuan
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

include __DIR__ . '/../includes/footer.php';