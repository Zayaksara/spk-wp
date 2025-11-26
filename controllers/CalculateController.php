<?php
// Prevent any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/WpCalculation.php';

// Check action
$action = $_GET['action'] ?? '';

if ($action === 'calculate') {
    $controller = new CalculateController();
    $controller->calculate();
    exit;
} else if ($action === 'get' && isset($_GET['analisis_id'])) {
    $controller = new CalculateController();
    $controller->getResults(intval($_GET['analisis_id']));
    exit;
}

class CalculateController {
    private $wpCalculation;
    
    public function __construct() {
        $this->wpCalculation = new WpCalculation();
    }
    
    public function calculate() {
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
                exit;
            }
            
            $analisis_id = intval($data['analisis_id'] ?? 0);
            
            if (!$analisis_id) {
                echo json_encode(['success' => false, 'error' => 'Analisis ID tidak valid']);
                exit;
            }
            
            // Calculate WP
            $results = $this->wpCalculation->calculate($analisis_id);
            
            // Save results
            $this->wpCalculation->saveResults($analisis_id, $results);
            
            echo json_encode(['success' => true, 'results' => $results]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    public function getResults($analisis_id) {
        $results = $this->wpCalculation->getResults($analisis_id);
        echo json_encode($results);
    }
}
?>
