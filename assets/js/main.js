// ============================================================
// CyberLab - Main JavaScript
// ============================================================

// ── Matrix Rain ─────────────────────────────────────────────
function initMatrix() {
    const canvas = document.getElementById('matrix-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノ{}[]<>/?;:ABCDEF0123456789';
    const charArr = chars.split('');
    const fontSize = 14;
    const cols = Math.floor(canvas.width / fontSize);
    const drops = Array(cols).fill(1);

    function draw() {
        ctx.fillStyle = 'rgba(5, 5, 8, 0.05)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#00ff88';
        ctx.font = `${fontSize}px 'Share Tech Mono', monospace`;

        for (let i = 0; i < drops.length; i++) {
            const char = charArr[Math.floor(Math.random() * charArr.length)];
            ctx.fillText(char, i * fontSize, drops[i] * fontSize);
            if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) drops[i] = 0;
            drops[i]++;
        }
    }

    setInterval(draw, 50);
    window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });
}

// ── Navbar Clock ─────────────────────────────────────────────
function initClock() {
    const el = document.getElementById('nav-time');
    if (!el) return;
    function updateClock() {
        const now = new Date();
        const time = now.toUTCString().split(' ')[4];
        el.textContent = `SYS: ONLINE | UTC ${time}`;
    }
    updateClock();
    setInterval(updateClock, 1000);
}

// ── Terminal Typewriter ──────────────────────────────────────
function initTerminal() {
    const cmds = document.querySelectorAll('.t-cmd[data-text]');
    cmds.forEach(cmd => {
        const text = cmd.dataset.text;
        let i = 0;
        const type = () => {
            if (i < text.length) {
                cmd.textContent += text[i++];
                setTimeout(type, 60 + Math.random() * 40);
            }
        };
        setTimeout(type, 800);
    });
}

// ── Copy Hint Codes ──────────────────────────────────────────
function initHintCopy() {
    document.querySelectorAll('.hint-code').forEach(el => {
        el.title = 'Cliquer pour copier dans le formulaire';
        el.addEventListener('click', () => {
            const target = document.querySelector('.form-input, .form-textarea');
            if (target) {
                target.value = el.textContent;
                target.focus();
                showToast('Payload copié !');
            }
        });
    });
}

// ── Toast Notification ───────────────────────────────────────
function showToast(msg, type = 'success') {
    const toast = document.createElement('div');
    const colors = { success: '#00ff88', error: '#ff3366', info: '#00d4ff' };
    toast.style.cssText = `
        position:fixed; bottom:2rem; right:2rem; z-index:9999;
        background:#0d0d1a; border:1px solid ${colors[type] || colors.success};
        color:${colors[type] || colors.success}; font-family:'Share Tech Mono',monospace;
        font-size:.85rem; padding:.8rem 1.5rem; border-radius:8px;
        box-shadow:0 0 30px rgba(0,255,136,.2);
        animation:fadeInUp .3s ease; transition:all .3s;
    `;
    toast.textContent = `[INFO] ${msg}`;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// ── Smooth Scroll ────────────────────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        document.querySelector(a.getAttribute('href'))?.scrollIntoView({ behavior: 'smooth' });
    });
});

// ── Intersection Observer for Animations ─────────────────────
function initObserver() {
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp .5s ease forwards';
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.lab-card, .tool-card').forEach(el => {
        el.style.opacity = '0';
        observer.observe(el);
    });
}

// ── Scan Effect on Card Hover ─────────────────────────────────
function initCardEffects() {
    document.querySelectorAll('.lab-card').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            const glow = card.querySelector('.lab-card-glow');
            if (glow) {
                glow.style.background = `radial-gradient(circle at ${x}% ${y}%, rgba(0,255,136,0.08) 0%, transparent 60%)`;
            }
        });
    });
}

// ── Attack Log Terminal ───────────────────────────────────────
function logAttack(type, payload) {
    const logEl = document.getElementById('attack-log');
    if (!logEl) return;
    const timestamp = new Date().toLocaleTimeString();
    const entry = document.createElement('p');
    entry.style.cssText = 'margin:.2rem 0; color:#00ff88; font-family:monospace; font-size:.8rem;';
    entry.innerHTML = `<span style="color:#666">[${timestamp}]</span> <span style="color:#ff3366">[${type}]</span> ${payload}`;
    logEl.prepend(entry);
}

// ── Init All ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initMatrix();
    initClock();
    initTerminal();
    initHintCopy();
    initCardEffects();
    // initObserver(); // Uncomment for fade-in animations on scroll
});
