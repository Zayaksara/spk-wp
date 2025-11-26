<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AnalisisModel.php';
require_once __DIR__ . '/../models/KriteriaModel.php';
require_once __DIR__ . '/../models/AlternatifModel.php';
require_once __DIR__ . '/../models/WpCalculation.php';

class ResultController {
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
    
    public function view($id) {
        $analisis = $this->analisisModel->getById($id);
        if (!$analisis) {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        $alternatif = $this->alternatifModel->getAll($id);
        $kriteria = $this->kriteriaModel->getAll($id);
        $nilaiData = $this->alternatifModel->getAllNilai($id);
        $hasil = $this->wpCalculation->getResults($id);
        
        // Organize nilai
        $nilai = [];
        foreach ($nilaiData as $n) {
            $nilai[$n['alternatif_id']][$n['kriteria_id']] = $n;
        }
        
        require_once __DIR__ . '/../views/result.php';
    }
}
?>
