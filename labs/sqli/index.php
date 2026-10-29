<?php
require_once __DIR__ . '/../../config/config.php';

$result = null;
$query_display = '';
$error = '';
$level = $_GET['level'] ?? '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['id'])) {
    $conn = getVulnDB();

    if ($level == '1') {
        // === NIVEAU 1 : SQL Injection classique (UNION-based) ===
        $id = $_GET['id'] ?? $_POST['id'] ?? '1';
        $query = "SELECT id, name, description, price FROM products WHERE id = $id";
        $query_display = $query;

        $res = $conn->query($query);
        if ($res) {
            $result = [];
            while ($row = $res->fetch_assoc()) {
                $result[] = $row;
            }
        } else {
            $error = $conn->error;
        }

    } elseif ($level == '2') {
        // === NIVEAU 2 : SQL Injection avec string (quote needed) ===
        $name = $_POST['name'] ?? '';
        $query = "SELECT id, name, description, price FROM products WHERE name = '$name'";
        $query_display = $query;

        $res = $conn->query($query);
        if ($res) {
            $result = [];
            while ($row = $res->fetch_assoc()) {
                $result[] = $row;
            }
        } else {
            $error = $conn->error;
        }

    } elseif ($level == '3') {
        // === NIVEAU 3 : Blind SQLi (boolean-based) ===
        $id = $_GET['id'] ?? '1';
        $query = "SELECT name FROM products WHERE id = $id";
        $query_display = $query;

        $res = $conn->query($query);
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $result = [['response' => '✅ Produit trouvé: ' . htmlspecialchars($row['name'])]];
        } elseif ($res) {
            $result = [['response' => '❌ Aucun produit trouvé']];
        } else {
            $error = $conn->error;
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Lab — CyberLab</title>
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
            <p class="page-breadcrumb"><a href="/KaliTest/">Accueil</a> / <a href="/KaliTest/labs/">Labs</a> / SQL Injection</p>
            <h1 class="page-title">🗄️ SQL INJECTION LAB</h1>
            <p class="page-desc">Exploitez les failles d'injection SQL pour extraire des données, accéder à des tables cachées et contourner l'authentification. Chaque niveau augmente la difficulté.</p>
        </div>

        <!-- Level Selector -->
        <div style="display:flex; gap:.6rem; margin-bottom:2rem; flex-wrap:wrap">
            <a href="?level=1" class="lab-btn <?= $level=='1'?'active':'' ?>" style="<?= $level=='1'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">
                Niveau 1 — Union-Based
            </a>
            <a href="?level=2" class="lab-btn <?= $level=='2'?'active':'' ?>" style="<?= $level=='2'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">
                Niveau 2 — String Injection
            </a>
            <a href="?level=3" class="lab-btn <?= $level=='3'?'active':'' ?>" style="<?= $level=='3'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">
                Niveau 3 — Blind Boolean
            </a>
        </div>

        <div class="lab-content">
            <!-- Main Panel -->
            <div>
                <?php if ($level == '1'): ?>
                <!-- NIVEAU 1 -->
                <div class="lab-panel" style="margin-bottom:1.5rem">
                    <div class="panel-header">🎯 NIVEAU 1 — Recherche de Produit (GET parameter)</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem;">
                            Ce formulaire passe l'ID du produit directement dans la requête SQL sans sanitisation.
                            Objectif : extraire le contenu de la table <code style="color:var(--neon-green)">users</code>.
                        </p>

                        <form method="GET" action="" id="sqli-form-1">
                            <input type="hidden" name="level" value="1">
                            <div class="form-group">
                                <label class="form-label">ID PRODUIT (paramètre GET)</label>
                                <input type="text" name="id" class="form-input" id="sqli-input"
                                       placeholder="ex: 1" value="<?= htmlspecialchars($_GET['id'] ?? '1') ?>">
                                <p class="form-hint">💡 L'URL ressemble à : ?id=1 — Essayez des payloads d'injection</p>
                            </div>
                            <button type="submit" class="btn-submit">🔍 RECHERCHER</button>
                        </form>

                        <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" style="margin-top:1rem">
                            <strong>Erreur SQL:</strong> <?= htmlspecialchars($error) ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($result !== null): ?>
                        <div class="result-box">
                            <div class="result-header">📊 RÉSULTAT — <?= count($result) ?> ligne(s) retournée(s)</div>
                            <div class="result-body">
                                <?php if (empty($result)): ?>
                                <p style="color:var(--text-secondary)">Aucun résultat.</p>
                                <?php else: ?>
                                <table class="data-table">
                                    <tr>
                                        <?php foreach (array_keys($result[0]) as $col): ?>
                                        <th><?= htmlspecialchars($col) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php foreach ($result as $row): ?>
                                    <tr>
                                        <?php foreach ($row as $val): ?>
                                        <td><?= htmlspecialchars((string)$val) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </table>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php elseif ($level == '2'): ?>
                <!-- NIVEAU 2 -->
                <div class="lab-panel" style="margin-bottom:1.5rem">
                    <div class="panel-header">🎯 NIVEAU 2 — Recherche par Nom (POST string)</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem;">
                            Ce formulaire utilise une chaîne de texte dans la requête SQL.
                            Vous devez gérer les quotes. Objectif : extraire les credentials des utilisateurs.
                        </p>

                        <form method="POST" action="?level=2">
                            <div class="form-group">
                                <label class="form-label">NOM DU PRODUIT</label>
                                <input type="text" name="name" class="form-input"
                                       placeholder="ex: Kali Linux USB" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                                <p class="form-hint">💡 La requête est : WHERE name = '[INPUT]' — Injectez après la quote</p>
                            </div>
                            <button type="submit" class="btn-submit">🔍 RECHERCHER</button>
                        </form>

                        <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" style="margin-top:1rem">
                            <strong>Erreur SQL:</strong> <?= htmlspecialchars($error) ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($result !== null): ?>
                        <div class="result-box">
                            <div class="result-header">📊 RÉSULTAT — <?= count($result) ?> ligne(s)</div>
                            <div class="result-body">
                                <?php if (empty($result)): ?>
                                <p style="color:var(--text-secondary)">Aucun résultat.</p>
                                <?php else: ?>
                                <table class="data-table">
                                    <tr><?php foreach (array_keys($result[0]) as $col): ?><th><?= htmlspecialchars($col) ?></th><?php endforeach; ?></tr>
                                    <?php foreach ($result as $row): ?>
                                    <tr><?php foreach ($row as $val): ?><td><?= htmlspecialchars((string)$val) ?></td><?php endforeach; ?></tr>
                                    <?php endforeach; ?>
                                </table>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php elseif ($level == '3'): ?>
                <!-- NIVEAU 3 -->
                <div class="lab-panel" style="margin-bottom:1.5rem">
                    <div class="panel-header">🎯 NIVEAU 3 — Blind SQL Injection (Boolean-Based)</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem;">
                            L'application ne retourne que Trouvé/Non-trouvé. Utilisez des conditions booléennes
                            pour extraire les données caractère par caractère.
                        </p>

                        <form method="GET" action="?level=3">
                            <div class="form-group">
                                <label class="form-label">ID PRODUIT (Blind)</label>
                                <input type="text" name="id" class="form-input"
                                       placeholder="ex: 1" value="<?= htmlspecialchars($_GET['id'] ?? '1') ?>">
                                <p class="form-hint">💡 Testez : 1 AND 1=1 (vrai) vs 1 AND 1=2 (faux)</p>
                            </div>
                            <button type="submit" class="btn-submit">🔍 TESTER</button>
                        </form>

                        <?php if ($result !== null): ?>
                        <div class="alert <?= str_contains($result[0]['response'], '✅') ? 'alert-success' : 'alert-danger' ?>" style="margin-top:1rem; font-size:1rem">
                            <?= $result[0]['response'] ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Query Display -->
                <?php if ($query_display): ?>
                <div class="lab-panel">
                    <div class="panel-header">📝 REQUÊTE SQL EXÉCUTÉE (mode éducatif)</div>
                    <div class="panel-body">
                        <code style="display:block; font-family:var(--font-mono); font-size:.85rem; color:var(--neon-yellow); background:rgba(0,0,0,.3); padding:1rem; border-radius:6px; word-break:break-all; border:1px solid rgba(255,215,0,.1)">
                            <?= htmlspecialchars($query_display) ?>
                        </code>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Hint Panel -->
            <div class="hint-panel">
                <div class="hint-section">
                    <div class="panel-header">📖 GUIDE DU LAB</div>
                </div>

                <div class="hint-section">
                    <p class="hint-title">🎯 OBJECTIF</p>
                    <p class="hint-text">
                        <?php if ($level == '1'): ?>
                        Utilisez UNION SELECT pour extraire les usernames et mots de passe de la table <code style="color:var(--neon-green)">users</code>.
                        <?php elseif ($level == '2'): ?>
                        Fermez la chaîne et injectez une UNION pour accéder aux données sensibles.
                        <?php else: ?>
                        Déduisez les données en observant les réponses booléennes vraie/fausse.
                        <?php endif; ?>
                    </p>
                </div>

                <div class="hint-section">
                    <p class="hint-title">⚡ PAYLOADS</p>
                    <?php if ($level == '1'): ?>
                    <code class="hint-code">1 OR 1=1</code>
                    <code class="hint-code">1 UNION SELECT 1,2,3,4-- -</code>
                    <code class="hint-code">1 UNION SELECT id,username,password,email FROM users-- -</code>
                    <code class="hint-code">0 UNION SELECT 1,table_name,3,4 FROM information_schema.tables WHERE table_schema=database()-- -</code>
                    <?php elseif ($level == '2'): ?>
                    <code class="hint-code">test' OR '1'='1</code>
                    <code class="hint-code">%' UNION SELECT 1,2,3,4-- -</code>
                    <code class="hint-code">%' UNION SELECT id,username,password,email FROM users-- -</code>
                    <?php else: ?>
                    <code class="hint-code">1 AND 1=1</code>
                    <code class="hint-code">1 AND 1=2</code>
                    <code class="hint-code">1 AND (SELECT COUNT(*) FROM users)>0</code>
                    <code class="hint-code">1 AND SUBSTRING((SELECT username FROM users LIMIT 1),1,1)='a'</code>
                    <?php endif; ?>
                </div>

                <div class="hint-section">
                    <p class="hint-title">📚 EXPLICATION</p>
                    <p class="hint-text">
                        L'injection SQL survient quand les données utilisateur sont directement concaténées dans une requête SQL sans validation ni échappement.
                        <br><br>
                        <strong style="color:var(--neon-red)">La table users contient:</strong><br>
                        id, username, password, email, role, secret_token
                    </p>
                </div>

                <div class="hint-section">
                    <p class="hint-title">🛡️ CONTRE-MESURES</p>
                    <p class="hint-text">
                        • Requêtes préparées (PDO/MySQLi)<br>
                        • Validation des entrées<br>
                        • Principe du moindre privilège<br>
                        • WAF (Web Application Firewall)
                    </p>
                </div>

                <div class="hint-section">
                    <p class="hint-title">🔗 OUTILS</p>
                    <p class="hint-text" style="font-size:.75rem; color:var(--neon-blue)">
                        • sqlmap -u "URL?id=1" --dbs<br>
                        • Burp Suite Intruder<br>
                        • Manual testing
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="/KaliTest/assets/js/main.js"></script>
    <script>
        // Auto-fill input from hint codes
        document.querySelectorAll('.hint-code').forEach(code => {
            code.addEventListener('click', () => {
                const input = document.querySelector('.form-input');
                if (input) {
                    input.value = code.textContent.trim();
                    showToast('Payload injecté dans le champ !');
                }
            });
        });
    </script>
</body>
</html>
