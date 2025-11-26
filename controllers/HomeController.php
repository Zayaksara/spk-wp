<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AnalisisModel.php';

class HomeController {
    private $analisisModel;
    
    public function __construct() {
        $this->analisisModel = new AnalisisModel();
    }
    
    public function index() {
        $analisis_list = $this->analisisModel->getAll();
        require_once __DIR__ . '/../views/home.php';
    }
    
    public function create() {
        require_once __DIR__ . '/../views/input_kriteria.php';
    }
    
    public function edit($id) {
        require_once __DIR__ . '/../models/KriteriaModel.php';
        require_once __DIR__ . '/../models/AlternatifModel.php';
        
        $analisis = $this->analisisModel->getById($id);
        if (!$analisis) {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Get alternatif
        $alternatifModel = new AlternatifModel();
        $analisis['alternatif'] = $alternatifModel->getAll($id);
        
        // Get kriteria
        $kriteriaModel = new KriteriaModel();
        $analisis['kriteria'] = $kriteriaModel->getAll($id);
        
        // Get nilai
        $nilaiData = $alternatifModel->getAllNilai($id);
        $nilaiOrganized = [];
        foreach ($nilaiData as $n) {
            $nilaiOrganized[$n['alternatif_id']][$n['kriteria_id']] = $n['nilai'];
        }
        $analisis['nilai'] = $nilaiOrganized;
        
        require_once __DIR__ . '/../views/input_kriteria.php';
    }
    
    public function view($id) {
        $analisis = $this->analisisModel->getById($id);
        if (!$analisis) {
            header('Location: ' . BASE_URL);
            exit;
        }
        require_once __DIR__ . '/../views/result.php';
    }
    
    public function delete($id) {
        header('Content-Type: application/json');
        if ($this->analisisModel->delete($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Gagal menghapus analisis']);
        }
        exit;
    }
}
?>
