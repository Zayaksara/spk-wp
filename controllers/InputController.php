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
        $judul = trim($data['judul'] ?? '');
        $metode = $data['metode'] ?? 'WP';
        
        // Validation
        if (empty($judul)) {
            echo json_encode(['success' => false, 'error' => 'Judul analisis tidak boleh kosong']);
            exit;
        }
        
        // Validate kriteria bobot
        if (isset($data['kriteria']) && is_array($data['kriteria'])) {
            $totalBobot = 0;
            foreach ($data['kriteria'] as $kri) {
                $bobot = floatval($kri['bobot'] ?? 0);
                if (!is_numeric($bobot) || $bobot < 0 || $bobot > 1) {
                    echo json_encode(['success' => false, 'error' => 'Bobot harus antara 0 dan 1']);
                    exit;
                }
                $totalBobot += $bobot;
            }
            if (abs($totalBobot - 1) > 0.01) {
                echo json_encode(['success' => false, 'error' => 'Total bobot harus sama dengan 1. Total saat ini: ' . number_format($totalBobot, 2)]);
                exit;
            }
        }
        
        // Start transaction
        require_once __DIR__ . '/../models/Database.php';
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        try {
            $conn->begin_transaction();
            
            if ($id) {
                $this->analisisModel->update($id, $judul, $metode);
                $this->kriteriaModel->deleteByAnalisis($id);
                $this->alternatifModel->deleteByAnalisis($id);
                $analisis_id = $id;
            } else {
                $analisis_id = $this->analisisModel->create($judul, $metode);
                if (!$analisis_id) {
                    throw new Exception('Gagal membuat analisis');
                }
            }
            
            // Save alternatif
            if (isset($data['alternatif']) && is_array($data['alternatif'])) {
                foreach ($data['alternatif'] as $index => $alt) {
                    $altName = trim($alt);
                    if (empty($altName)) {
                        throw new Exception('Nama alternatif tidak boleh kosong');
                    }
                    if (!$this->alternatifModel->create($analisis_id, $altName, $index + 1)) {
                        throw new Exception('Gagal menyimpan alternatif');
                    }
                }
            }
            
            // Save kriteria
            if (isset($data['kriteria']) && is_array($data['kriteria'])) {
                foreach ($data['kriteria'] as $index => $kri) {
                    $kriName = trim($kri['nama'] ?? '');
                    if (empty($kriName)) {
                        throw new Exception('Nama kriteria tidak boleh kosong');
                    }
                    $bobot = floatval($kri['bobot'] ?? 0);
                    if (!$this->kriteriaModel->create($analisis_id, $kriName, $bobot, $kri['tipe'] ?? 'benefit', $index + 1)) {
                        throw new Exception('Gagal menyimpan kriteria');
                    }
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
                                $nilaiValue = floatval($nilaiValue);
                                if ($nilaiValue < 0) {
                                    throw new Exception('Nilai tidak boleh negatif');
                                }
                                if (!$this->alternatifModel->setNilai($analisis_id, $altId, $kriId, $nilaiValue)) {
                                    throw new Exception('Gagal menyimpan nilai');
                                }
                            }
                        }
                    }
                }
            }
            
            $conn->commit();
            echo json_encode(['success' => true, 'id' => $analisis_id]);
            
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
?>
