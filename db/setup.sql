CREATE DATABASE IF NOT EXISTS meditrust_db;
USE meditrust_db;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50),
    password VARCHAR(255), -- Celah: Menggunakan MD5 (Broken Auth)
    role ENUM('admin', 'dokter')
);

CREATE TABLE patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    nik VARCHAR(20),      -- Celah: Plaintext (Sensitive Data Exposure)
    diagnosis TEXT,       -- Celah: Potensi Stored XSS
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed data untuk testing mahasiswa
INSERT INTO users (username, password, role) VALUES ('dr_ade', md5('password123'), 'dokter');
INSERT INTO patients (name, nik, diagnosis) VALUES ('Budi Santoso', '3515010101010001', 'Flu Burung');