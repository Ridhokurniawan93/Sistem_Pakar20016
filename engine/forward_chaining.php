<?php
/**
 * Forward Chaining Engine
 * Sistem Pakar Penentuan Jurusan Berdasarkan Minat dan Bakat
 * 
 * Algoritma:
 * 1. Sistem menerima jawaban siswa (array id_atribut yang dipilih)
 * 2. Jawaban dicocokkan dengan rule setiap jurusan
 * 3. Jika kondisi rule terpenuhi (atribut cocok), rule aktif
 * 4. Hitung persentase kecocokan per jurusan
 * 5. Urutkan dari persentase tertinggi ke terendah
 */

class ForwardChaining {
    
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Load semua rule dari database
     * @return array [id_jurusan => [id_atribut1, id_atribut2, ...], ...]
     */
    public function loadRules() {
        $rules = [];
        $sql = "SELECT r.id_jurusan, j.nama_jurusan, r.id_atribut, a.nama_atribut, a.kategori
                FROM rule_jurusan r
                JOIN jurusan j ON r.id_jurusan = j.id_jurusan
                JOIN atribut a ON r.id_atribut = a.id_atribut
                ORDER BY r.id_jurusan, r.id_atribut";
        $result = $this->conn->query($sql);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $id_jurusan = $row['id_jurusan'];
                if (!isset($rules[$id_jurusan])) {
                    $rules[$id_jurusan] = [
                        'nama_jurusan' => $row['nama_jurusan'],
                        'atribut' => []
                    ];
                }
                $rules[$id_jurusan]['atribut'][] = [
                    'id_atribut' => $row['id_atribut'],
                    'nama_atribut' => $row['nama_atribut'],
                    'kategori' => $row['kategori']
                ];
            }
        }
        
        return $rules;
    }
    
    /**
     * Proses Forward Chaining
     * @param array $jawaban Array of id_atribut yang dipilih siswa
     * @return array Hasil penentuan dengan persentase
     */
    public function proses($jawaban) {
        $rules = $this->loadRules();
        $hasil = [];
        
        foreach ($rules as $id_jurusan => $data) {
            $matched = 0;
            $total = count($data['atribut']);
            $matched_details = [];
            
            // Forward Chaining: cek setiap kondisi rule
            foreach ($data['atribut'] as $atribut) {
                if (in_array($atribut['id_atribut'], $jawaban)) {
                    $matched++;
                    $matched_details[] = $atribut;
                }
            }
            
            // Hitung persentase kecocokan
            $persentase = ($total > 0) ? round(($matched / $total) * 100, 2) : 0;
            
            // Rule aktif jika persentase > 0
            $aktif = $persentase > 0;
            
            $hasil[] = [
                'id_jurusan' => $id_jurusan,
                'nama_jurusan' => $data['nama_jurusan'],
                'total_atribut' => $total,
                'matched' => $matched,
                'persentase' => $persentase,
                'aktif' => $aktif,
                'matched_details' => $matched_details
            ];
        }
        
        // Urutkan berdasarkan persentase tertinggi
        usort($hasil, function($a, $b) {
            return $b['persentase'] - $a['persentase'];
        });
        
        return $hasil;
    }
    
    /**
     * Get jurusan terbaik (persentase tertinggi)
     * @param array $hasil Hasil dari proses()
     * @return array|null Jurusan terbaik atau null
     */
    public function getBestMatch($hasil) {
        if (empty($hasil)) return null;
        
        // Return jurusan dengan persentase tertinggi yang > 0
        foreach ($hasil as $h) {
            if ($h['persentase'] > 0) {
                return $h;
            }
        }
        return null;
    }
    
    /**
     * Simpan hasil konsultasi ke database
     * @param string $nisn
     * @param array $jawaban Array [id_atribut => jawaban_text]
     * @param array $hasil Hasil dari proses()
     * @return int ID konsultasi
     */
    public function simpanHasil($nisn, $jawaban, $hasil) {
        $nisn = $this->conn->real_escape_string($nisn);
        $tanggal = date('Y-m-d');
        
        $this->conn->begin_transaction();
        
        try {
            // Insert konsultasi
            $sql = "INSERT INTO konsultasi (nisn, tanggal) VALUES ('$nisn', '$tanggal')";
            $this->conn->query($sql);
            $id_konsultasi = $this->conn->insert_id;
            
            // Insert detail konsultasi
            foreach ($jawaban as $id_atribut => $jawaban_text) {
                $id_atribut = intval($id_atribut);
                $jawaban_text = $this->conn->real_escape_string($jawaban_text);
                $this->conn->query("INSERT INTO detail_konsultasi (id_konsultasi, id_atribut, jawaban) 
                                    VALUES ($id_konsultasi, $id_atribut, '$jawaban_text')");
            }
            
            // Insert hasil penentuan (semua jurusan dengan persentase > 0)
            foreach ($hasil as $h) {
                if ($h['persentase'] > 0) {
                    $id_jurusan = intval($h['id_jurusan']);
                    $persentase = floatval($h['persentase']);
                    $this->conn->query("INSERT INTO hasil_penentuan (id_konsultasi, id_jurusan, persentase) 
                                        VALUES ($id_konsultasi, $id_jurusan, $persentase)");
                }
            }
            
            $this->conn->commit();
            return $id_konsultasi;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
}
?>
