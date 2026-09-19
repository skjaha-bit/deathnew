<div id="fyToast" class="fy-toast" role="status" aria-live="polite"></div>
<style>
    .fy-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        min-width: 220px;
        max-width: 360px;
        padding: .75rem 1rem;
        border-radius: 12px;
        color: #fff;
        font-weight: 600;
        font-size: .9rem;
        box-shadow: 0 8px 24px rgba(0,0,0,.18);
        opacity: 0;
        transform: translateY(12px);
        pointer-events: none;
        transition: opacity .2s ease, transform .2s ease;
        z-index: 1080;
    }
    .fy-toast.show { opacity: 1; transform: translateY(0); }
    .fy-toast.success { background: var(--fy-teal-500, #2f7a6c); }
    .fy-toast.error { background: #c0392b; }
</style>
<script>
    function showToast(message, type) {
        var toast = document.getElementById('fyToast');
        if (!toast) return;
        toast.textContent = message;
        toast.className = 'fy-toast show ' + (type || 'success');
        clearTimeout(window.__fyToastTimer);
        window.__fyToastTimer = setTimeout(function () {
            toast.className = 'fy-toast';
        }, 3000);
    }
</script>
