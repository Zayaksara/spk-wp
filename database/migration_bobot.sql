-- Migration: Update bobot column from DECIMAL(5,2) to DECIMAL(6,4)
-- Run this if you already have existing database

USE spk_wp;

ALTER TABLE kriteria MODIFY bobot DECIMAL(6,4) NOT NULL;








