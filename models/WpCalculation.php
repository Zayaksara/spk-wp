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
        
        // Cache max/min untuk setiap kriteria (optimasi performance)
        $maxMin = [];
        foreach ($kriteria as $kri) {
            $max = 0;
            $min = PHP_INT_MAX;
            
            foreach ($alternatif as $alt) {
                $val = $nilai[$alt['id']][$kri['id']] ?? 0;
                if ($val > $max) $max = $val;
                if ($val > 0 && $val < $min) $min = $val;
            }
            
            $maxMin[$kri['id']] = [
                'max' => $max,
                'min' => ($min > 0 && $min < PHP_INT_MAX) ? $min : 1
            ];
        }
        
        // Step 1: Normalize values (menggunakan cache)
        $normalized = [];
        foreach ($alternatif as $alt) {
            $normalized[$alt['id']] = [];
            foreach ($kriteria as $kri) {
                $value = $nilai[$alt['id']][$kri['id']] ?? 0;
                
                if ($kri['tipe'] === 'benefit') {
                    $max = $maxMin[$kri['id']]['max'];
                    $normalized[$alt['id']][$kri['id']] = $max > 0 ? $value / $max : 0;
                } else {
                    $min = $maxMin[$kri['id']]['min'];
                    $normalized[$alt['id']][$kri['id']] = ($min > 0 && $value > 0) ? $min / $value : 0;
                }
            }
        }
        
        // Step 2: Calculate WP (Weighted Product)
        $results = [];
        foreach ($alternatif as $alt) {
            $wp = 1;
            foreach ($kriteria as $kri) {
                $normValue = $normalized[$alt['id']][$kri['id']] ?? 0;
                $wp *= pow($normValue, $kri['bobot']);
            }
            
            $results[] = [
                'alternatif_id' => $alt['id'],
                'alternatif_nama' => $alt['nama'],
                'nilai_wp' => $wp,
                'nilai' => $nilai[$alt['id']] ?? []
            ];
        }
        
        // Step 3: Sort by WP (descending)
        usort($results, function($a, $b) {
            return $b['nilai_wp'] <=> $a['nilai_wp'];
        });
        
        // Step 4: Add ranking
        foreach ($results as $index => &$result) {
            $result['ranking'] = $index + 1;
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
}
?>

