// resources/js/app.js
import './bootstrap';

// ── CSRF token for all fetch() calls ─────────────────────────
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

window.fetchWithCsrf = (url, options = {}) => fetch(url, {
    ...options,
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        ...options.headers,
    },
});

// ── Auto-dismiss alerts after 5 s ────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert.alert-dismissible').forEach(el => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert?.close();
        }, 5000);
    });
});

// ── Confirm delete forms ──────────────────────────────────────
document.addEventListener('submit', (e) => {
    const form = e.target;
    const msg  = form.dataset.confirm;
    if (msg && !confirm(msg)) {
        e.preventDefault();
    }
});

// ── Mobile sidebar toggle ─────────────────────────────────────
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar')?.classList.toggle('show');
});

// ── Close sidebar on outside click (mobile) ──────────────────
document.addEventListener('click', (e) => {
    const sidebar = document.getElementById('sidebar');
    const toggle  = document.getElementById('sidebarToggle');
    if (sidebar?.classList.contains('show') &&
        !sidebar.contains(e.target) &&
        !toggle?.contains(e.target)) {
        sidebar.classList.remove('show');
    }
});
