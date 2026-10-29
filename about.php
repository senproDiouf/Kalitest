<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos — CyberLab</title>
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
            <a href="/KaliTest/tools/" class="nav-link">Outils</a>
            <a href="/KaliTest/about.php" class="nav-link active">À propos</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="page-header">
            <p class="page-breadcrumb"><a href="/KaliTest/">Accueil</a> / À Propos</p>
            <h1 class="page-title">ℹ️ À PROPOS DE CYBERLAB</h1>
        </div>

        <div style="max-width:900px">
            <div class="lab-panel" style="margin-bottom:1.5rem">
                <div class="panel-header">🎯 QU'EST-CE QUE CYBERLAB ?</div>
                <div class="panel-body">
                    <p style="color:var(--text-secondary); line-height:1.8; margin-bottom:1rem">
                        CyberLab est une plateforme d'apprentissage de la cybersécurité conçue pour pratiquer les
                        techniques de tests d'intrusion dans un environnement <strong style="color:var(--neon-green)">légal, éthique et isolé</strong>.
                    </p>
                    <p style="color:var(--text-secondary); line-height:1.8; margin-bottom:1rem">
                        Inspirée de DVWA (Damn Vulnerable Web Application) et de WebGoat, cette plateforme contient
                        des vulnérabilités <strong>intentionnelles</strong> couvrant les principales catégories de l'OWASP Top 10.
                    </p>
                    <div class="alert alert-warning">
                        ⚠️ <strong>Avertissement légal :</strong> Cette application est volontairement vulnérable.
                        Ne l'exposez jamais sur Internet sans protection appropriée. Utilisez-la uniquement sur votre réseau local
                        à des fins d'apprentissage éthique.
                    </div>
                </div>
            </div>

            <div class="lab-panel" style="margin-bottom:1.5rem">
                <div class="panel-header">📋 VULNÉRABILITÉS COUVERTES</div>
                <div class="panel-body">
                    <table class="data-table">
                        <tr><th>Lab</th><th>Type</th><th>OWASP</th><th>Niveaux</th><th>Technique</th></tr>
                        <tr><td>🗄️ SQL Injection</td><td>Injection</td><td>A03</td><td>3</td><td>Union, Blind, Auth Bypass</td></tr>
                        <tr><td>💉 XSS</td><td>Injection</td><td>A03</td><td>3</td><td>Reflected, Stored, DOM</td></tr>
                        <tr><td>🔄 CSRF</td><td>Broken Access</td><td>A01</td><td>3</td><td>GET, POST, Token Bypass</td></tr>
                        <tr><td>📁 File Upload</td><td>Insecure Design</td><td>A04</td><td>3</td><td>Ext, MIME, WebShell</td></tr>
                        <tr><td>💻 Cmd Injection</td><td>Injection</td><td>A03</td><td>3</td><td>RCE, Bypass, Blind</td></tr>
                        <tr><td>🔑 IDOR</td><td>Broken Access</td><td>A01</td><td>3</td><td>URL, API, Hidden Field</td></tr>
                        <tr><td>📂 LFI</td><td>Misconfig</td><td>A05</td><td>3</td><td>Traversal, Bypass, Wrappers</td></tr>
                    </table>
                </div>
            </div>

            <div class="lab-panel" style="margin-bottom:1.5rem">
                <div class="panel-header">🛠️ STACK TECHNIQUE</div>
                <div class="panel-body">
                    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:1rem">
                        <?php
                        $tech = [
                            ['PHP 8+', '🐘', 'Backend'],
                            ['MySQL/MariaDB', '🗄️', 'Base de données'],
                            ['Apache', '🌐', 'Serveur web'],
                            ['Laragon', '🦅', 'Environnement local'],
                            ['HTML/CSS/JS', '💻', 'Frontend'],
                            ['Vanilla CSS', '🎨', 'Styles cyberpunk'],
                        ];
                        foreach ($tech as [$name, $icon, $role]): ?>
                        <div style="background:rgba(255,255,255,.02); border:1px solid var(--border-dim); border-radius:8px; padding:1rem; text-align:center">
                            <div style="font-size:1.5rem; margin-bottom:.3rem"><?= $icon ?></div>
                            <div style="font-family:var(--font-title); font-size:.85rem; margin-bottom:.2rem"><?= $name ?></div>
                            <div style="font-family:var(--font-mono); font-size:.65rem; color:var(--text-secondary)"><?= $role ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="lab-panel">
                <div class="panel-header">📚 RESSOURCES D'APPRENTISSAGE</div>
                <div class="panel-body">
                    <div style="display:grid; gap:.6rem">
                        <?php
                        $resources = [
                            ['OWASP Top 10', 'https://owasp.org/Top10/', 'Guide officiel des vulnérabilités web les plus critiques'],
                            ['PortSwigger Web Academy', 'https://portswigger.net/web-security', 'Labs interactifs de Burp Suite (gratuit)'],
                            ['HackTheBox', 'https://hackthebox.com', 'Plateforme CTF avancée'],
                            ['TryHackMe', 'https://tryhackme.com', 'Parcours guidés pour débutants'],
                            ['Exploit-DB', 'https://www.exploit-db.com', 'Base de données d\'exploits'],
                            ['PayloadsAllTheThings', 'https://github.com/swisskyrepo/PayloadsAllTheThings', 'Payloads pour tous types de vulnérabilités'],
                        ];
                        foreach ($resources as [$name, $url, $desc]): ?>
                        <div style="display:flex; align-items:center; justify-content:space-between; padding:.8rem 1rem; background:rgba(255,255,255,.02); border:1px solid var(--border-dim); border-radius:6px">
                            <div>
                                <div style="font-family:var(--font-mono); font-size:.85rem; color:var(--neon-green); margin-bottom:.2rem"><?= $name ?></div>
                                <div style="font-size:.78rem; color:var(--text-secondary)"><?= $desc ?></div>
                            </div>
                            <a href="<?= $url ?>" target="_blank" style="font-family:var(--font-mono); font-size:.75rem; color:var(--neon-blue); text-decoration:none; white-space:nowrap; margin-left:1rem">🔗 Visiter</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-brand">CYBER<span class="accent">LAB</span></div>
        <p class="footer-text">Plateforme de pentesting pour l'apprentissage éthique</p>
        <p class="footer-copy">© 2026 CyberLab — Use Responsibly</p>
    </footer>

    <script src="/KaliTest/assets/js/main.js"></script>
</body>
</html>
