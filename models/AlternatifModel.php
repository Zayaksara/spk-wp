<?php
require_once __DIR__ . '/Database.php';

class AlternatifModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($analisis_id) {
        $stmt = $this->db->prepare("SELECT * FROM alternatif WHERE analisis_id = ? ORDER BY urutan");
        $stmt->bind_param("i", $analisis_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM alternatif WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function create($analisis_id, $nama, $urutan) {
        $stmt = $this->db->prepare("INSERT INTO alternatif (analisis_id, nama, urutan) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $analisis_id, $nama, $urutan);
        return $stmt->execute();
    }
    
    public function update($id, $nama) {
        $stmt = $this->db->prepare("UPDATE alternatif SET nama = ? WHERE id = ?");
        $stmt->bind_param("si", $nama, $id);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM alternatif WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function deleteByAnalisis($analisis_id) {
        $stmt = $this->db->prepare("DELETE FROM alternatif WHERE analisis_id = ?");
        $stmt->bind_param("i", $analisis_id);
        return $stmt->execute();
    }
    
    public function getNilai($analisis_id, $alternatif_id, $kriteria_id) {
        $stmt = $this->db->prepare("SELECT nilai FROM nilai WHERE analisis_id = ? AND alternatif_id = ? AND kriteria_id = ?");
        $stmt->bind_param("iii", $analisis_id, $alternatif_id, $kriteria_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['nilai'] : null;
    }
    
    public function setNilai($analisis_id, $alternatif_id, $kriteria_id, $nilai) {
        $stmt = $this->db->prepare("INSERT INTO nilai (analisis_id, alternatif_id, kriteria_id, nilai) VALUES (?, ?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE nilai = ?");
        $stmt->bind_param("iiidd", $analisis_id, $alternatif_id, $kriteria_id, $nilai, $nilai);
        return $stmt->execute();
    }
    
    public function getAllNilai($analisis_id) {
        $stmt = $this->db->prepare("
            SELECT n.*, a.nama as alternatif_nama, k.nama as kriteria_nama 
            FROM nilai n
            JOIN alternatif a ON n.alternatif_id = a.id
            JOIN kriteria k ON n.kriteria_id = k.id
            WHERE n.analisis_id = ?
        ");
        $stmt->bind_param("i", $analisis_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

