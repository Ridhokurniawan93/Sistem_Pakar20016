<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (isLoggedIn()) {
    if (checkRole('admin')) {
        redirect('admin/dashboard.php');
    } else {
        redirect('siswa/dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Pakar Penentuan Jurusan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="auth-body">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 auth-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-plus fa-4x text-primary mb-3"></i>
                            <h3 class="fw-bold text-primary">Registrasi Siswa</h3>
                            <p class="text-muted">Daftarkan diri Anda untuk mulai konsultasi</p>
                        </div>
                        
                        <?php
                        if (isset($_SESSION['flash_message'])) {
                            echo $_SESSION['flash_message'];
                            unset($_SESSION['flash_message']);
                        }
                        ?>
                        
                        <form action="<?= base_url('auth/register_process.php') ?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label">NISN</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                    <input type="text" class="form-control" name="nisn" 
                                           placeholder="Masukkan NISN" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-user-circle"></i>
                                    </span>
                                    <input type="text" class="form-control" name="username" 
                                           placeholder="Buat username untuk login" required>
                                </div>
                                <small class="text-muted">Username digunakan untuk login</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" name="nama" 
                                           placeholder="Masukkan nama lengkap" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <textarea class="form-control" name="alamat" rows="3" 
                                              placeholder="Masukkan alamat" required></textarea>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" name="password" 
                                           placeholder="Masukkan password" required minlength="6">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Konfirmasi Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" name="konfirmasi_password" 
                                           placeholder="Ulangi password" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                                <i class="fas fa-user-plus me-2"></i>Daftar
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p class="text-muted mb-0">Sudah punya akun? 
                                <a href="<?= base_url('auth/login.php') ?>" class="text-primary fw-bold">
                                    Login di sini
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
