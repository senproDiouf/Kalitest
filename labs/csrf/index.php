<?php
require_once __DIR__ . '/../../config/config.php';

$level = $_GET['level'] ?? '1';
$message = '';

// Simuler un utilisateur connecté pour le lab CSRF
if (!isset($_SESSION['csrf_user'])) {
    $_SESSION['csrf_user'] = [
        'id' => 1,
        'username' => 'alice',
        'email' => 'alice@cyberlab.local',
        'balance' => 1000.00
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($level == '1') {
        // NIVEAU 1 : CSRF sans protection - transfert d'argent
        $amount = floatval($_POST['amount'] ?? 0);
        $to = $_POST['to'] ?? '';
        $_SESSION['csrf_user']['balance'] -= $amount;
        $message = "✅ Transfert de {$amount}€ vers '{$to}' effectué ! Nouveau solde: " . $_SESSION['csrf_user']['balance'] . "€";

    } elseif ($level == '2') {
        // NIVEAU 2 : Changement d'email sans token CSRF
        $new_email = $_POST['email'] ?? '';
        $_SESSION['csrf_user']['email'] = $new_email;
        $message = "✅ Email mis à jour vers: $new_email";

    } elseif ($level == '3') {
        // NIVEAU 3 : Avec token CSRF (protection)
        $token = $_POST['csrf_token'] ?? '';
        $expected = $_SESSION['csrf_token_l3'] ?? '';

        if ($token === $expected && !empty($token)) {
            $message = "✅ Changement de mot de passe accepté — Token CSRF valide !";
        } else {
            $message = "❌ ERREUR: Token CSRF invalide ou manquant !";
        }
    }
}

// Générer token pour niveau 3
if ($level == '3') {
    $_SESSION['csrf_token_l3'] = bin2hex(random_bytes(16));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSRF Lab — CyberLab</title>
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
            <p class="page-breadcrumb"><a href="/KaliTest/">Accueil</a> / <a href="/KaliTest/labs/">Labs</a> / CSRF</p>
            <h1 class="page-title">🔄 CSRF LAB</h1>
            <p class="page-desc">Cross-Site Request Forgery — Forcez un utilisateur authentifié à exécuter des actions non désirées à son insu.</p>
        </div>

        <div style="display:flex; gap:.6rem; margin-bottom:2rem; flex-wrap:wrap">
            <a href="?level=1" class="lab-btn" style="<?= $level=='1'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 1 — CSRF Transfert</a>
            <a href="?level=2" class="lab-btn" style="<?= $level=='2'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 2 — CSRF Email</a>
            <a href="?level=3" class="lab-btn" style="<?= $level=='3'?'background:rgba(0,255,136,.2); border-color:var(--neon-green)':'' ?>">Niveau 3 — Avec Protection</a>
        </div>

        <!-- User Info Banner -->
        <div class="alert alert-info" style="margin-bottom:1.5rem">
            👤 <strong>Utilisateur simulé :</strong> <?= $_SESSION['csrf_user']['username'] ?> |
            📧 Email: <span id="display-email"><?= htmlspecialchars($_SESSION['csrf_user']['email']) ?></span> |
            💰 Solde: <strong><?= $_SESSION['csrf_user']['balance'] ?>€</strong>
            <a href="?level=<?= $level ?>&reset=1" style="margin-left:1rem; color:var(--neon-red); font-family:var(--font-mono); font-size:.75rem">[Réinitialiser]</a>
        </div>

        <?php if (!empty($message)): ?>
        <div class="alert <?= str_starts_with($message, '✅') ? 'alert-success' : 'alert-danger' ?>" style="margin-bottom:1.5rem">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <div class="lab-content">
            <div>
                <?php if ($level == '1'): ?>
                <div class="lab-panel" style="margin-bottom:1.5rem">
                    <div class="panel-header">🎯 NIVEAU 1 — Formulaire de Transfert (Sans protection CSRF)</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem">
                            Ce formulaire permet de transférer de l'argent <strong>sans token CSRF</strong>.
                            Un attaquant peut créer une page piégée qui effectue automatiquement ce transfert.
                        </p>

                        <form method="POST" action="?level=1" id="csrf-form">
                            <div class="form-group">
                                <label class="form-label">MONTANT (€)</label>
                                <input type="number" name="amount" class="form-input" value="100" min="1">
                            </div>
                            <div class="form-group">
                                <label class="form-label">DESTINATAIRE</label>
                                <input type="text" name="to" class="form-input" value="attaquant" placeholder="Compte destinataire">
                            </div>
                            <button type="submit" class="btn-submit">💸 TRANSFÉRER</button>
                        </form>
                    </div>
                </div>

                <!-- Fake Attack Page Demo -->
                <div class="lab-panel">
                    <div class="panel-header">💀 PAGE PIÉGÉE DE L'ATTAQUANT (Simulation)</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--text-secondary); margin-bottom:1rem">
                            Voici ce qu'une page malveillante pourrait contenir pour exploiter cette faille :
                        </p>
                        <code style="display:block; font-family:var(--font-mono); font-size:.8rem; color:var(--neon-red); background:rgba(0,0,0,.4); padding:1rem; border-radius:6px; border:1px solid rgba(255,51,102,.2); white-space:pre-wrap; margin-bottom:1rem">&lt;!-- Page malveillante sur attaquant.com --&gt;
&lt;h1&gt;Gagnez un iPhone gratuit ! 🎁&lt;/h1&gt;
&lt;img src="http://localhost/KaliTest/labs/csrf/?level=1" style="display:none"&gt;

&lt;!-- OU avec un formulaire auto-soumis : --&gt;
&lt;form action="http://localhost/KaliTest/labs/csrf/?level=1"
      method="POST" id="steal"&gt;
  &lt;input type="hidden" name="amount" value="1000"&gt;
  &lt;input type="hidden" name="to" value="attaquant"&gt;
&lt;/form&gt;
&lt;script&gt;document.getElementById('steal').submit();&lt;/script&gt;</code>

                        <button class="btn-submit" style="background:linear-gradient(135deg,#ff3366,#cc0033)"
                                onclick="simulateCsrf()">
                            ⚡ SIMULER L'ATTAQUE CSRF
                        </button>
                    </div>
                </div>

                <?php elseif ($level == '2'): ?>
                <div class="lab-panel">
                    <div class="panel-header">🎯 NIVEAU 2 — Changement d'Email (Sans protection)</div>
                    <div class="panel-body">
                        <form method="POST" action="?level=2">
                            <div class="form-group">
                                <label class="form-label">NOUVEL EMAIL</label>
                                <input type="email" name="email" class="form-input"
                                       placeholder="nouveau@email.com" value="attaquant@evil.com">
                            </div>
                            <button type="submit" class="btn-submit">✉️ CHANGER L'EMAIL</button>
                        </form>

                        <div style="margin-top:1rem; padding:.8rem; background:rgba(255,51,102,.05); border:1px solid rgba(255,51,102,.2); border-radius:6px; font-family:var(--font-mono); font-size:.75rem; color:var(--neon-red)">
                            ⚠️ Un attaquant peut envoyer un lien :<br>
                            <code style="color:var(--neon-yellow)">GET /labs/csrf/?level=2&email=attaquant@evil.com</code><br>
                            Si vous êtes connecté et cliquez sur ce lien, votre email est changé !
                        </div>
                    </div>
                </div>

                <?php elseif ($level == '3'): ?>
                <div class="lab-panel">
                    <div class="panel-header">🛡️ NIVEAU 3 — Avec Token CSRF (Protection)</div>
                    <div class="panel-body">
                        <p style="font-size:.85rem; color:var(--neon-green); margin-bottom:1rem">
                            ✅ Ce formulaire est protégé par un token CSRF unique. Essayez de forger une requête sans le token !
                        </p>

                        <form method="POST" action="?level=3">
                            <!-- ✅ Token CSRF de protection -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token_l3'] ?>">

                            <div class="form-group">
                                <label class="form-label">NOUVEAU MOT DE PASSE</label>
                                <input type="password" name="password" class="form-input" placeholder="Nouveau mdp">
                            </div>
                            <button type="submit" class="btn-submit">🔒 CHANGER LE MOT DE PASSE</button>
                        </form>

                        <div style="margin-top:1rem; padding:.8rem; background:rgba(0,212,255,.05); border:1px solid rgba(0,212,255,.2); border-radius:6px">
                            <p style="font-family:var(--font-mono); font-size:.75rem; color:var(--neon-blue)">// TOKEN CSRF ACTUEL (visible en mode éducatif)</p>
                            <code style="font-family:var(--font-mono); font-size:.8rem; color:var(--neon-yellow)"><?= $_SESSION['csrf_token_l3'] ?></code>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="hint-panel">
                <div class="hint-section">
                    <div class="panel-header">📖 GUIDE CSRF</div>
                </div>
                <div class="hint-section">
                    <p class="hint-title">🎯 PRINCIPE</p>
                    <p class="hint-text">
                        CSRF exploite la confiance du serveur envers le navigateur d'un utilisateur authentifié.
                        Le serveur reçoit une requête avec un cookie valide et l'exécute.
                    </p>
                </div>
                <div class="hint-section">
                    <p class="hint-title">⚡ ATTAQUE</p>
                    <p class="hint-text">
                        1. Victime connectée sur site-cible.com<br>
                        2. Victime visite attaquant.com<br>
                        3. Page malveillante envoie requête vers site-cible.com<br>
                        4. Cookie envoyé automatiquement !<br>
                        5. Action exécutée à l'insu de la victime
                    </p>
                </div>
                <div class="hint-section">
                    <p class="hint-title">🛡️ DÉFENSES</p>
                    <p class="hint-text">
                        • Tokens CSRF (SameSite + Random)<br>
                        • SameSite=Strict sur les cookies<br>
                        • Vérification de l'en-tête Origin<br>
                        • Double Submit Cookie Pattern<br>
                        • Re-authentification pour actions sensibles
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="/KaliTest/assets/js/main.js"></script>
    <script>
        function simulateCsrf() {
            showToast('Simulation CSRF lancée ! Le formulaire caché se soumet...', 'error');
            setTimeout(() => {
                const form = document.getElementById('csrf-form');
                if (form) {
                    form.querySelector('[name="amount"]').value = 999;
                    form.querySelector('[name="to"]').value = 'ATTAQUANT_CSRF';
                    form.submit();
                }
            }, 1500);
        }
    </script>
</body>
</html>
