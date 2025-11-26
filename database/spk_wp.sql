-- Database: spk_wp
-- Create database (jika belum ada)
CREATE DATABASE IF NOT EXISTS spk_wp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE spk_wp;

-- Table: analisis
CREATE TABLE IF NOT EXISTS analisis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    metode VARCHAR(50) NOT NULL DEFAULT 'WP',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: alternatif
CREATE TABLE IF NOT EXISTS alternatif (
    id INT AUTO_INCREMENT PRIMARY KEY,
    analisis_id INT NOT NULL,
    nama VARCHAR(255) NOT NULL,
    urutan INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (analisis_id) REFERENCES analisis(id) ON DELETE CASCADE,
    INDEX idx_analisis (analisis_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: kriteria
CREATE TABLE IF NOT EXISTS kriteria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    analisis_id INT NOT NULL,
    nama VARCHAR(255) NOT NULL,
    bobot DECIMAL(6,4) NOT NULL,
    tipe ENUM('benefit', 'cost') NOT NULL DEFAULT 'benefit',
    urutan INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (analisis_id) REFERENCES analisis(id) ON DELETE CASCADE,
    INDEX idx_analisis (analisis_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: nilai
CREATE TABLE IF NOT EXISTS nilai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    analisis_id INT NOT NULL,
    alternatif_id INT NOT NULL,
    kriteria_id INT NOT NULL,
    nilai DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (analisis_id) REFERENCES analisis(id) ON DELETE CASCADE,
    FOREIGN KEY (alternatif_id) REFERENCES alternatif(id) ON DELETE CASCADE,
    FOREIGN KEY (kriteria_id) REFERENCES kriteria(id) ON DELETE CASCADE,
    UNIQUE KEY unique_nilai (analisis_id, alternatif_id, kriteria_id),
    INDEX idx_analisis (analisis_id),
    INDEX idx_alternatif (alternatif_id),
    INDEX idx_kriteria (kriteria_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: hasil (untuk menyimpan hasil perhitungan)
CREATE TABLE IF NOT EXISTS hasil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    analisis_id INT NOT NULL,
    alternatif_id INT NOT NULL,
    nilai_wp DECIMAL(10,4) NOT NULL,
    ranking INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (analisis_id) REFERENCES analisis(id) ON DELETE CASCADE,
    FOREIGN KEY (alternatif_id) REFERENCES alternatif(id) ON DELETE CASCADE,
    UNIQUE KEY unique_hasil (analisis_id, alternatif_id),
    INDEX idx_analisis (analisis_id),
    INDEX idx_ranking (analisis_id, ranking)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

