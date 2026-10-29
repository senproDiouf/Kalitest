<?php
require_once __DIR__ . '/../../config/config.php';

$level = $_GET['level'] ?? '1';
$note_data = null;
$user_data = null;

// Simuler une session utilisateur
$current_user_id = 4; // Utilisateur connecté = hacker (id 4)

if ($level == '1') {
    // ⚠️ IDOR - Accès direct aux notes par ID sans vérification d'ownership
    $note_id = $_GET['note_id'] ?? null;
    if ($note_id !== null) {
        $db = getVulnDB();
        // Pas de vérification : WHERE user_id = $current_user_id
        $res = $db->query("SELECT * FROM notes WHERE id = " . intval($note_id));
        $note_data = $res ? $res->fetch_assoc() : null;
        $db->close();
    }

} elseif ($level == '2') {
    // ⚠️ IDOR - Accès au profil utilisateur par ID
    $user_id = $_GET['user_id'] ?? null;
    if ($user_id !== null) {
        $db = getVulnDB();
        $res = $db->query("SELECT id, username, email, role, secret_token FROM users WHERE id = " . intval($user_id));
        $user_data = $res ? $res->fetch_assoc() : null;
        $db->close();
    }

} elseif ($level == '3') {
    // ⚠️ IDOR - Manipulation de paramètre caché dans un formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $target_id = $_POST['user_id'] ?? $current_user_id;
        $new_email = $_POST['new_email'] ?? '';
        $db = getVulnDB();
        $db->query("UPDATE users SET email = '" . $db->real_escape_string($new_email) . "' WHERE id = " . intval($target_id));
        $affected = $db->affected_rows;
        $db->close();
        $message = $affected > 0 ? "✅ Email de l'utilisateur ID=$target_id mis à jour vers: $new_email" : "❌ Erreur de mise à jour";
    }
}

// Charger toutes les notes publiques
$db = getVulnDB();
$public_notes = $db->query("SELECT n.*, u.username FROM notes n JOIN users u ON n.user_id = u.id WHERE n.is_private = 0");
$db->close();

