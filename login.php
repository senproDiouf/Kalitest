<?php
require_once __DIR__ . '/../config/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // ⚠️ VULNÉRABILITÉ INTENTIONNELLE : SQL Injection dans l'authentification
    // Payload de bypass : admin' -- ou ' OR '1'='1
    $conn = getVulnDB();
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";

    // Log the query for educational purposes
    $logged_query = htmlspecialchars($query);

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user;
        $_SESSION['logged_in'] = true;
        header('Location: /KaliTest/dashboard.php');
        exit;
    } else {
        $error = 'Identifiants incorrects.';
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — CyberLab</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/KaliTest/assets/css/style.css">
</head>
<body>
    <canvas id="matrix-canvas"></canvas>

    <nav class="navbar">
        <div class="nav-brand">
            <div class="brand-icon">⬡</div>
            <span class="brand-name">CYBER<span class="accent">LAB</span></span>
        </div>
        <div class="nav-status" style="margin-right:auto">
            <div class="status-dot"></div>
            <span class="status-text" id="nav-time"></span>
        </div>
        <div class="nav-links">
            <a href="/KaliTest/" class="nav-link">Accueil</a>
            <a href="/KaliTest/labs/" class="nav-link">Labs</a>
        </div>
    </nav>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">🔓</div>
                <h1 class="auth-title">AUTHENTIFICATION</h1>
                <p class="auth-subtitle">Connexion au système CyberLab</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-danger">⚠ <?= $error ?></div>
            <?php endif; ?>

            <form method="POST" id="login-form">
                <div class="form-group">
                    <label class="form-label" for="username">IDENTIFIANT</label>
                    <input type="text" id="username" name="username" class="form-input"
                           placeholder="ex: admin" autocomplete="off" required
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">MOT DE PASSE</label>
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="mot de passe" required>
                </div>
                <button type="submit" class="btn-submit" style="width:100%">
                    ⚡ CONNEXION
                </button>
            </form>

            <?php if (!empty($logged_query ?? '')): ?>
            <div style="margin-top:1.5rem; padding:1rem; background:rgba(0,0,0,.3); border:1px solid rgba(255,51,102,.2); border-radius:6px;">
                <p style="font-family:monospace; font-size:.7rem; color:#ff3366; margin-bottom:.4rem;">// REQUÊTE SQL EXÉCUTÉE (mode éducatif) :</p>
                <code style="font-family:monospace; font-size:.75rem; color:#ffd700; word-break:break-all;"><?= $logged_query ?></code>
            </div>
            <?php endif; ?>

            <!-- Hint Box -->
            <div style="margin-top:1.5rem; padding:1rem; background:rgba(0,212,255,.04); border:1px solid rgba(0,212,255,.1); border-radius:6px;">
                <p style="font-family:monospace; font-size:.7rem; color:#00d4ff; margin-bottom:.6rem;">// INDICES DE TEST</p>
                <p style="font-family:monospace; font-size:.72rem; color:#666; margin-bottom:.3rem;">Comptes valides:</p>
                <code class="hint-code" title="Copier" onclick="document.getElementById('username').value='admin'; document.getElementById('password').value='admin123'">admin / admin123</code>
                <p style="font-family:monospace; font-size:.72rem; color:#ff8c00; margin-top:.6rem; margin-bottom:.3rem;">SQLi Auth Bypass:</p>
                <code class="hint-code" title="Copier payload SQLi" onclick="document.getElementById('username').value=`admin' -- `; document.getElementById('password').value='anything'">admin' -- </code>
                <code class="hint-code" onclick="document.getElementById('username').value=`' OR '1'='1`; document.getElementById('password').value=`' OR '1'='1`">' OR '1'='1</code>
            </div>
        </div>
    </div>

    <script src="/KaliTest/assets/js/main.js"></script>
</body>
</html>
