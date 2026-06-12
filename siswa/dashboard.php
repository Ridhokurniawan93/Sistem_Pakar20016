<?php
$page_title = 'Dashboard Siswa';
require_once __DIR__ . '/../includes/session_start.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Safety check - if siswa session is missing, reload from database
if (!isset($_SESSION['nisn']) && isset($_SESSION['id_user'])) {
    $uid = intval($_SESSION['id_user']);
    $res = $conn->query("SELECT * FROM siswa WHERE id_user = $uid");
    if ($res && $res->num_rows > 0) {
        $s = $res->fetch_assoc();
        $_SESSION['nisn'] = $s['nisn'];
        $_SESSION['nama'] = $s['nama'];
    }
}

$nisn = $_SESSION['nisn'] ?? '';
$nama = $_SESSION['nama'] ?? '';

// Get student's consultation history
$histori = false;
$total_konsultasi = 0;

if (!empty($nisn)) {
    $histori = $conn->query("SELECT k.id_konsultasi, k.tanggal, j.nama_jurusan, hp.persentase
        FROM konsultasi k
        JOIN hasil_penentuan hp ON k.id_konsultasi = hp.id_konsultasi
        JOIN jurusan j ON hp.id_jurusan = j.id_jurusan
        WHERE k.nisn = '$nisn'
        AND hp.persentase = (SELECT MAX(persentase) FROM hasil_penentuan WHERE id_konsultasi = k.id_konsultasi)
        ORDER BY k.created_at DESC LIMIT 5");

    $result_total = $conn->query("SELECT COUNT(DISTINCT id_konsultasi) as total FROM konsultasi WHERE nisn = '$nisn'");
    if ($result_total && $result_total->num_rows > 0) {
        $total_konsultasi = $result_total->fetch_assoc()['total'];
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Welcome Card -->
<div class="card mb-4">
    <div class="card-body" style="background: linear-gradient(135deg, #0d6efd, #0a58ca); border-radius: 15px;">
        <div class="row align-items-center">
            <div class="col-md-8 text-white">
                <h3 class="mb-2">Selamat Datang, <?= htmlspecialchars($nama) ?>!</h3>
                <p class="mb-0 opacity-75">
                    <i class="fas fa-id-card me-2"></i>NISN: <?= htmlspecialchars($nisn) ?>
                </p>
                <p class="mb-0 mt-2 opacity-75">
                    <i class="fas fa-info-circle me-2"></i>
                    Silakan lakukan konsultasi untuk menentukan jurusan yang sesuai dengan minat dan bakat Anda.
                </p>
            </div>
            <div class="col-md-4 text-center mt-3 mt-md-0">
                <a href="<?= base_url('siswa/konsultasi.php') ?>" class="btn btn-light btn-lg px-4">
                    <i class="fas fa-comments me-2"></i>Mulai Konsultasi
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <i class="fas fa-clipboard-check stat-icon"></i>
                <div class="stat-number"><?= $total_konsultasi ?></div>
                <div class="stat-label mt-2">Total Konsultasi</div>
            </div>
        </div>
    </div>
</div>

<!-- History -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Konsultasi</h5>
        <a href="<?= base_url('siswa/hasil.php') ?>" class="btn btn-sm btn-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
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
                            <td><span class="badge bg-primary"><?= htmlspecialchars($row['nama_jurusan']) ?></span></td>
                            <td><span class="badge bg-success"><?= number_format($row['persentase'], 1) ?>%</span></td>
                            <td>
                                <a href="<?= base_url('siswa/hasil.php?id=' . $row['id_konsultasi']) ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada riwayat konsultasi. Mulai konsultasi sekarang!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
