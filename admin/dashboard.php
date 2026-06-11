<?php
$page_title = 'Dashboard Admin';
require_once __DIR__ . '/../includes/session_start.php';
require_once __DIR__ . '/../includes/auth_check.php';
// Get statistics
$total_jurusan = $conn->query("SELECT COUNT(*) as total FROM jurusan")->fetch_assoc()['total'];
$total_atribut = $conn->query("SELECT COUNT(*) as total FROM atribut")->fetch_assoc()['total'];
$total_rule = $conn->query("SELECT COUNT(*) as total FROM rule_jurusan")->fetch_assoc()['total'];
$total_siswa = $conn->query("SELECT COUNT(*) as total FROM siswa")->fetch_assoc()['total'];
$total_hasil = $conn->query("SELECT COUNT(*) as total FROM hasil_penentuan")->fetch_assoc()['total'];

// Recent consultations
$recent = $conn->query("SELECT k.id_konsultasi, s.nama, s.nisn, k.tanggal, j.nama_jurusan, hp.persentase 
    FROM konsultasi k 
    JOIN siswa s ON k.nisn = s.nisn 
    JOIN hasil_penentuan hp ON k.id_konsultasi = hp.id_konsultasi 
    JOIN jurusan j ON hp.id_jurusan = j.id_jurusan 
    WHERE hp.persentase = (
        SELECT MAX(persentase)
        FROM hasil_penentuan
        WHERE id_konsultasi = k.id_konsultasi
    )
    ORDER BY k.created_at DESC
    LIMIT 5");

// Header
include __DIR__ . '/../includes/header.php';
?>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <i class="fas fa-school stat-icon"></i>
                <div class="stat-number"><?= $total_jurusan ?></div>
                <div class="stat-label mt-2">Total Jurusan</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <i class="fas fa-list-check stat-icon"></i>
                <div class="stat-number"><?= $total_atribut ?></div>
                <div class="stat-label mt-2">Total Atribut</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-warning text-white">
            <div class="card-body">
                <i class="fas fa-cogs stat-icon"></i>
                <div class="stat-number"><?= $total_rule ?></div>
                <div class="stat-label mt-2">Total Rule</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-info text-white">
            <div class="card-body">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-number"><?= $total_siswa ?></div>
                <div class="stat-label mt-2">Total Siswa</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card bg-danger text-white">
            <div class="card-body">
                <i class="fas fa-chart-bar stat-icon"></i>
                <div class="stat-number"><?= $total_hasil ?></div>
                <div class="stat-label mt-2">Total Hasil Penentuan</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Consultations -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Konsultasi Terbaru</h5>
        <a href="<?= base_url('admin/hasil.php') ?>" class="btn btn-sm btn-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Tanggal</th>
                        <th>Jurusan Rekomendasi</th>
                        <th>Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent && $recent->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $recent->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['nisn']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><span class="badge bg-primary"><?= htmlspecialchars($row['nama_jurusan']) ?></span></td>
                            <td><span class="badge bg-success"><?= number_format($row['persentase'], 1) ?>%</span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada data konsultasi
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
