<?php
$page_title = 'Hasil Konsultasi';
require_once __DIR__ . '/../includes/session_start.php';
require_once __DIR__ . '/../includes/auth_check.php';

$nisn = $_SESSION['nisn'];

// If specific konsultasi ID is provided
if (isset($_GET['id'])) {
    $id_konsultasi = intval($_GET['id']);
    
    // Verify this konsultasi belongs to this student
    $check = $conn->query("SELECT * FROM konsultasi WHERE id_konsultasi = $id_konsultasi AND nisn = '$nisn'");
    if (!$check || $check->num_rows === 0) {
        redirect('siswa/hasil.php');
    }
    
    $konsultasi = $check->fetch_assoc();
    
    // Get student info
    $siswa_info = $conn->query("SELECT * FROM siswa WHERE nisn = '$nisn'")->fetch_assoc();
    
    // Get results for this konsultasi
    $hasil = $conn->query("SELECT hp.*, j.nama_jurusan 
        FROM hasil_penentuan hp 
        JOIN jurusan j ON hp.id_jurusan = j.id_jurusan 
        WHERE hp.id_konsultasi = $id_konsultasi 
        ORDER BY hp.persentase DESC");
    
    // Get detail jawaban
    $detail = $conn->query("SELECT dk.*, a.nama_atribut, a.kategori 
        FROM detail_konsultasi dk 
        JOIN atribut a ON dk.id_atribut = a.id_atribut 
        WHERE dk.id_konsultasi = $id_konsultasi 
        ORDER BY a.kategori");
        
    $show_detail = true;
} else {
    // Show all consultations
    $histori = $conn->query("SELECT k.id_konsultasi, k.tanggal, j.nama_jurusan, hp.persentase,
        (SELECT COUNT(*) FROM hasil_penentuan WHERE id_konsultasi = k.id_konsultasi AND persentase > 0) as total_match
        FROM konsultasi k
        LEFT JOIN hasil_penentuan hp ON k.id_konsultasi = hp.id_konsultasi
        LEFT JOIN jurusan j ON hp.id_jurusan = j.id_jurusan
        WHERE k.nisn = '$nisn'
        AND (hp.persentase = (SELECT MAX(persentase) FROM hasil_penentuan WHERE id_konsultasi = k.id_konsultasi) OR hp.id_hasil IS NULL)
        ORDER BY k.created_at DESC");
    
    $show_detail = false;
}

require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($show_detail): ?>
<!-- Detail View -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="<?= base_url('siswa/hasil.php') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
    <a href="<?= base_url('siswa/cetak.php?id=' . $id_konsultasi) ?>" class="btn btn-danger" target="_blank">
        <i class="fas fa-file-pdf me-2"></i>Cetak PDF
    </a>
</div>

<!-- Student Info -->
<div class="card mb-4">
    <div class="card-body" style="background: linear-gradient(135deg, #0d6efd, #0a58ca); border-radius: 15px;">
        <div class="row text-white">
            <div class="col-md-4">
                <h6 class="opacity-75 mb-1">Nama Siswa</h6>
                <h5 class="mb-0"><?= htmlspecialchars($siswa_info['nama']) ?></h5>
            </div>
            <div class="col-md-4">
                <h6 class="opacity-75 mb-1">NISN</h6>
                <h5 class="mb-0"><?= htmlspecialchars($siswa_info['nisn']) ?></h5>
            </div>
            <div class="col-md-4">
                <h6 class="opacity-75 mb-1">Tanggal Konsultasi</h6>
                <h5 class="mb-0"><?= date('d F Y', strtotime($konsultasi['tanggal'])) ?></h5>
            </div>
        </div>
    </div>
</div>

<!-- Best Match -->
<?php 
$hasil_array = [];
if ($hasil) {
    while ($row = $hasil->fetch_assoc()) {
        $hasil_array[] = $row;
    }
}
$best = !empty($hasil_array) ? $hasil_array[0] : null;
?>

<?php if ($best): ?>
<div class="card result-card mb-4">
    <div class="result-header">
        <h4 class="mb-2"><i class="fas fa-trophy me-2"></i>Jurusan Rekomendasi</h4>
        <h2 class="mb-1"><?= htmlspecialchars($best['nama_jurusan']) ?></h2>
        <div class="match-percentage text-white"><?= number_format($best['persentase'], 1) ?>%</div>
        <small class="opacity-75">Kecocokan</small>
    </div>
</div>
<?php endif; ?>

<!-- All Results -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Semua Hasil Analisis</h5>
    </div>
    <div class="card-body">
        <?php foreach ($hasil_array as $index => $h): ?>
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span>
                    <strong><?= ($index + 1) ?>.</strong> 
                    <?= htmlspecialchars($h['nama_jurusan']) ?>
                    <?php if ($index === 0): ?>
                    <span class="badge bg-success ms-2">Terbaik</span>
                    <?php endif; ?>
                </span>
                <span class="badge bg-primary"><?= number_format($h['persentase'], 1) ?>%</span>
            </div>
            <div class="progress">
                <div class="progress-bar <?= $index === 0 ? 'bg-success' : 'bg-primary' ?>" 
                     role="progressbar" 
                     style="width: <?= $h['persentase'] ?>%"
                     aria-valuenow="<?= $h['persentase'] ?>" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <?= number_format($h['persentase'], 1) ?>%
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Jawaban Siswa -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Jawaban Anda</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Jawaban</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($detail && $detail->num_rows > 0): ?>
                        <?php $no = 1; while ($d = $detail->fetch_assoc()): ?>
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
</div>

<?php else: ?>
<!-- List View -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="section-title mb-0">Riwayat Hasil Konsultasi</h4>
    <a href="<?= base_url('siswa/konsultasi.php') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Konsultasi Baru
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jurusan Rekomendasi</th>
                        <th>Persentase</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($histori && $histori->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $histori->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td>
                                <span class="badge bg-primary"><?= htmlspecialchars($row['nama_jurusan'] ?? '-') ?></span>
                            </td>
                            <td>
                                <span class="badge bg-success"><?= number_format($row['persentase'] ?? 0, 1) ?>%</span>
                            </td>
                            <td>
                                <a href="<?= base_url('siswa/hasil.php?id=' . $row['id_konsultasi']) ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <a href="<?= base_url('siswa/cetak.php?id=' . $row['id_konsultasi']) ?>" 
                                   class="btn btn-sm btn-danger" target="_blank">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada hasil konsultasi. 
                                <a href="<?= base_url('siswa/konsultasi.php') ?>">Mulai konsultasi</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
