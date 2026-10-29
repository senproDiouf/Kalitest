<?php
// ============================================================
// CyberLab - Configuration de la base de données
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cyberlab');

// Connexion PDO (pour certains labs sécurisés)
function getDB() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('<div class="error-box">Erreur DB: ' . $e->getMessage() . '</div>');
    }
}

// Connexion mysqli (pour les labs vulnérables - SQL Injection)
function getVulnDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Erreur de connexion: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Démarrage de session sécurisé
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('SITE_NAME', 'CyberLab');
define('SITE_VERSION', '1.0.0');
define('BASE_URL', '/KaliTest/');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
