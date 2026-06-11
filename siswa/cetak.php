<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['level'] !== 'siswa') {
    $pf = basename(dirname(__DIR__));
    header("Location: /$pf/auth/login.php");
    exit();
}

$nisn = $_SESSION['nisn'];
$id_konsultasi = intval($_GET['id'] ?? 0);

// Verify ownership
$check = $conn->query("SELECT * FROM konsultasi WHERE id_konsultasi = $id_konsultasi AND nisn = '$nisn'");
if (!$check || $check->num_rows === 0) {
    die("Data tidak ditemukan");
}
$konsultasi = $check->fetch_assoc();

// Get student info
$siswa = $conn->query("SELECT * FROM siswa WHERE nisn = '$nisn'")->fetch_assoc();

// Get results
$hasil = $conn->query("SELECT hp.*, j.nama_jurusan 
    FROM hasil_penentuan hp 
    JOIN jurusan j ON hp.id_jurusan = j.id_jurusan 
    WHERE hp.id_konsultasi = $id_konsultasi 
    ORDER BY hp.persentase DESC");

// Get detail
$detail = $conn->query("SELECT dk.*, a.nama_atribut, a.kategori 
    FROM detail_konsultasi dk 
    JOIN atribut a ON dk.id_atribut = a.id_atribut 
    WHERE dk.id_konsultasi = $id_konsultasi 
    ORDER BY a.kategori");

$hasil_array = [];
if ($hasil) {
    while ($row = $hasil->fetch_assoc()) {
        $hasil_array[] = $row;
    }
}
$best = !empty($hasil_array) ? $hasil_array[0] : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Hasil - Sistem Pakar Penentuan Jurusan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        .print-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-print {
            text-align: center;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-print h2 {
            color: #0d6efd;
            font-weight: 700;
        }
        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .result-box {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 25px;
        }
        .result-box h3 { font-size: 1.8rem; }
        .result-box .percentage { font-size: 3rem; font-weight: 700; }
        .progress-custom {
            height: 20px;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .progress-bar-custom {
            border-radius: 10px;
        }
        .table-print { width: 100%; border-collapse: collapse; }
        .table-print th, .table-print td {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            font-size: 0.9rem;
        }
        .table-print th { background: #0d6efd; color: white; }
        .footer-print {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            color: #6c757d;
            font-size: 0.85rem;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        @media print {
            body { background: white; }
            .print-container { box-shadow: none; margin: 0; padding: 20px; }
            .no-print { display: none !important; }
            .result-box { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
            }
            .table-print th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="no-print text-center mb-3 pt-3">
        <button onclick="window.print()" class="btn btn-primary btn-lg">
            <i class="fas fa-print me-2"></i>Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-lg ms-2">
            <i class="fas fa-times me-2"></i>Tutup
        </button>
    </div>

    <div class="print-container">
        <!-- Header -->
        <div class="header-print">
            <h2>SISTEM PAKAR PENENTUAN JURUSAN</h2>
            <p class="mb-0">Berdasarkan Minat dan Bakat Siswa</p>
            <small class="text-muted">Metode Forward Chaining</small>
        </div>

        <!-- Student Info -->
        <div class="info-box">
            <div class="row">
                <div class="col-md-4">
                    <strong>Nama Siswa:</strong><br>
                    <?= htmlspecialchars($siswa['nama']) ?>
                </div>
                <div class="col-md-4">
                    <strong>NISN:</strong><br>
                    <?= htmlspecialchars($siswa['nisn']) ?>
                </div>
                <div class="col-md-4">
                    <strong>Tanggal Konsultasi:</strong><br>
                    <?= date('d F Y', strtotime($konsultasi['tanggal'])) ?>
                </div>
            </div>
        </div>

        <!-- Best Result -->
        <?php if ($best): ?>
        <div class="result-box">
            <h5 class="opacity-75 mb-2">JURUSAN YANG DIREKOMENDASIKAN</h5>
            <h3><?= htmlspecialchars($best['nama_jurusan']) ?></h3>
            <div class="percentage"><?= number_format($best['persentase'], 1) ?>%</div>
            <p class="mb-0 opacity-75">Tingkat Kecocokan</p>
        </div>
        <?php endif; ?>

        <!-- All Results -->
        <h5 class="mb-3"><strong>Detail Analisis Semua Jurusan</strong></h5>
        <table class="table-print">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jurusan</th>
                    <th>Cocok</th>
                    <th>Total Atribut</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hasil_array as $index => $h): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($h['nama_jurusan']) ?></td>
                    <td style="text-align:center">-</td>
                    <td style="text-align:center">-</td>
                    <td style="text-align:center"><strong><?= number_format($h['persentase'], 1) ?>%</strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Jawaban -->
        <h5 class="mb-3 mt-4"><strong>Jawaban Siswa</strong></h5>
        <table class="table-print">
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
                        <td><?= htmlspecialchars($d['kategori']) ?></td>
                        <td><?= htmlspecialchars($d['jawaban']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Kesimpulan -->
        <?php if ($best): ?>
        <div class="mt-4 p-3" style="background: #e8f5e9; border-radius: 10px; border-left: 4px solid #198754;">
            <h6 class="text-success mb-2"><strong>Kesimpulan:</strong></h6>
            <p class="mb-0">
                Berdasarkan analisis menggunakan metode <strong>Forward Chaining</strong>, 
                jurusan yang paling sesuai untuk <strong><?= htmlspecialchars($siswa['nama']) ?></strong> 
                adalah <strong><?= htmlspecialchars($best['nama_jurusan']) ?></strong> 
                dengan tingkat kecocokan sebesar <strong><?= number_format($best['persentase'], 1) ?>%</strong>.
            </p>
        </div>
        <?php endif; ?>

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Siswa,</p>
                <div class="signature-line">
                    <strong><?= htmlspecialchars($siswa['nama']) ?></strong><br>
                    NISN: <?= htmlspecialchars($siswa['nisn']) ?>
                </div>
            </div>
            <div class="signature-box">
                <p>Admin Sistem,</p>
                <div class="signature-line">
                    <strong>Administrator</strong>
                </div>
            </div>
        </div>

        <div class="footer-print">
            <p>Dokumen ini dicetak oleh Sistem Pakar Penentuan Jurusan pada tanggal <?= date('d F Y H:i:s') ?></p>
        </div>
    </div>

    <script>
        // Auto print after page load
        window.onload = function() {
            // Don't auto-print, let user click the button
        };
    </script>
</body>
</html>
