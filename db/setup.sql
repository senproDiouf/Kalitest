-- ============================================================
-- CyberLab - Base de données vulnérable pour tests d'intrusion
-- ============================================================

CREATE DATABASE IF NOT EXISTS cyberlab CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE cyberlab;

-- Table utilisateurs (intentionnellement vulnérable)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user',
    secret_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Utilisateurs de test (mots de passe en clair et hashés MD5 - vulnérables intentionnellement)
INSERT INTO users (username, password, email, role, secret_token) VALUES
('admin', 'admin123', 'admin@cyberlab.local', 'admin', 'FLAG{admin_secret_token_12345}'),
('alice', 'password', 'alice@cyberlab.local', 'user', 'FLAG{alice_private_data_67890}'),
('bob', 'bob123', 'bob@cyberlab.local', 'user', 'FLAG{bob_hidden_flag_abcde}'),
('hacker', 'letmein', 'hacker@cyberlab.local', 'user', 'FLAG{ethical_hacker_token_xyz}'),
('guest', 'guest', 'guest@cyberlab.local', 'guest', 'FLAG{guest_flag_test_111}');

-- Table produits (pour les labs SQL Injection)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    category VARCHAR(50),
    secret_field VARCHAR(200) DEFAULT 'HIDDEN_DATA'
);

INSERT INTO products (name, description, price, category, secret_field) VALUES
('Kali Linux USB', 'Clé USB bootable Kali Linux 2024', 29.99, 'outils', 'SECRET_PRODUCT_KEY_001'),
('Metasploit Pro', 'Framework de pentesting professionnel', 299.99, 'logiciels', 'SECRET_PRODUCT_KEY_002'),
('Burp Suite Pro', 'Proxy d\'interception pour tests web', 399.99, 'logiciels', 'SECRET_PRODUCT_KEY_003'),
('Raspberry Pi 4', 'Mini-ordinateur pour projets sécurité', 79.99, 'hardware', 'SECRET_PRODUCT_KEY_004'),
('WiFi Pineapple', 'Dispositif d\'audit réseau sans-fil', 199.99, 'hardware', 'SECRET_PRODUCT_KEY_005');

-- Table messages (pour XSS)
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO messages (username, message) VALUES
('admin', 'Bienvenue sur CyberLab ! Explorez les labs avec précaution.'),
('alice', 'J\'ai trouvé une super ressource sur les injections SQL !'),
('bob', 'Le lab XSS est vraiment bien fait pour apprendre.');

-- Table fichiers uploadés (pour lab File Upload)
CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255),
    original_name VARCHAR(255),
    uploader VARCHAR(50),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table notes IDOR (pour lab Insecure Direct Object Reference)
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(200),
    content TEXT,
    is_private TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO notes (user_id, title, content, is_private) VALUES
(1, 'Clé API Admin', 'ADMIN_API_KEY=sk-admin-super-secret-12345abcdef', 1),
(1, 'Credentials serveur', 'SSH: root@192.168.1.100 / password: toor', 1),
(2, 'Note d\'Alice', 'FLAG{idor_alice_private_note_found}', 1),
(2, 'Liste courses', 'Acheter du café et des chips', 0),
(3, 'TODO Bob', 'FLAG{idor_bob_secret_note_captured}', 1),
(4, 'Note publique', 'Bienvenue sur le lab IDOR ! Pouvez-vous accéder aux notes privées ?', 0);

-- Table logs (pour monitoring)
CREATE TABLE IF NOT EXISTS attack_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45),
    attack_type VARCHAR(50),
    payload TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
