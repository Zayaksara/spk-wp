<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AnalisisModel.php';
require_once __DIR__ . '/../models/KriteriaModel.php';
require_once __DIR__ . '/../models/AlternatifModel.php';
require_once __DIR__ . '/../models/WpCalculation.php';

class DashboardController {
    private $analisisModel;
    private $kriteriaModel;
    private $alternatifModel;
    private $wpCalculation;
    
    public function __construct() {
        $this->analisisModel = new AnalisisModel();
        $this->kriteriaModel = new KriteriaModel();
        $this->alternatifModel = new AlternatifModel();
        $this->wpCalculation = new WpCalculation();
    }
    
    public function index() {
        // Get statistics
        $allAnalisis = $this->analisisModel->getAll();
        
        $stats = [
            'total_analisis' => count($allAnalisis),
            'analisis_bulan_ini' => 0,
            'total_alternatif' => 0,
            'total_kriteria' => 0,
            'analisis_terbaru' => array_slice($allAnalisis, 0, 5)
        ];
        
        // Calculate monthly stats
        $currentMonth = date('Y-m');
        foreach ($allAnalisis as $analisis) {
            if (strpos($analisis['created_at'], $currentMonth) === 0) {
                $stats['analisis_bulan_ini']++;
            }
        }
        
        // Get total alternatif and kriteria
        foreach ($allAnalisis as $analisis) {
            $alternatif = $this->alternatifModel->getAll($analisis['id']);
            $kriteria = $this->kriteriaModel->getAll($analisis['id']);
            $stats['total_alternatif'] += count($alternatif);
            $stats['total_kriteria'] += count($kriteria);
        }
        
        // Get analisis with results
        $analisis_with_results = [];
        foreach ($stats['analisis_terbaru'] as $analisis) {
            try {
                $hasil = $this->wpCalculation->getResults($analisis['id']);
                $analisis['has_results'] = count($hasil) > 0;
            } catch (Exception $e) {
                $analisis['has_results'] = false;
            }
            $analisis['alternatif_count'] = count($this->alternatifModel->getAll($analisis['id']));
            $analisis['kriteria_count'] = count($this->kriteriaModel->getAll($analisis['id']));
            $analisis_with_results[] = $analisis;
        }
        $stats['analisis_terbaru'] = $analisis_with_results;
        
        require_once __DIR__ . '/../views/dashboard.php';
    }
}
?>

