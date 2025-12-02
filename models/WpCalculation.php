<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/KriteriaModel.php';
require_once __DIR__ . '/AlternatifModel.php';

class WpCalculation {
    private $db;
    private $kriteriaModel;
    private $alternatifModel;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->kriteriaModel = new KriteriaModel();
        $this->alternatifModel = new AlternatifModel();
    }
    
    public function calculate($analisis_id) {
        // Get data
        $kriteria = $this->kriteriaModel->getAll($analisis_id);
        $alternatif = $this->alternatifModel->getAll($analisis_id);
        $nilaiData = $this->alternatifModel->getAllNilai($analisis_id);
        
        // Organize nilai
        $nilai = [];
        foreach ($nilaiData as $n) {
            $nilai[$n['alternatif_id']][$n['kriteria_id']] = $n['nilai'];
        }
        
        // Step 1: Normalize bobot (jika total != 1)
        $totalBobot = array_sum(array_column($kriteria, 'bobot'));
        $normalizedBobot = [];
        foreach ($kriteria as $kri) {
            if ($totalBobot > 0 && abs($totalBobot - 1.0) > 0.0001) {
                // Normalisasi jika total != 1
                $normalizedBobot[$kri['id']] = $kri['bobot'] / $totalBobot;
            } else {
                // Sudah ternormalisasi (total = 1)
                $normalizedBobot[$kri['id']] = $kri['bobot'];
            }
        }
        
        // Step 2: Tentukan bobot bertanda (benefit = +, cost = -)
        $signedBobot = [];
        foreach ($kriteria as $kri) {
            if ($kri['tipe'] === 'benefit') {
                $signedBobot[$kri['id']] = +$normalizedBobot[$kri['id']]; // Positif
            } else {
                $signedBobot[$kri['id']] = -$normalizedBobot[$kri['id']]; // Negatif
            }
        }
        
        // Step 3: Calculate S Vector (langsung dari nilai asli dengan bobot bertanda)
        // WP tidak menggunakan normalisasi min/max, langsung pakai nilai asli
        $sVector = [];
        foreach ($alternatif as $alt) {
            $s = 1;
            foreach ($kriteria as $kri) {
                $value = $nilai[$alt['id']][$kri['id']] ?? 0;
                $wj = $signedBobot[$kri['id']];
                // Langsung pakai nilai asli, tidak perlu normalisasi min/max
                if ($value > 0) {
                    $s *= pow($value, $wj);
                } else {
                    $s = 0; // Jika nilai 0 atau negatif, hasilnya 0
                    break;
                }
            }
            
            $sVector[] = [
                'alternatif_id' => $alt['id'],
                'alternatif_nama' => $alt['nama'],
                'nilai_s' => $s,
                'nilai' => $nilai[$alt['id']] ?? []
            ];
        }
        
        // Step 5: Calculate V Vector (normalisasi S vector)
        $totalS = array_sum(array_column($sVector, 'nilai_s'));
        $vVector = [];
        foreach ($sVector as $sItem) {
            $v = $totalS > 0 ? $sItem['nilai_s'] / $totalS : 0;
            $vVector[] = [
                'alternatif_id' => $sItem['alternatif_id'],
                'alternatif_nama' => $sItem['alternatif_nama'],
                'nilai_s' => $sItem['nilai_s'],
                'nilai_v' => $v,
                'nilai' => $sItem['nilai']
            ];
        }
        
        // Step 6: Sort by V Vector (descending) dan tambahkan ranking
        usort($vVector, function($a, $b) {
            return $b['nilai_v'] <=> $a['nilai_v'];
        });
        
        // Format hasil untuk kompatibilitas dengan kode yang ada
        $results = [];
        foreach ($vVector as $index => $vItem) {
            $results[] = [
                'alternatif_id' => $vItem['alternatif_id'],
                'alternatif_nama' => $vItem['alternatif_nama'],
                'nilai_wp' => $vItem['nilai_v'], // Simpan V vector sebagai nilai_wp untuk kompatibilitas
                'nilai_s' => $vItem['nilai_s'],
                'nilai_v' => $vItem['nilai_v'],
                'ranking' => $index + 1,
                'nilai' => $vItem['nilai']
            ];
        }
        
        return $results;
    }
    
    public function saveResults($analisis_id, $results) {
        try {
            // Delete existing results
            $stmt = $this->db->prepare("DELETE FROM hasil WHERE analisis_id = ?");
            $stmt->bind_param("i", $analisis_id);
            if (!$stmt->execute()) {
                throw new Exception('Gagal menghapus hasil lama: ' . $stmt->error);
            }
            
            // Insert new results
            $stmt = $this->db->prepare("INSERT INTO hasil (analisis_id, alternatif_id, nilai_wp, ranking) VALUES (?, ?, ?, ?)");
            
            foreach ($results as $result) {
                $alternatif_id = $result['alternatif_id'];
                $nilai_wp = $result['nilai_wp'];
                $ranking = $result['ranking'];
                
                $stmt->bind_param("iidi", $analisis_id, $alternatif_id, $nilai_wp, $ranking);
                if (!$stmt->execute()) {
                    throw new Exception('Gagal menyimpan hasil: ' . $stmt->error);
                }
            }
            
            return true;
        } catch (Exception $e) {
            throw new Exception('Error saving results: ' . $e->getMessage());
        }
    }
    
    public function getResults($analisis_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT h.*, a.nama as alternatif_nama 
                FROM hasil h
                JOIN alternatif a ON h.alternatif_id = a.id
                WHERE h.analisis_id = ?
                ORDER BY h.ranking
            ");
            $stmt->bind_param("i", $analisis_id);
            if (!$stmt->execute()) {
                throw new Exception('Gagal mengambil hasil: ' . $stmt->error);
            }
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw new Exception('Error getting results: ' . $e->getMessage());
        }
    }
    
    public function getCalculationDetails($analisis_id) {
        // Get data
        $kriteria = $this->kriteriaModel->getAll($analisis_id);
        $alternatif = $this->alternatifModel->getAll($analisis_id);
        $nilaiData = $this->alternatifModel->getAllNilai($analisis_id);
        
        // Organize nilai
        $nilai = [];
        foreach ($nilaiData as $n) {
            $nilai[$n['alternatif_id']][$n['kriteria_id']] = $n['nilai'];
        }
        
        // Step 1: Normalize bobot (jika total != 1)
        $totalBobot = array_sum(array_column($kriteria, 'bobot'));
        $normalizedBobot = [];
        foreach ($kriteria as $kri) {
            if ($totalBobot > 0 && abs($totalBobot - 1.0) > 0.0001) {
                // Normalisasi jika total != 1
                $normalizedBobot[$kri['id']] = $kri['bobot'] / $totalBobot;
            } else {
                // Sudah ternormalisasi (total = 1)
                $normalizedBobot[$kri['id']] = $kri['bobot'];
            }
        }
        
        // Step 2: Tentukan bobot bertanda (benefit = +, cost = -)
        $signedBobot = [];
        foreach ($kriteria as $kri) {
            if ($kri['tipe'] === 'benefit') {
                $signedBobot[$kri['id']] = +$normalizedBobot[$kri['id']]; // Positif
            } else {
                $signedBobot[$kri['id']] = -$normalizedBobot[$kri['id']]; // Negatif
            }
        }
        
        // Step 3: Calculate S Vector dengan details (langsung dari nilai asli)
        // WP tidak menggunakan normalisasi min/max, langsung pakai nilai asli
        $sVectorDetails = [];
        foreach ($alternatif as $alt) {
            $s = 1;
            $steps = [];
            
            foreach ($kriteria as $kri) {
                $value = $nilai[$alt['id']][$kri['id']] ?? 0;
                $wj = $signedBobot[$kri['id']];
                // Langsung pakai nilai asli, tidak perlu normalisasi min/max
                if ($value > 0) {
                    $powered = pow($value, $wj);
                    $s *= $powered;
                } else {
                    $powered = 0;
                    $s = 0;
                }
                
                $steps[] = [
                    'kriteria_id' => $kri['id'],
                    'kriteria_nama' => $kri['nama'],
                    'nilai_asli' => $value, // Simpan nilai asli, bukan normalized
                    'bobot_original' => $kri['bobot'],
                    'bobot_normalized' => $normalizedBobot[$kri['id']],
                    'bobot_signed' => $wj,
                    'powered' => $powered
                ];
            }
            
            $sVectorDetails[] = [
                'alternatif_id' => $alt['id'],
                'alternatif_nama' => $alt['nama'],
                'nilai_s' => $s,
                'steps' => $steps
            ];
        }
        
        // Step 4: Calculate V Vector (normalisasi S vector)
        $totalS = array_sum(array_column($sVectorDetails, 'nilai_s'));
        $vVectorDetails = [];
        foreach ($sVectorDetails as $sDetail) {
            $v = $totalS > 0 ? $sDetail['nilai_s'] / $totalS : 0;
            $vVectorDetails[] = [
                'alternatif_id' => $sDetail['alternatif_id'],
                'alternatif_nama' => $sDetail['alternatif_nama'],
                'nilai_s' => $sDetail['nilai_s'],
                'nilai_v' => $v,
                'steps' => $sDetail['steps']
            ];
        }
        
        // Step 5: Sort by V Vector (descending) dan tambahkan ranking
        usort($vVectorDetails, function($a, $b) {
            return $b['nilai_v'] <=> $a['nilai_v'];
        });
        
        // Add ranking
        foreach ($vVectorDetails as $index => &$detail) {
            $detail['ranking'] = $index + 1;
            // Untuk kompatibilitas dengan kode yang ada
            $detail['nilai_wp'] = $detail['nilai_v'];
        }
        
        // Organize nilai untuk view (hanya nilai, bukan array)
        $nilaiSimple = [];
        foreach ($alternatif as $alt) {
            $nilaiSimple[$alt['id']] = [];
            foreach ($kriteria as $kri) {
                $nilaiSimple[$alt['id']][$kri['id']] = $nilai[$alt['id']][$kri['id']] ?? 0;
            }
        }
        
        return [
            'kriteria' => $kriteria,
            'alternatif' => $alternatif,
            'nilai' => $nilaiSimple,
            'normalizedBobot' => $normalizedBobot,
            'signedBobot' => $signedBobot,
            'totalS' => $totalS,
            'wpDetails' => $vVectorDetails // Berisi S dan V vector
        ];
    }
}
?>







