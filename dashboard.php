<?php
require_once __DIR__ . '/config/config.php';

if (!isset($_SESSION['user'])) {
    header('Location: /KaliTest/login.php');
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — CyberLab</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/KaliTest/assets/css/style.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 1.5rem;
            max-width: 1300px;
            margin: 0 auto;
        }
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .user-profile-card {
            background: var(--bg-card);
            border: 1px solid var(--border-dim);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
        }
        .avatar {
            width: 70px; height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--neon-green), var(--neon-blue));
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1rem;
            box-shadow: 0 0 30px rgba(0,255,136,.2);
        }
        .user-name {
            font-family: var(--font-title);
            font-size: 1rem;
            margin-bottom: .3rem;
        }
        .user-role {
            font-family: var(--font-mono);
            font-size: .7rem;
            color: <?= $user['role'] === 'admin' ? 'var(--neon-red)' : 'var(--neon-green)' ?>;
            background: <?= $user['role'] === 'admin' ? 'rgba(255,51,102,.1)' : 'rgba(0,255,136,.1)' ?>;
            padding: .2rem .8rem;
            border-radius: 10px;
            border: 1px solid <?= $user['role'] === 'admin' ? 'rgba(255,51,102,.3)' : 'rgba(0,255,136,.3)' ?>;
        }
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-dim);
            border-radius: 10px;
            padding: 1.2rem;
            text-align: center;
            transition: var(--transition);
        }
        .stat-card:hover { border-color: rgba(0,255,136,.3); }
        .stat-card-num {
            font-family: var(--font-title);
            font-size: 2rem;
            font-weight: 700;
            color: var(--neon-green);
        }
        .stat-card-label {
            font-family: var(--font-mono);
            font-size: .65rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-top: .3rem;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .7rem 1rem;
            font-family: var(--font-mono);
            font-size: .8rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 6px;
            transition: var(--transition);
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(0,255,136,.06);
            color: var(--neon-green);
        }
        .log-terminal {
            background: #000;
            border: 1px solid rgba(0,255,136,.15);
            border-radius: 8px;
            padding: 1rem;
            height: 200px;
            overflow-y: auto;
            font-family: var(--font-mono);
            font-size: .75rem;
        }
        .log-line { color: #444; margin: .2rem 0; }
        .log-line.active { color: var(--neon-green); }
        .log-line.warn { color: var(--neon-yellow); }
        .log-line.error { color: var(--neon-red); }
        @media(max-width:900px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .stat-cards { grid-template-columns: repeat(2,1fr); }
        }
    </style>
</head>
<body>
    <canvas id="matrix-canvas"></canvas>

    <nav class="navbar">
        <div class="nav-brand">
            <div class="brand-icon">⬡</div>
            <span class="brand-name">CYBER<span class="accent">LAB</span></span>
            <span class="version-tag">v1.0</span>
        </div>
        <div class="nav-status" style="margin-right:auto">
            <div class="status-dot"></div>
            <span class="status-text" id="nav-time"></span>
        </div>
        <div class="nav-links">
            <a href="/KaliTest/" class="nav-link">Accueil</a>
            <a href="/KaliTest/labs/" class="nav-link">Labs</a>
            <a href="/KaliTest/tools/" class="nav-link">Outils</a>
        </div>
        <div class="nav-user">
            <span class="user-badge">👤 <?= htmlspecialchars($user['username']) ?></span>
            <a href="/KaliTest/logout.php" class="btn-logout">Déco</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="page-header">
            <p class="page-breadcrumb"><a href="/KaliTest/">Accueil</a> / Dashboard</p>
            <h1 class="page-title">🖥️ DASHBOARD OPÉRATIONNEL</h1>
            <p class="page-desc">Bienvenue <strong style="color:var(--neon-green)"><?= htmlspecialchars($user['username']) ?></strong> — Choisissez un lab et commencez l'entraînement.</p>
        </div>

        <div class="dashboard-grid">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="user-profile-card">
                    <div class="avatar">👤</div>
                    <div class="user-name"><?= htmlspecialchars($user['username']) ?></div>
                    <div class="user-role"><?= strtoupper($user['role']) ?></div>
                    <hr style="border-color:var(--border-dim); margin:1rem 0">
                    <div style="font-family:var(--font-mono); font-size:.72rem; color:var(--text-secondary); text-align:left; line-height:1.8">
                        <div>📧 <?= htmlspecialchars($user['email'] ?? 'N/A') ?></div>
                        <div>🆔 ID: <?= $user['id'] ?></div>
                        <?php if ($user['role'] === 'admin'): ?>
                        <div style="margin-top:.5rem; color:var(--neon-red)">🔑 Token: <code style="color:var(--neon-yellow); font-size:.65rem"><?= htmlspecialchars($user['secret_token']) ?></code></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="lab-panel" style="padding:0">
                    <div class="panel-header">📋 NAVIGATION</div>
                    <div class="sidebar-nav" style="padding:.5rem">
                        <a href="/KaliTest/dashboard.php" class="active">🏠 Dashboard</a>
                        <a href="/KaliTest/labs/">⚡ Tous les Labs</a>
                        <a href="/KaliTest/labs/sqli/">🗄️ SQL Injection</a>
                        <a href="/KaliTest/labs/xss/">💉 XSS</a>
                        <a href="/KaliTest/labs/csrf/">🔄 CSRF</a>
                        <a href="/KaliTest/labs/fileupload/">📁 File Upload</a>
                        <a href="/KaliTest/labs/cmdi/">💻 Cmd Injection</a>
                        <a href="/KaliTest/labs/idor/">🔑 IDOR</a>
                        <a href="/KaliTest/labs/lfi/">📂 LFI</a>
                        <a href="/KaliTest/tools/">🛠️ Outils</a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div>
                <!-- Stats -->
                <div class="stat-cards">
                    <div class="stat-card">
                        <div class="stat-card-num" style="color:var(--neon-green)">7</div>
                        <div class="stat-card-label">Labs Actifs</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-num" style="color:var(--neon-blue)">25+</div>
                        <div class="stat-card-label">Vulnérabilités</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-num" style="color:var(--neon-red)"><?= $user['role'] === 'admin' ? 'ADMIN' : 'USER' ?></div>
                        <div class="stat-card-label">Niveau Accès</div>
                    </div>
                </div>

                <!-- Quick Labs -->
                <div class="lab-panel" style="margin-bottom:1.5rem">
                    <div class="panel-header">⚡ ACCÈS RAPIDE AUX LABS</div>
                    <div class="panel-body">
                        <div class="labs-grid" style="grid-template-columns: repeat(auto-fill,minmax(250px,1fr)); gap:1rem">
                            <a href="/KaliTest/labs/sqli/" style="text-decoration:none">
                                <div class="lab-card" style="padding:1rem">
                                    <div style="font-size:1.5rem; margin-bottom:.5rem">🗄️</div>
                                    <div style="font-family:var(--font-title); font-size:.9rem; margin-bottom:.3rem">SQL Injection</div>
                                    <div style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-yellow)">INTERMÉDIAIRE</div>
                                </div>
                            </a>
                            <a href="/KaliTest/labs/xss/" style="text-decoration:none">
                                <div class="lab-card" style="padding:1rem">
                                    <div style="font-size:1.5rem; margin-bottom:.5rem">💉</div>
                                    <div style="font-family:var(--font-title); font-size:.9rem; margin-bottom:.3rem">XSS</div>
                                    <div style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-green)">DÉBUTANT</div>
                                </div>
                            </a>
                            <a href="/KaliTest/labs/csrf/" style="text-decoration:none">
                                <div class="lab-card" style="padding:1rem">
                                    <div style="font-size:1.5rem; margin-bottom:.5rem">🔄</div>
                                    <div style="font-family:var(--font-title); font-size:.9rem; margin-bottom:.3rem">CSRF</div>
                                    <div style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-yellow)">INTERMÉDIAIRE</div>
                                </div>
                            </a>
                            <a href="/KaliTest/labs/fileupload/" style="text-decoration:none">
                                <div class="lab-card" style="padding:1rem">
                                    <div style="font-size:1.5rem; margin-bottom:.5rem">📁</div>
                                    <div style="font-family:var(--font-title); font-size:.9rem; margin-bottom:.3rem">File Upload</div>
                                    <div style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-red)">AVANCÉ</div>
                                </div>
                            </a>
                            <a href="/KaliTest/labs/cmdi/" style="text-decoration:none">
                                <div class="lab-card" style="padding:1rem">
                                    <div style="font-size:1.5rem; margin-bottom:.5rem">💻</div>
                                    <div style="font-family:var(--font-title); font-size:.9rem; margin-bottom:.3rem">Cmd Injection</div>
                                    <div style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-red)">AVANCÉ</div>
                                </div>
                            </a>
                            <a href="/KaliTest/labs/idor/" style="text-decoration:none">
                                <div class="lab-card" style="padding:1rem">
                                    <div style="font-size:1.5rem; margin-bottom:.5rem">🔑</div>
                                    <div style="font-family:var(--font-title); font-size:.9rem; margin-bottom:.3rem">IDOR</div>
                                    <div style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-green)">DÉBUTANT</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- System Log -->
                <div class="lab-panel">
                    <div class="panel-header">📜 JOURNAL SYSTÈME</div>
                    <div class="panel-body">
                        <div class="log-terminal" id="sys-log">
                            <div class="log-line active">[<?= date('H:i:s') ?>] SESSION INITIÉE — Utilisateur: <?= htmlspecialchars($user['username']) ?></div>
                            <div class="log-line active">[SYSTEM] CyberLab v1.0 opérationnel</div>
                            <div class="log-line warn">[WARNING] 7 modules de vulnérabilités actifs</div>
                            <div class="log-line">[INFO] PHP <?= PHP_VERSION ?> | MySQL connecté</div>
                            <div class="log-line">[INFO] IP Client: <?= $_SERVER['REMOTE_ADDR'] ?></div>
                            <div class="log-line">[INFO] User-Agent: <?= htmlspecialchars(substr($_SERVER['HTTP_USER_AGENT'] ?? 'N/A', 0, 80)) ?></div>
                            <div class="log-line active">[READY] Sélectionnez un lab pour commencer...</div>
                            <div class="log-line" id="log-cursor" style="color:var(--neon-green)">_ </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/KaliTest/assets/js/main.js"></script>
    <script>
        // Animate log cursor
        const cursor = document.getElementById('log-cursor');
        setInterval(() => { cursor.style.opacity = cursor.style.opacity === '0' ? '1' : '0'; }, 500);
    </script>
</body>
</html>
