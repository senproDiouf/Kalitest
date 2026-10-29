<?php
// ============================================================
// CyberLab - Script d'installation automatique de la base de données
// Accéder à : http://localhost/KaliTest/install.php
// ============================================================

$host = 'localhost';
$user = 'root';
$pass = '';

$steps = [];
$success = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['auto'])) {
    try {
        // Connexion sans base de données
        $conn = new mysqli($host, $user, $pass);
        if ($conn->connect_error) {
            throw new Exception("Connexion MySQL impossible: " . $conn->connect_error . " — Assurez-vous que Laragon est démarré !");
        }
        $steps[] = ['✅', 'Connexion MySQL établie', 'success'];

        // Création de la base de données
        $conn->query("CREATE DATABASE IF NOT EXISTS cyberlab CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        $steps[] = ['✅', 'Base de données "cyberlab" créée', 'success'];

        // Sélection de la DB
        $conn->select_db('cyberlab');

        // Table users
        $conn->query("DROP TABLE IF EXISTS users");
        $conn->query("CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            role VARCHAR(20) DEFAULT 'user',
            secret_token VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $steps[] = ['✅', 'Table "users" créée', 'success'];

        // Données users
        $conn->query("INSERT INTO users (username, password, email, role, secret_token) VALUES
            ('admin', 'admin123', 'admin@cyberlab.local', 'admin', 'FLAG{admin_secret_token_12345}'),
            ('alice', 'password', 'alice@cyberlab.local', 'user', 'FLAG{alice_private_data_67890}'),
            ('bob', 'bob123', 'bob@cyberlab.local', 'user', 'FLAG{bob_hidden_flag_abcde}'),
            ('hacker', 'letmein', 'hacker@cyberlab.local', 'user', 'FLAG{ethical_hacker_token_xyz}'),
            ('guest', 'guest', 'guest@cyberlab.local', 'guest', 'FLAG{guest_flag_test_111}')");
        $steps[] = ['✅', '5 utilisateurs de test insérés (admin, alice, bob, hacker, guest)', 'success'];

        // Table products
        $conn->query("DROP TABLE IF EXISTS products");
        $conn->query("CREATE TABLE products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10,2),
            category VARCHAR(50),
            secret_field VARCHAR(200) DEFAULT 'HIDDEN_DATA'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $conn->query("INSERT INTO products (name, description, price, category, secret_field) VALUES
            ('Kali Linux USB', 'Clé USB bootable Kali Linux 2024', 29.99, 'outils', 'SECRET_PRODUCT_KEY_001'),
            ('Metasploit Pro', 'Framework de pentesting professionnel', 299.99, 'logiciels', 'SECRET_PRODUCT_KEY_002'),
            ('Burp Suite Pro', 'Proxy interception pour tests web', 399.99, 'logiciels', 'SECRET_PRODUCT_KEY_003'),
            ('Raspberry Pi 4', 'Mini-ordinateur pour projets sécurité', 79.99, 'hardware', 'SECRET_PRODUCT_KEY_004'),
            ('WiFi Pineapple', 'Dispositif audit réseau sans-fil', 199.99, 'hardware', 'SECRET_PRODUCT_KEY_005')");
        $steps[] = ['✅', 'Table "products" créée avec 5 produits', 'success'];

        // Table messages
        $conn->query("DROP TABLE IF EXISTS messages");
        $conn->query("CREATE TABLE messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50),
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $conn->query("INSERT INTO messages (username, message) VALUES
            ('admin', 'Bienvenue sur CyberLab ! Explorez les labs avec précaution.'),
            ('alice', 'J\'ai trouvé une super ressource sur les injections SQL !'),
            ('bob', 'Le lab XSS est vraiment bien fait pour apprendre.')");
        $steps[] = ['✅', 'Table "messages" créée avec 3 messages', 'success'];

        // Table uploads
        $conn->query("DROP TABLE IF EXISTS uploads");
        $conn->query("CREATE TABLE uploads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255),
            original_name VARCHAR(255),
            uploader VARCHAR(50),
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $steps[] = ['✅', 'Table "uploads" créée', 'success'];

        // Table notes (IDOR)
        $conn->query("DROP TABLE IF EXISTS notes");
        $conn->query("CREATE TABLE notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            title VARCHAR(200),
            content TEXT,
            is_private TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $conn->query("INSERT INTO notes (user_id, title, content, is_private) VALUES
            (1, 'Clé API Admin', 'ADMIN_API_KEY=sk-admin-super-secret-12345abcdef', 1),
            (1, 'Credentials serveur', 'SSH: root@192.168.1.100 / password: toor', 1),
            (2, 'Note d''Alice', 'FLAG{idor_alice_private_note_found}', 1),
            (2, 'Liste courses', 'Acheter du café et des chips', 0),
            (3, 'TODO Bob', 'FLAG{idor_bob_secret_note_captured}', 1),
            (4, 'Note publique', 'Bienvenue sur le lab IDOR ! Pouvez-vous accéder aux notes privées ?', 0)");
        $steps[] = ['✅', 'Table "notes" créée avec 6 notes (dont des notes privées)', 'success'];

        // Table attack_logs
        $conn->query("DROP TABLE IF EXISTS attack_logs");
        $conn->query("CREATE TABLE attack_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45),
            attack_type VARCHAR(50),
            payload TEXT,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $steps[] = ['✅', 'Table "attack_logs" créée', 'success'];

        $conn->close();
        $steps[] = ['🎉', 'Installation terminée ! La base de données CyberLab est prête.', 'success'];

        // Créer le dossier uploads
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $steps[] = ['✅', 'Dossier uploads/ créé', 'success'];

        // Créer le dossier LFI pages
        $lfi_dir = __DIR__ . '/labs/lfi/pages/';
        if (!is_dir($lfi_dir)) mkdir($lfi_dir, 0777, true);
        file_put_contents($lfi_dir . 'home.txt', "Bienvenue sur CyberLab !\nPage d'accueil du système LFI Lab.");
        file_put_contents($lfi_dir . 'about.txt', "CyberLab v1.0 - LFI Lab\nPlateforme de pentesting éducatif.");
        file_put_contents($lfi_dir . 'secret.txt', "FLAG{lfi_secret_page_found_congrats}\nAPI_KEY=lfi_secret_api_key_12345\nDATABASE_PASS=super_secret_db_pass");
        $steps[] = ['✅', 'Fichiers LFI de test créés (home.txt, about.txt, secret.txt)', 'success'];

    } catch (Exception $e) {
        $steps[] = ['❌', 'ERREUR: ' . $e->getMessage(), 'error'];
        $success = false;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation — CyberLab</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/KaliTest/assets/css/style.css">
</head>
<body>
    <canvas id="matrix-canvas"></canvas>

    <div class="auth-wrapper">
        <div class="auth-card" style="max-width:600px">
            <div class="auth-header">
                <div class="auth-icon">⚙️</div>
                <h1 class="auth-title">INSTALLATION CYBERLAB</h1>
                <p class="auth-subtitle">Configuration de la base de données</p>
            </div>

            <?php if (empty($steps)): ?>
            <div class="alert alert-warning" style="margin-bottom:1.5rem">
                ⚠️ Assurez-vous que <strong>Laragon est démarré</strong> (MySQL doit être actif) avant de lancer l'installation.
            </div>

            <div style="margin-bottom:1.5rem; padding:1rem; background:rgba(0,0,0,.2); border-radius:6px; font-family:var(--font-mono); font-size:.75rem; color:var(--text-secondary); line-height:1.8">
                <p style="color:var(--neon-blue); margin-bottom:.5rem">// Ce script va créer :</p>
                <p>• Base de données : <span style="color:var(--neon-green)">cyberlab</span></p>
                <p>• Tables : users, products, messages, notes, uploads, attack_logs</p>
                <p>• Données de test avec utilisateurs et FLAGS</p>
                <p>• Dossiers nécessaires (uploads/, labs/lfi/pages/)</p>
            </div>

            <form method="POST">
                <button type="submit" class="btn-submit" style="width:100%">
                    ⚡ LANCER L'INSTALLATION
                </button>
            </form>

            <?php else: ?>

            <div style="margin-bottom:1.5rem">
                <?php foreach ($steps as [$icon, $msg, $type]): ?>
                <div style="display:flex; align-items:flex-start; gap:.6rem; padding:.4rem .6rem; margin-bottom:.3rem; background:rgba(<?= $type === 'success' ? '0,255,136' : '255,51,102' ?>,.05); border-radius:4px; border-left:2px solid var(--neon-<?= $type === 'success' ? 'green' : 'red' ?>)">
                    <span style="font-size:.9rem"><?= $icon ?></span>
                    <span style="font-family:var(--font-mono); font-size:.78rem; color:var(--<?= $type === 'success' ? 'text-primary' : 'neon-red' ?>)"><?= htmlspecialchars($msg) ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success" style="margin-bottom:1.5rem">
                🎉 <strong>Installation réussie !</strong> CyberLab est prêt à l'emploi.
            </div>
            <a href="/KaliTest/" class="btn-submit" style="display:block; text-align:center; text-decoration:none; margin-bottom:.8rem">
                🚀 ACCÉDER À CYBERLAB
            </a>
            <a href="/KaliTest/login.php" class="btn-submit" style="display:block; text-align:center; text-decoration:none; background:linear-gradient(135deg,var(--neon-blue),#0099bb)">
                🔓 SE CONNECTER (admin/admin123)
            </a>
            <?php else: ?>
            <div class="alert alert-danger" style="margin-bottom:1.5rem">
                ❌ Installation échouée. Vérifiez que <strong>Laragon est démarré</strong> et que MySQL est actif.
            </div>
            <form method="POST">
                <button type="submit" class="btn-submit" style="width:100%; background:linear-gradient(135deg,#ff8c00,#cc6600)">
                    🔄 RÉESSAYER
                </button>
            </form>
            <?php endif; ?>

            <?php endif; ?>

            <div style="margin-top:1.5rem; padding:.8rem; background:rgba(0,0,0,.2); border-radius:6px; font-family:var(--font-mono); font-size:.7rem; color:var(--text-secondary)">
                <p style="color:var(--neon-yellow); margin-bottom:.3rem">⚠️ SUPPRIMER CE FICHIER EN PRODUCTION</p>
                <p>Ce fichier install.php donne accès à votre base de données. Supprimez-le après installation si vous exposez ce site.</p>
            </div>
        </div>
    </div>

    <script src="/KaliTest/assets/js/main.js"></script>
</body>
</html>
