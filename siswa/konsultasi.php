<?php
$page_title = 'Konsultasi Penentuan Jurusan';
require_once __DIR__ . '/../includes/session_start.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../engine/forward_chaining.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_konsultasi'])) {
    $jawaban_ids = [];
    $jawaban_text = [];
    
    foreach ($_POST['jawaban'] as $id_atribut => $value) {
        $jawaban_ids[] = intval($value);
        // Get the nama_atribut for the selected answer
        $id_val = intval($value);
        $attr_result = $conn->query("SELECT nama_atribut FROM atribut WHERE id_atribut = $id_val");
        if ($attr_result && $attr_result->num_rows > 0) {
            $jawaban_text[$id_val] = $attr_result->fetch_assoc()['nama_atribut'];
        }
    }
    
    // Process with Forward Chaining
    $engine = new ForwardChaining($conn);
    $hasil = $engine->proses($jawaban_ids);
    
    // Save to database
    try {
        $id_konsultasi = $engine->simpanHasil($_SESSION['nisn'], $jawaban_text, $hasil);
        redirect('siswa/hasil.php?id=' . $id_konsultasi);
    } catch (Exception $e) {
        $error = "Gagal menyimpan hasil konsultasi: " . $e->getMessage();
    }
}

// Get atribut grouped by kategori (only kategori used in rules)
$kategoris_used = $conn->query("SELECT DISTINCT a.kategori 
    FROM atribut a 
    INNER JOIN rule_jurusan r ON a.id_atribut = r.id_atribut 
    ORDER BY FIELD(a.kategori, 'Cita-cita','Minat','Kesukaan','Keingintahuan','Kebutuhan','Pengetahuan','Mudah Memahami','Mampu Mengenal Masalah','Kreatif','Inovatif')");

$kategori_list = [];
while ($k = $kategoris_used->fetch_assoc()) {
    $kategori_list[] = $k['kategori'];
}

// Get atribut per kategori
$atribut_by_kategori = [];
foreach ($kategori_list as $kat) {
    $kat_escaped = $conn->real_escape_string($kat);
    $result = $conn->query("SELECT * FROM atribut WHERE kategori = '$kat_escaped' ORDER BY id_atribut");
    $atribut_by_kategori[$kat] = [];
    while ($row = $result->fetch_assoc()) {
        $atribut_by_kategori[$kat][] = $row;
    }
}

// Question text per kategori
$pertanyaan = [
    'Cita-cita' => 'Apa cita-cita Anda di masa depan?',
    'Minat' => 'Apa yang paling menarik minat Anda?',
    'Kesukaan' => 'Apa yang paling Anda sukai?',
    'Keingintahuan' => 'Bidang apa yang paling membuat Anda penasaran?',
    'Kebutuhan' => 'Apa yang menurut Anda paling dibutuhkan saat ini?',
    'Pengetahuan' => 'Bidang apa yang paling Anda pahami dasarnya?',
    'Mudah Memahami' => 'Masalah di bidang apa yang paling mudah Anda pahami?',
    'Mampu Mengenal Masalah' => 'Di bidang apa Anda mampu mengenali masalah?',
    'Kreatif' => 'Di bidang apa Anda paling kreatif?',
    'Inovatif' => 'Di bidang apa Anda mampu berinovasi?',
    'Motivasi' => 'Apa yang paling memotivasi Anda?',
    'Bakat' => 'Di bidang apa Anda merasa paling berbakat?'
];

$kategori_icons = [
    'Cita-cita' => 'fa-star', 'Minat' => 'fa-heart', 'Kesukaan' => 'fa-thumbs-up',
    'Keingintahuan' => 'fa-lightbulb', 'Kebutuhan' => 'fa-hand-holding', 'Pengetahuan' => 'fa-book',
    'Mudah Memahami' => 'fa-brain', 'Mampu Mengenal Masalah' => 'fa-search', 'Kreatif' => 'fa-palette',
    'Inovatif' => 'fa-rocket', 'Motivasi' => 'fa-fire', 'Bakat' => 'fa-award'
];

require_once __DIR__ . '/../includes/header.php';
?>

<?php if (isset($error)): ?>
<?= showAlert($error, 'danger') ?>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="text-primary mb-2"><i class="fas fa-clipboard-list me-2"></i>Konsultasi Penentuan Jurusan</h4>
                <p class="text-muted mb-0">Jawablah seluruh pertanyaan di bawah ini sesuai dengan minat dan bakat Anda. Sistem akan menganalisis jawaban Anda menggunakan metode Forward Chaining untuk menentukan jurusan yang paling sesuai.</p>
            </div>
            <div class="col-md-4 text-center mt-3 mt-md-0">
                <div class="alert alert-info mb-0 py-2">
                    <i class="fas fa-info-circle me-1"></i>
                    Total: <strong><?= count($kategori_list) ?></strong> pertanyaan
                </div>
            </div>
        </div>
    </div>
</div>

<form id="formKonsultasi" method="POST" action="">
    <?php $no = 1; foreach ($kategori_list as $kat): ?>
    <div class="card question-card question-group mb-3">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas <?= $kategori_icons[$kat] ?? 'fa-question' ?> me-2"></i>
                Pertanyaan <?= $no ?>: <?= htmlspecialchars($kat) ?>
            </h6>
            <small class="opacity-75"><?= $pertanyaan[$kat] ?? 'Pilih salah satu jawaban' ?></small>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($atribut_by_kategori[$kat] as $attr): ?>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" 
                               name="jawaban[<?= $kat ?>]" 
                               value="<?= $attr['id_atribut'] ?>" 
                               id="attr_<?= $attr['id_atribut'] ?>"
                               required>
                        <label class="form-check-label" for="attr_<?= $attr['id_atribut'] ?>">
                            <?= htmlspecialchars($attr['nama_atribut']) ?>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php $no++; endforeach; ?>
    
    <div class="text-center mt-4">
        <button type="submit" name="submit_konsultasi" class="btn btn-primary btn-lg px-5">
            <i class="fas fa-paper-plane me-2"></i>Proses Jawaban
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
