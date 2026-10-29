<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberLab - Plateforme de Pentesting</title>
    <meta name="description" content="Plateforme d'apprentissage en cybersécurité avec des labs de tests d'intrusion">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/KaliTest/assets/css/style.css">
</head>
<body>
    <!-- Matrix rain background -->
    <canvas id="matrix-canvas"></canvas>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-brand">
            <div class="brand-icon">⬡</div>
            <span class="brand-name">CYBER<span class="accent">LAB</span></span>
            <span class="version-tag">v1.0</span>
        </div>
        <div class="nav-status">
            <div class="status-dot"></div>
            <span class="status-text" id="nav-time"></span>
        </div>
        <div class="nav-links">
            <a href="/KaliTest/" class="nav-link active">Dashboard</a>
            <a href="/KaliTest/labs/" class="nav-link">Labs</a>
            <a href="/KaliTest/tools/" class="nav-link">Outils</a>
            <a href="/KaliTest/about.php" class="nav-link">À propos</a>
        </div>
        <div class="nav-user">
            <?php
            require_once __DIR__ . '/config/config.php';
            if(isset($_SESSION['user'])):
            ?>
            <span class="user-badge">👤 <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
            <a href="/KaliTest/logout.php" class="btn-logout">Déco</a>
            <?php else: ?>
            <a href="/KaliTest/login.php" class="btn-login">Connexion</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="badge-dot"></span>
                ENVIRONNEMENT D'ENTRAÎNEMENT ACTIF
            </div>
            <h1 class="hero-title">
                <span class="glitch" data-text="CyberLab">CyberLab</span>
            </h1>
            <p class="hero-subtitle">Maîtrisez l'art du <span class="highlight">test d'intrusion</span> à travers des labs interactifs et des vulnérabilités réelles</p>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-num">07</span>
                    <span class="stat-label">Labs Actifs</span>
                </div>
                <div class="stat-sep">|</div>
                <div class="stat">
                    <span class="stat-num">25+</span>
                    <span class="stat-label">Vulnérabilités</span>
                </div>
                <div class="stat-sep">|</div>
                <div class="stat">
                    <span class="stat-num">∞</span>
                    <span class="stat-label">Apprentissage</span>
                </div>
            </div>
            <div class="hero-cta">
                <a href="/KaliTest/labs/" class="btn-primary">
                    <span class="btn-icon">⚡</span> Accéder aux Labs
                </a>
                <a href="/KaliTest/login.php" class="btn-secondary">
                    <span class="btn-icon">🔓</span> Se Connecter
                </a>
            </div>
        </div>
        <div class="hero-terminal">
            <div class="terminal-header">
                <div class="terminal-dots">
                    <span class="dot red"></span>
                    <span class="dot yellow"></span>
                    <span class="dot green"></span>
                </div>
                <span class="terminal-title">cyberlab@kali:~$</span>
            </div>
            <div class="terminal-body" id="terminal-output">
                <p class="t-line"><span class="t-prompt">$</span> <span class="t-cmd" data-text="nmap -sV -A 192.168.1.1"></span></p>
                <p class="t-line"><span class="t-output">Starting Nmap 7.94 ...</span></p>
                <p class="t-line"><span class="t-output">PORT   STATE SERVICE VERSION</span></p>
                <p class="t-line"><span class="t-output">80/tcp open  http    Apache 2.4.57</span></p>
                <p class="t-line"><span class="t-output">3306/tcp open  mysql  MariaDB</span></p>
                <p class="t-line t-blink"><span class="t-prompt">$</span> <span class="t-cursor">█</span></p>
            </div>
        </div>
    </section>

    <!-- Labs Grid -->
    <section class="labs-section">
        <div class="section-header">
            <h2 class="section-title"><span class="accent">//</span> MODULES DE FORMATION</h2>
            <p class="section-desc">Chaque lab simule une vulnérabilité réelle. Exploitez-la, comprenez-la, défendez-vous.</p>
        </div>

        <div class="labs-grid">
            <!-- SQL Injection -->
            <div class="lab-card" onclick="window.location='/KaliTest/labs/sqli/'">
                <div class="lab-card-glow"></div>
                <div class="lab-header">
                    <div class="lab-icon">🗄️</div>
                    <div class="lab-meta">
                        <span class="lab-difficulty intermediate">INTERMÉDIAIRE</span>
                        <span class="lab-owasp">OWASP A03</span>
                    </div>
                </div>
                <h3 class="lab-title">SQL Injection</h3>
                <p class="lab-desc">Manipulez les requêtes SQL pour extraire des données confidentielles, bypasser l'authentification et accéder à des tables cachées.</p>
                <div class="lab-tags">
                    <span class="tag">Union-Based</span>
                    <span class="tag">Blind SQLi</span>
                    <span class="tag">Auth Bypass</span>
                </div>
                <div class="lab-footer">
                    <span class="lab-challenges">5 challenges</span>
                    <button class="lab-btn">LANCER ▶</button>
                </div>
            </div>

            <!-- XSS -->
            <div class="lab-card" onclick="window.location='/KaliTest/labs/xss/'">
                <div class="lab-card-glow xss"></div>
                <div class="lab-header">
                    <div class="lab-icon">💉</div>
                    <div class="lab-meta">
                        <span class="lab-difficulty beginner">DÉBUTANT</span>
                        <span class="lab-owasp">OWASP A03</span>
                    </div>
                </div>
                <h3 class="lab-title">Cross-Site Scripting</h3>
                <p class="lab-desc">Injectez du code JavaScript malveillant dans des pages web pour voler des cookies, rediriger des utilisateurs ou exécuter du code arbitraire.</p>
                <div class="lab-tags">
                    <span class="tag">Reflected XSS</span>
                    <span class="tag">Stored XSS</span>
                    <span class="tag">DOM XSS</span>
                </div>
                <div class="lab-footer">
                    <span class="lab-challenges">4 challenges</span>
                    <button class="lab-btn">LANCER ▶</button>
                </div>
            </div>

            <!-- CSRF -->
            <div class="lab-card" onclick="window.location='/KaliTest/labs/csrf/'">
                <div class="lab-card-glow csrf"></div>
                <div class="lab-header">
                    <div class="lab-icon">🔄</div>
                    <div class="lab-meta">
                        <span class="lab-difficulty intermediate">INTERMÉDIAIRE</span>
                        <span class="lab-owasp">OWASP A01</span>
                    </div>
                </div>
                <h3 class="lab-title">CSRF</h3>
                <p class="lab-desc">Forcez un utilisateur authentifié à exécuter des actions non désirées via des requêtes forgées cross-site.</p>
                <div class="lab-tags">
                    <span class="tag">Token Bypass</span>
                    <span class="tag">GET CSRF</span>
                    <span class="tag">POST CSRF</span>
                </div>
                <div class="lab-footer">
                    <span class="lab-challenges">3 challenges</span>
                    <button class="lab-btn">LANCER ▶</button>
                </div>
            </div>

            <!-- File Upload -->
            <div class="lab-card" onclick="window.location='/KaliTest/labs/fileupload/'">
                <div class="lab-card-glow upload"></div>
                <div class="lab-header">
                    <div class="lab-icon">📁</div>
                    <div class="lab-meta">
                        <span class="lab-difficulty advanced">AVANCÉ</span>
                        <span class="lab-owasp">OWASP A04</span>
                    </div>
                </div>
                <h3 class="lab-title">File Upload Vulnérable</h3>
                <p class="lab-desc">Contournez les filtres de validation pour uploader un webshell PHP et obtenir une exécution de code distante sur le serveur.</p>
                <div class="lab-tags">
                    <span class="tag">Extension Bypass</span>
                    <span class="tag">MIME Bypass</span>
                    <span class="tag">WebShell</span>
                </div>
                <div class="lab-footer">
                    <span class="lab-challenges">4 challenges</span>
                    <button class="lab-btn">LANCER ▶</button>
                </div>
            </div>

            <!-- Command Injection -->
            <div class="lab-card" onclick="window.location='/KaliTest/labs/cmdi/'">
                <div class="lab-card-glow cmd"></div>
                <div class="lab-header">
                    <div class="lab-icon">💻</div>
                    <div class="lab-meta">
                        <span class="lab-difficulty advanced">AVANCÉ</span>
                        <span class="lab-owasp">OWASP A03</span>
                    </div>
                </div>
                <h3 class="lab-title">Command Injection</h3>
                <p class="lab-desc">Exécutez des commandes système arbitraires en injectant des caractères spéciaux dans des champs d'entrée non filtrés.</p>
                <div class="lab-tags">
                    <span class="tag">OS Command</span>
                    <span class="tag">Blind CMDi</span>
                    <span class="tag">RCE</span>
                </div>
                <div class="lab-footer">
                    <span class="lab-challenges">3 challenges</span>
                    <button class="lab-btn">LANCER ▶</button>
                </div>
            </div>

            <!-- IDOR -->
            <div class="lab-card" onclick="window.location='/KaliTest/labs/idor/'">
                <div class="lab-card-glow idor"></div>
                <div class="lab-header">
                    <div class="lab-icon">🔑</div>
                    <div class="lab-meta">
                        <span class="lab-difficulty beginner">DÉBUTANT</span>
                        <span class="lab-owasp">OWASP A01</span>
                    </div>
                </div>
                <h3 class="lab-title">IDOR</h3>
                <p class="lab-desc">Accédez à des ressources non autorisées en manipulant des identifiants directement dans les paramètres URL ou les corps de requête.</p>
                <div class="lab-tags">
                    <span class="tag">URL Manipulation</span>
                    <span class="tag">ID Tampering</span>
                    <span class="tag">Data Exposure</span>
                </div>
                <div class="lab-footer">
                    <span class="lab-challenges">3 challenges</span>
                    <button class="lab-btn">LANCER ▶</button>
                </div>
            </div>

            <!-- LFI -->
            <div class="lab-card" onclick="window.location='/KaliTest/labs/lfi/'">
                <div class="lab-card-glow lfi"></div>
                <div class="lab-header">
                    <div class="lab-icon">📂</div>
                    <div class="lab-meta">
                        <span class="lab-difficulty advanced">AVANCÉ</span>
                        <span class="lab-owasp">OWASP A05</span>
                    </div>
                </div>
                <h3 class="lab-title">LFI / Path Traversal</h3>
                <p class="lab-desc">Lisez des fichiers arbitraires sur le serveur via la traversée de répertoires et l'inclusion de fichiers locaux.</p>
                <div class="lab-tags">
                    <span class="tag">../</span>
                    <span class="tag">Null Byte</span>
                    <span class="tag">/etc/passwd</span>
                </div>
                <div class="lab-footer">
                    <span class="lab-challenges">3 challenges</span>
                    <button class="lab-btn">LANCER ▶</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Banner -->
    <section class="info-banner">
        <div class="info-icon">⚠️</div>
        <div class="info-text">
            <strong>Environnement légal et éthique</strong> — Cette plateforme est conçue uniquement pour l'apprentissage de la cybersécurité. Toutes les vulnérabilités sont intentionnelles et isolées dans cet environnement de test.
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-brand">CYBER<span class="accent">LAB</span></div>
        <p class="footer-text">Plateforme de pentesting pour l'apprentissage éthique de la cybersécurité</p>
        <div class="footer-links">
            <a href="/KaliTest/about.php">À Propos</a>
            <a href="/KaliTest/labs/">Labs</a>
            <a href="/KaliTest/login.php">Connexion</a>
        </div>
        <p class="footer-copy">© 2026 CyberLab — Use Responsibly</p>
    </footer>

    <script src="/KaliTest/assets/js/main.js"></script>
</body>
</html>
