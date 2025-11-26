<?php
require_once __DIR__ . '/Database.php';

class KriteriaModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($analisis_id) {
        $stmt = $this->db->prepare("SELECT * FROM kriteria WHERE analisis_id = ? ORDER BY urutan");
        $stmt->bind_param("i", $analisis_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM kriteria WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function create($analisis_id, $nama, $bobot, $tipe, $urutan) {
        $stmt = $this->db->prepare("INSERT INTO kriteria (analisis_id, nama, bobot, tipe, urutan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdss", $analisis_id, $nama, $bobot, $tipe, $urutan);
        return $stmt->execute();
    }
    
    public function update($id, $nama, $bobot, $tipe) {
        $stmt = $this->db->prepare("UPDATE kriteria SET nama = ?, bobot = ?, tipe = ? WHERE id = ?");
        $stmt->bind_param("sdss", $nama, $bobot, $tipe, $id);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM kriteria WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function deleteByAnalisis($analisis_id) {
        $stmt = $this->db->prepare("DELETE FROM kriteria WHERE analisis_id = ?");
        $stmt->bind_param("i", $analisis_id);
        return $stmt->execute();
    }
}
?>



