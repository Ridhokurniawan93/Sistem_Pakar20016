<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$is_admin = checkRole('admin');
?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header text-center py-4">
        <i class="fas fa-graduation-cap fa-3x text-white mb-2"></i>
        <h5 class="text-white mb-0">Sistem Pakar</h5>
        <small class="text-white-50">Penentuan Jurusan</small>
    </div>
    
    <ul class="sidebar-nav">
        <?php if ($is_admin): ?>
        <li class="<?= ($current_page == 'dashboard.php' && $current_dir == 'admin') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/dashboard.php') ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="<?= ($current_page == 'jurusan.php') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/jurusan.php') ?>">
                <i class="fas fa-school"></i>
                <span>Data Jurusan</span>
            </a>
        </li>
        <li class="<?= ($current_page == 'atribut.php') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/atribut.php') ?>">
                <i class="fas fa-list-check"></i>
                <span>Data Atribut</span>
            </a>
        </li>
        <li class="<?= ($current_page == 'rule.php') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/rule.php') ?>">
                <i class="fas fa-cogs"></i>
                <span>Data Rule</span>
            </a>
        </li>
        <li class="<?= ($current_page == 'siswa.php') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/siswa.php') ?>">
                <i class="fas fa-users"></i>
                <span>Data Siswa</span>
            </a>
        </li>
        <li class="<?= ($current_page == 'hasil.php' && $current_dir == 'admin') ? 'active' : '' ?>">
            <a href="<?= base_url('admin/hasil.php') ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Hasil Penentuan</span>
            </a>
        </li>
        <?php else: ?>
        <li class="<?= ($current_page == 'dashboard.php' && $current_dir == 'siswa') ? 'active' : '' ?>">
            <a href="<?= base_url('siswa/dashboard.php') ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="<?= ($current_page == 'konsultasi.php') ? 'active' : '' ?>">
            <a href="<?= base_url('siswa/konsultasi.php') ?>">
                <i class="fas fa-comments"></i>
                <span>Konsultasi</span>
            </a>
        </li>
        <li class="<?= ($current_page == 'hasil.php' && $current_dir == 'siswa') ? 'active' : '' ?>">
            <a href="<?= base_url('siswa/hasil.php') ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Hasil Saya</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
    
    <div class="sidebar-footer">
        <a href="<?= base_url('auth/logout.php') ?>">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