$db2 = getVulnDB();
$all_notes = $db2->query("SELECT n.id, n.user_id, n.title, n.is_private, u.username FROM notes n JOIN users u ON n.user_id = u.id ORDER BY n.id");
$db2->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDOR Lab — CyberLab</title>
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
            <p class="page-breadcrumb"><a href="/KaliTest/">Accueil</a> / <a href="/KaliTest/labs/">Labs</a> / IDOR</p>
            <h1 class="page-title">🔑 IDOR LAB</h1>
            <p class="page-desc">Insecure Direct Object Reference — Accédez à des ressources non autorisées en manipulant des identifiants dans l'URL ou les formulaires.</p>
        </div>

        <div style="margin-bottom:1rem" class="alert alert-info">
            👤 Vous êtes connecté en tant que : <strong>hacker</strong> (ID = <?= $current_user_id ?>) —
            Objectif: accéder aux données des autres utilisateurs en manipulant les IDs
        </div>

        <div style="display:flex; gap:.6rem; margin-bottom:2rem; flex-wrap:wrap">
            <a href="?level=1" class="lab-btn" style="<?= $level=='1'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 1 — Notes Privées</a>
            <a href="?level=2" class="lab-btn" style="<?= $level=='2'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 2 — Profils Utilisateurs</a>
            <a href="?level=3" class="lab-btn" style="<?= $level=='3'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 3 — Paramètre Caché</a>
        </div>

        <div class="lab-content">
            <div>
                <?php if ($level == '1'): ?>
                <div class="lab-panel" style="margin-bottom:1.5rem">
                    <div class="panel-header">🎯 NIVEAU 1 — Lecture de Notes (IDOR via URL)</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem">
                            L'application affiche une note par son ID dans l'URL : <code style="color:var(--neon-yellow)">?note_id=4</code>.
                            Il n'y a <strong>aucune vérification</strong> que la note appartient à l'utilisateur courant.
                        </p>

                        <div style="margin-bottom:1rem">
                            <label class="form-label">ACCÉDER À UNE NOTE PAR ID</label>
                            <div style="display:flex; gap:.5rem; align-items:center">
                                <input type="number" id="note-id-input" class="form-input" value="4" min="1" max="6" style="max-width:120px">
                                <button class="btn-submit" onclick="window.location='?level=1&note_id='+document.getElementById('note-id-input').value">
                                    🔍 ACCÉDER
                                </button>
                            </div>
                            <p class="form-hint">💡 Votre note est ID=6. Essayez ID=1, 2, 3, 4, 5 (notes privées d'autres utilisateurs !)</p>
                        </div>

                        <?php if ($note_data): ?>
                        <div class="result-box">
                            <div class="result-header">📝 NOTE ID=<?= htmlspecialchars($_GET['note_id']) ?></div>
                            <div class="result-body">
                                <table class="data-table">
                                    <tr><th>Champ</th><th>Valeur</th></tr>
                                    <tr><td>ID</td><td><?= $note_data['id'] ?></td></tr>
                                    <tr><td>User ID</td><td style="color:<?= $note_data['user_id'] != $current_user_id ? 'var(--neon-red)' : 'var(--neon-green)' ?>"><?= $note_data['user_id'] ?> <?= $note_data['user_id'] != $current_user_id ? '⚠️ PAS VOTRE NOTE !' : '✅ Votre note' ?></td></tr>
                                    <tr><td>Titre</td><td><?= htmlspecialchars($note_data['title']) ?></td></tr>
                                    <tr><td>Contenu</td><td style="color:var(--neon-green)"><?= htmlspecialchars($note_data['content']) ?></td></tr>
                                    <tr><td>Privée</td><td><?= $note_data['is_private'] ? '🔒 OUI' : '🌐 NON' ?></td></tr>
                                </table>
                                <?php if (str_starts_with($note_data['content'], 'FLAG{')): ?>
                                <div class="flag-box" style="margin-top:1rem">🚩 <?= htmlspecialchars($note_data['content']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Index des notes -->
                <div class="lab-panel">
                    <div class="panel-header">📋 INDEX DES NOTES (ce que vous pouvez voir normalement)</div>
                    <div class="panel-body">
                        <table class="data-table">
                            <tr><th>ID</th><th>Propriétaire</th><th>Titre</th><th>Privée?</th><th>Action</th></tr>
                            <?php
                            $all_notes->data_seek(0);
                            while ($note = $all_notes->fetch_assoc()):
                                $is_mine = $note['user_id'] == $current_user_id;
                                $is_private = $note['is_private'];
                            ?>
                            <tr style="<?= ($is_private && !$is_mine) ? 'opacity:.5' : '' ?>">
                                <td><?= $note['id'] ?></td>
                                <td style="color:<?= $is_mine ? 'var(--neon-green)' : 'var(--text-secondary)' ?>"><?= htmlspecialchars($note['username']) ?> <?= $is_mine ? '(vous)' : '' ?></td>
                                <td><?= $is_private && !$is_mine ? '🔒 [Privée]' : htmlspecialchars($note['title']) ?></td>
                                <td><?= $is_private ? '🔒' : '🌐' ?></td>
                                <td>
                                    <a href="?level=1&note_id=<?= $note['id'] ?>"
                                       style="font-family:var(--font-mono); font-size:.75rem; color:<?= $is_private && !$is_mine ? 'var(--neon-red)' : 'var(--neon-green)' ?>">
                                        <?= $is_private && !$is_mine ? '⚠️ Accéder (IDOR)' : '🔍 Voir' ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                </div>

                <?php elseif ($level == '2'): ?>
                <div class="lab-panel">
                    <div class="panel-header">🎯 NIVEAU 2 — Profils Utilisateurs (/profile?user_id=X)</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem">
                            L'API retourne le profil complet d'un utilisateur par son ID, incluant le token secret.
                        </p>

                        <div style="display:flex; gap:.5rem; margin-bottom:1rem; flex-wrap:wrap">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <a href="?level=2&user_id=<?= $i ?>"
                               style="font-family:var(--font-mono); font-size:.8rem; color:<?= $i == $current_user_id ? 'var(--neon-green)' : 'var(--neon-red)' ?>; background:rgba(255,255,255,.04); border:1px solid var(--border-dim); padding:.4rem .8rem; border-radius:4px; text-decoration:none">
                                User ID=<?= $i ?> <?= $i == $current_user_id ? '(vous)' : '' ?>
                            </a>
                            <?php endfor; ?>
                        </div>

                        <?php if ($user_data): ?>
                        <div class="result-box">
                            <div class="result-header">👤 PROFIL USER ID=<?= htmlspecialchars($_GET['user_id']) ?></div>
                            <div class="result-body">
                                <table class="data-table">
                                    <?php foreach ($user_data as $key => $val): ?>
                                    <tr>
                                        <th><?= htmlspecialchars($key) ?></th>
                                        <td style="color:<?= $key === 'secret_token' ? 'var(--neon-yellow)' : 'var(--text-primary)' ?>"><?= htmlspecialchars((string)$val) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </table>
                                <?php if (isset($user_data['secret_token'])): ?>
                                <div class="flag-box" style="margin-top:1rem">🚩 <?= htmlspecialchars($user_data['secret_token']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php elseif ($level == '3'): ?>
                <div class="lab-panel">
                    <div class="panel-header">🎯 NIVEAU 3 — Paramètre Caché dans le Formulaire</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem">
                            Ce formulaire contient un champ <code>user_id</code> caché. En l'inspectant et le modifiant,
                            vous pouvez modifier le compte d'un autre utilisateur.
                        </p>

                        <?php if (isset($message)): ?>
                        <div class="alert <?= str_starts_with($message, '✅') ? 'alert-success' : 'alert-danger' ?>" style="margin-bottom:1rem">
                            <?= htmlspecialchars($message) ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="?level=3">
                            <!-- ⚠️ Champ caché IDOR - changez user_id avec DevTools -->
                            <input type="hidden" name="user_id" value="<?= $current_user_id ?>" id="hidden-uid">

                            <div class="form-group">
                                <label class="form-label">NOUVEL EMAIL</label>
                                <input type="email" name="new_email" class="form-input"
                                       value="hacked@attaquant.com" placeholder="nouveau@email.com">
                            </div>
                            <button type="submit" class="btn-submit">✉️ METTRE À JOUR MON EMAIL</button>
                        </form>

                        <div style="margin-top:1rem; padding:.8rem; background:rgba(0,212,255,.04); border:1px solid rgba(0,212,255,.1); border-radius:6px; font-family:var(--font-mono); font-size:.75rem; color:var(--neon-blue)">
                            💡 <strong>Indice:</strong> Ouvrez les DevTools (F12), trouvez le champ caché
                            <code style="color:var(--neon-yellow)">user_id</code> et changez sa valeur vers 1 (admin)
                            avant de soumettre le formulaire !
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="hint-panel">
                <div class="hint-section">
                    <div class="panel-header">📖 GUIDE IDOR</div>
                </div>
                <div class="hint-section">
                    <p class="hint-title">🎯 OBJECTIFS</p>
                    <p class="hint-text">
                        N1: Lire les notes privées d'alice (ID=2) et bob (ID=3)<br>
                        N2: Extraire le token secret de l'admin (ID=1)<br>
                        N3: Modifier l'email de l'admin via DevTools
                    </p>
                </div>
                <div class="hint-section">
                    <p class="hint-title">⚡ MÉTHODES</p>
                    <code class="hint-code">?note_id=1 (note admin)</code>
                    <code class="hint-code">?note_id=2 (note alice)</code>
                    <code class="hint-code">?user_id=1 (profil admin)</code>
                    <p class="hint-text" style="margin-top:.5rem">Pour N3: F12 → Inspecteur → Modifier user_id caché</p>
                </div>
                <div class="hint-section">
                    <p class="hint-title">🛡️ DÉFENSES</p>
                    <p class="hint-text">
                        • Vérifier ownership côté serveur<br>
                        • Utiliser des UUID au lieu d'IDs séquentiels<br>
                        • Contrôle d'accès basé sur le rôle (RBAC)<br>
                        • Jamais faire confiance aux paramètres client<br>
                        • Journaliser les accès anormaux
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="/KaliTest/assets/js/main.js"></script>
</body>
</html>
