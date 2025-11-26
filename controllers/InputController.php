<?php
// Prevent any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AnalisisModel.php';
require_once __DIR__ . '/../models/KriteriaModel.php';
require_once __DIR__ . '/../models/AlternatifModel.php';

// Check if action is set
$action = $_GET['action'] ?? '';

if ($action === 'save') {
    $controller = new InputController();
    $controller->saveAnalisis();
    exit;
}

class InputController {
    private $analisisModel;
    private $kriteriaModel;
    private $alternatifModel;
    
    public function __construct() {
        $this->analisisModel = new AnalisisModel();
        $this->kriteriaModel = new KriteriaModel();
        $this->alternatifModel = new AlternatifModel();
    }
    
    public function saveAnalisis() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
            exit;
        }
        
        $id = isset($data['id']) ? intval($data['id']) : null;
        $judul = $data['judul'] ?? '';
        $metode = $data['metode'] ?? 'WP';
        
        if (empty($judul)) {
            echo json_encode(['success' => false, 'error' => 'Judul analisis tidak boleh kosong']);
            exit;
        }
        
        if ($id) {
            // Update
            $this->analisisModel->update($id, $judul, $metode);
            // Delete existing data
            $this->kriteriaModel->deleteByAnalisis($id);
            $this->alternatifModel->deleteByAnalisis($id);
            $analisis_id = $id;
        } else {
            // Create
            $analisis_id = $this->analisisModel->create($judul, $metode);
            if (!$analisis_id) {
                echo json_encode(['success' => false, 'error' => 'Gagal membuat analisis']);
                exit;
            }
        }
        
        // Save alternatif
        if (isset($data['alternatif']) && is_array($data['alternatif'])) {
            foreach ($data['alternatif'] as $index => $alt) {
                $this->alternatifModel->create($analisis_id, $alt, $index + 1);
            }
        }
        
        // Save kriteria
        if (isset($data['kriteria']) && is_array($data['kriteria'])) {
            foreach ($data['kriteria'] as $index => $kri) {
                $this->kriteriaModel->create($analisis_id, $kri['nama'], $kri['bobot'], $kri['tipe'], $index + 1);
            }
        }
        
        // Get alternatif and kriteria IDs
        $alternatif = $this->alternatifModel->getAll($analisis_id);
        $kriteria = $this->kriteriaModel->getAll($analisis_id);
        
        // Save nilai
        if (isset($data['nilai']) && is_array($data['nilai'])) {
            foreach ($data['nilai'] as $altIndex => $kriteriaNilai) {
                $altId = isset($alternatif[$altIndex]['id']) ? $alternatif[$altIndex]['id'] : null;
                if ($altId) {
                    foreach ($kriteriaNilai as $kriIndex => $nilaiValue) {
                        $kriId = isset($kriteria[$kriIndex]['id']) ? $kriteria[$kriIndex]['id'] : null;
                        if ($kriId) {
                            $this->alternatifModel->setNilai($analisis_id, $altId, $kriId, $nilaiValue);
                        }
                    }
                }
            }
        }
        
        echo json_encode(['success' => true, 'id' => $analisis_id]);
    }
}
?>
