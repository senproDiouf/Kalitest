<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labs — CyberLab</title>
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
            <a href="/KaliTest/labs/" class="nav-link active">Labs</a>
            <a href="/KaliTest/tools/" class="nav-link">Outils</a>
        </div>
        <div class="nav-user">
            <?php if(isset($_SESSION['user'])): ?>
            <span class="user-badge">👤 <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
            <a href="/KaliTest/logout.php" class="btn-logout">Déco</a>
            <?php else: ?>
            <a href="/KaliTest/login.php" class="btn-login">Connexion</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="page-header">
            <p class="page-breadcrumb"><a href="/KaliTest/">Accueil</a> / Labs</p>
            <h1 class="page-title">⚡ MODULES DE FORMATION</h1>
            <p class="page-desc">Choisissez un lab pour commencer votre entraînement. Chaque module contient plusieurs niveaux de difficulté progressive et des explications détaillées.</p>
        </div>

        <!-- OWASP Top 10 Reference -->
        <div style="background:var(--bg-card); border:1px solid var(--border-dim); border-radius:10px; padding:1rem 1.5rem; margin-bottom:2rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap">
            <div style="font-family:var(--font-mono); font-size:.75rem; color:var(--neon-blue)">📋 OWASP TOP 10:</div>
            <?php
            $owasp = ['A01:Broken Access Control','A02:Crypto Failures','A03:Injection','A04:Insecure Design','A05:Misconfig','A06:Outdated Components','A07:Auth Failures','A08:Data Integrity','A09:Logging','A10:SSRF'];
            foreach ($owasp as $item): ?>
            <span style="font-family:var(--font-mono); font-size:.65rem; color:var(--text-secondary); background:rgba(255,255,255,.03); padding:.2rem .6rem; border-radius:3px; border:1px solid rgba(255,255,255,.05)"><?= $item ?></span>
            <?php endforeach; ?>
        </div>

        <div class="labs-grid">
            <?php
            $labs = [
                ['url'=>'/KaliTest/labs/sqli/', 'icon'=>'🗄️', 'title'=>'SQL Injection', 'diff'=>'INTERMÉDIAIRE', 'diff_class'=>'intermediate', 'owasp'=>'A03', 'desc'=>'Manipulez les requêtes SQL pour extraire des données et contourner l\'authentification.', 'tags'=>['Union-Based','Blind SQLi','Auth Bypass'], 'challenges'=>5],
                ['url'=>'/KaliTest/labs/xss/', 'icon'=>'💉', 'title'=>'Cross-Site Scripting', 'diff'=>'DÉBUTANT', 'diff_class'=>'beginner', 'owasp'=>'A03', 'desc'=>'Injectez du JavaScript malveillant pour voler des cookies et exécuter du code côté client.', 'tags'=>['Reflected','Stored','DOM-Based'], 'challenges'=>4],
                ['url'=>'/KaliTest/labs/csrf/', 'icon'=>'🔄', 'title'=>'CSRF', 'diff'=>'INTERMÉDIAIRE', 'diff_class'=>'intermediate', 'owasp'=>'A01', 'desc'=>'Forcez un utilisateur authentifié à effectuer des actions non désirées via des requêtes forgées.', 'tags'=>['Token Bypass','GET','POST'], 'challenges'=>3],
                ['url'=>'/KaliTest/labs/fileupload/', 'icon'=>'📁', 'title'=>'File Upload', 'diff'=>'AVANCÉ', 'diff_class'=>'advanced', 'owasp'=>'A04', 'desc'=>'Contournez les validations pour uploader un webshell et obtenir une exécution distante.', 'tags'=>['Extension Bypass','MIME','WebShell'], 'challenges'=>4],
                ['url'=>'/KaliTest/labs/cmdi/', 'icon'=>'💻', 'title'=>'Command Injection', 'diff'=>'AVANCÉ', 'diff_class'=>'advanced', 'owasp'=>'A03', 'desc'=>'Exécutez des commandes système via des champs d\'entrée non filtrés.', 'tags'=>['OS Command','Blind','RCE'], 'challenges'=>3],
                ['url'=>'/KaliTest/labs/idor/', 'icon'=>'🔑', 'title'=>'IDOR', 'diff'=>'DÉBUTANT', 'diff_class'=>'beginner', 'owasp'=>'A01', 'desc'=>'Accédez à des ressources non autorisées en manipulant des identifiants dans les paramètres.', 'tags'=>['URL Manipulation','ID Tampering','Data Exposure'], 'challenges'=>3],
                ['url'=>'/KaliTest/labs/lfi/', 'icon'=>'📂', 'title'=>'LFI / Path Traversal', 'diff'=>'AVANCÉ', 'diff_class'=>'advanced', 'owasp'=>'A05', 'desc'=>'Lisez des fichiers arbitraires sur le serveur via la traversée de répertoires.', 'tags'=>['../','PHP Wrappers','Log Poisoning'], 'challenges'=>3],
            ];
            foreach ($labs as $lab): ?>
            <div class="lab-card" onclick="window.location='<?= $lab['url'] ?>'">
                <div class="lab-card-glow"></div>
                <div class="lab-header">
                    <div class="lab-icon"><?= $lab['icon'] ?></div>
                    <div class="lab-meta">
                        <span class="lab-difficulty <?= $lab['diff_class'] ?>"><?= $lab['diff'] ?></span>
                        <span class="lab-owasp">OWASP <?= $lab['owasp'] ?></span>
                    </div>
                </div>
                <h3 class="lab-title"><?= $lab['title'] ?></h3>
                <p class="lab-desc"><?= $lab['desc'] ?></p>
                <div class="lab-tags">
                    <?php foreach ($lab['tags'] as $tag): ?>
                    <span class="tag"><?= $tag ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="lab-footer">
                    <span class="lab-challenges"><?= $lab['challenges'] ?> niveaux</span>
                    <button class="lab-btn">LANCER ▶</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Legend -->
        <div style="display:flex; gap:1rem; margin-top:2rem; flex-wrap:wrap">
            <div style="display:flex; align-items:center; gap:.5rem">
                <span class="lab-difficulty beginner">DÉBUTANT</span>
                <span style="font-family:var(--font-mono); font-size:.75rem; color:var(--text-secondary)">— Pas ou peu de connaissances requises</span>
            </div>
            <div style="display:flex; align-items:center; gap:.5rem">
                <span class="lab-difficulty intermediate">INTERMÉDIAIRE</span>
                <span style="font-family:var(--font-mono); font-size:.75rem; color:var(--text-secondary)">— Bases en web nécessaires</span>
            </div>
            <div style="display:flex; align-items:center; gap:.5rem">
                <span class="lab-difficulty advanced">AVANCÉ</span>
                <span style="font-family:var(--font-mono); font-size:.75rem; color:var(--text-secondary)">— Expérience en sécurité recommandée</span>
            </div>
        </div>
    </div>

    <script src="/KaliTest/assets/js/main.js"></script>
</body>
</html>
