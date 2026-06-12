# Sistem Pakar Penentuan Jurusan Berdasarkan Minat dan Bakat

Aplikasi website Sistem Pakar untuk membantu calon siswa menentukan jurusan yang sesuai berdasarkan minat dan bakat menggunakan **Metode Forward Chaining**.

---

## Teknologi

| Teknologi | Keterangan |
|-----------|-----------|
| HTML5 | Struktur halaman |
| CSS3 | Styling custom |
| JavaScript | Interaksi & AJAX |
| PHP Native | Backend (tanpa framework) |
| MySQL | Database |
| Bootstrap 5 | UI Framework |
| Font Awesome 6 | Icon |
| DataTables | Tabel interaktif |
| SweetAlert2 | Alert modern |

---

## Fitur Sistem

### Admin
- Dashboard statistik (Total Jurusan, Atribut, Rule, Siswa, Hasil Penentuan)
- CRUD Data Jurusan
- CRUD Data Atribut (12 kategori: Minat, Cita-cita, Kesukaan, dll)
- CRUD Data Rule (mapping atribut ke jurusan)
- CRUD Data Siswa
- Lihat semua hasil konsultasi siswa

### Siswa
- Registrasi akun (NISN, Nama, Alamat, Password)
- Konsultasi penentuan jurusan (jawab pertanyaan per kategori)
- Lihat hasil rekomendasi jurusan dengan persentase kecocokan
- Cetak hasil konsultasi (PDF via print browser)

### Mesin Inferensi
- Metode **Forward Chaining**
- Mencocokkan jawaban siswa dengan rule setiap jurusan
- Menghitung persentase kecocokan per jurusan
- Menampilkan jurusan dengan kecocokan tertinggi

---

## Jurusan

1. Teknik Komputer Jaringan (TKJ)
2. Teknik Instalasi Tenaga Listrik (TITL)
3. Teknik Bisnis Sepeda Motor (TBSM)
4. Teknik Kendaraan Ringan (TKR)
5. Multimedia (MM)
6. Otomatisasi Tata Kelola Perkantoran (OTKP)
7. Akuntansi Keuangan Lembaga (AKL)

---

## Database

Nama database: `db_sistem_pakar_jurusan`

### Tabel

| Tabel | Keterangan |
|-------|-----------|
| `users` | Data login (admin & siswa) |
| `siswa` | Data profil siswa |
| `jurusan` | Data jurusan |
| `atribut` | Data atribut/pertanyaan per kategori |
| `rule_jurusan` | Mapping rule antara jurusan dan atribut |
| `konsultasi` | Riwayat konsultasi siswa |
| `detail_konsultasi` | Jawaban siswa per konsultasi |
| `hasil_penentuan` | Hasil rekomendasi jurusan + persentase |

---

## Instalasi

### Persyaratan

- PHP >= 7.4 (direkomendasikan PHP 8.x)
- MySQL / MariaDB
- Web Server: XAMPP / WAMP / Laragon

### Langkah-langkah

#### 1. Setup Database

1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`)
2. Buat database baru bernama `db_sistem_pakar_jurusan`
3. Import file SQL: `database/db_sistem_pakar_jurusan.sql`

#### 2. Setup Project

**Opsi A: Menggunakan XAMPP/Apache**

1. Salin folder project ke dalam `htdocs` (contoh: `C:\xampp\htdocs\Web_Sistem Pakar`)
2. Pastikan Apache & MySQL sudah running di XAMPP
3. Akses via browser: `http://localhost/Web_Sistem Pakar/`

**Opsi B: Menggunakan PHP Built-in Server (Tanpa XAMPP)**

1. Buka terminal/command prompt di folder project
2. Jalankan perintah:
   ```bash
   php -S localhost:8000
   ```
3. Akses via browser: `http://localhost:8000/`

#### 3. Login

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| Siswa | (daftar dulu) | (saat registrasi) |

---

## Struktur Folder

```
Web_Sistem Pakar/
├── index.php                    # Entry point
├── config/
│   └── database.php             # Koneksi DB & helper functions
├── database/
│   └── db_sistem_pakar_jurusan.sql
├── assets/
│   ├── css/style.css            # Custom CSS
│   └── js/script.js             # Custom JS
├── includes/
│   ├── session_start.php        # Start session + load config
│   ├── auth_check.php           # Session guard & role check
│   ├── header.php               # HTML head + navbar
│   ├── sidebar.php              # Sidebar navigasi
│   └── footer.php               # Footer + scripts
├── auth/
│   ├── login.php                # Halaman login
│   ├── register.php             # Halaman registrasi siswa
│   ├── login_process.php        # Proses login
│   ├── register_process.php     # Proses registrasi
│   └── logout.php               # Logout
├── admin/
│   ├── dashboard.php            # Dashboard admin
│   ├── jurusan.php              # CRUD Jurusan
│   ├── atribut.php              # CRUD Atribut
│   ├── rule.php                 # CRUD Rule
│   ├── siswa.php                # CRUD Siswa
│   ├── hasil.php                # Lihat hasil konsultasi
│   └── ajax/
│       ├── jurusan_ajax.php     # AJAX endpoint Jurusan
│       ├── atribut_ajax.php     # AJAX endpoint Atribut
│       ├── rule_ajax.php        # AJAX endpoint Rule
│       └── siswa_ajax.php       # AJAX endpoint Siswa
├── siswa/
│   ├── dashboard.php            # Dashboard siswa
│   ├── konsultasi.php           # Form konsultasi
│   ├── hasil.php                # Hasil konsultasi
│   └── cetak.php                # Cetak PDF
├── engine/
│   └── forward_chaining.php     # Mesin inferensi Forward Chaining
└── libraries/
```

---

## Cara Kerja Forward Chaining

```
1. Siswa menjawab semua pertanyaan (10 kategori)
2. Sistem menyimpan jawaban sebagai fakta (array id_atribut)
3. Untuk setiap jurusan, sistem memeriksa rule:
   IF [atribut1] AND [atribut2] AND ... AND [atributN]
   THEN Jurusan = X
4. Setiap atribut yang cocok dihitung
5. Persentase = (atribut_cocok / total_atribut_rule) x 100%
6. Hasil diurutkan dari persentase tertinggi
7. Jurusan dengan persentase tertinggi = rekomendasi
```

### Contoh

```
Rule TKJ: Programmer, Bermain Komputer, Penggunaan Komputer, ...

Jawaban Siswa: Programmer, Bermain Komputer, Desain Gambar, ...

Match: 2 dari 10 atribut → Persentase = 20%
```

---

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| `ERR_TOO_MANY_REDIRECTS` | Pastikan `session_start()` ada di setiap halaman yang butuh auth |
| Halaman 404 Not Found | Cek nama folder di URL harus sama persis dengan nama folder project |
| Koneksi database gagal | Pastikan MySQL running dan database sudah di-import |
| CSS/JS tidak muncul | Cek `base_url` di `config/database.php` sudah benar |
| Logout error | Pastikan `config/database.php` di-include sebelum `redirect()` |

---

## Default Login

- **Admin**: username `admin` / password `admin123`
- **Siswa**: Registrasi terlebih dahulu di halaman register

---

## Lisensi

Project ini dibuat untuk project UAS
