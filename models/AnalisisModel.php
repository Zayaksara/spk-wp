<?php
require_once __DIR__ . '/Database.php';

class AnalisisModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        $result = $this->db->query("SELECT * FROM analisis ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM analisis WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function create($judul, $metode = 'WP') {
        $stmt = $this->db->prepare("INSERT INTO analisis (judul, metode) VALUES (?, ?)");
        $stmt->bind_param("ss", $judul, $metode);
        if ($stmt->execute()) {
            return $this->db->getLastInsertId();
        }
        return false;
    }
    
    public function update($id, $judul, $metode) {
        $stmt = $this->db->prepare("UPDATE analisis SET judul = ?, metode = ? WHERE id = ?");
        $stmt->bind_param("ssi", $judul, $metode, $id);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM analisis WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>










