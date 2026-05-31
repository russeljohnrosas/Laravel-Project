// Toast notification system - auto-dismiss, top-right position
(function () {
    'use strict';

    const DURATION = 3000; // ms before auto-dismiss

    const TYPE_MAP = {
        success: {
            alertClass: 'alert-success',
            icon:  '<i class="ti ti-circle-check"></i>',
            label: 'Success',
        },
        error: {
            alertClass: 'alert-danger',
            icon:  '<i class="ti ti-circle-x"></i>',
            label: 'Error',
        },
        warning: {
            alertClass: 'alert-warning',
            icon:  '<i class="ti ti-alert-triangle"></i>',
            label: 'Warning',
        },
        info: {
            alertClass: 'alert-info',
            icon:  '<i class="ti ti-info-circle"></i>',
            label: 'Info',
        },
    };

    // Create or return the toast container div
    function getContainer() {
        let el = document.getElementById('bsToastContainer');
        if (!el) {
            el = document.createElement('div');
            el.id = 'bsToastContainer';
            el.style.cssText = [
                'position:fixed',
                'top:1.25rem',
                'right:1.25rem',
                'z-index:9999',
                'display:flex',
                'flex-direction:column',
                'gap:.5rem',
                'pointer-events:none',
                'width:320px',
                'max-width:calc(100vw - 2rem)',
            ].join(';');
            document.body.appendChild(el);
        }
        return el;
    }

    // Build a single toast element
    function buildToast(message, cfg) {
        const el = document.createElement('div');
        el.setAttribute('role', 'alert');
        el.className = `alert ${cfg.alertClass} d-flex align-items-start gap-2 mb-0 shadow-sm bs-toast`;
        el.style.cssText = 'pointer-events:all;animation:toastIn .25s ease;border-radius:10px;font-size:.875rem;';
        el.innerHTML = `
            <span class="bs-toast-icon flex-shrink-0" style="font-size:1rem;margin-top:.05rem;">${cfg.icon}</span>
            <div class="flex-grow-1">
                <div style="font-weight:700;font-size:.8rem;margin-bottom:.1rem;">${cfg.label}</div>
                <div>${escapeHtml(message)}</div>
            </div>
            <button type="button"
                    style="background:none;border:none;padding:0;cursor:pointer;opacity:.6;font-size:.8rem;margin-top:.1rem;flex-shrink:0;"
                    onclick="this.closest('.bs-toast').__dismiss()">
                <i class="ti ti-x"></i>
            </button>`;
        return el;
    }

    // Dismiss a toast with a fade-out animation
    function dismiss(el) {
        if (el.__dismissed) return;
        el.__dismissed = true;
        el.style.animation = 'toastOut .3s ease forwards';
        el.addEventListener('animationend', () => el.remove(), { once: true });
    }

    // Escape HTML to prevent XSS in toast messages
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Inject the keyframe animations into the page once
    function ensureStyles() {
        if (document.getElementById('bsToastStyles')) return;
        const s = document.createElement('style');
        s.id = 'bsToastStyles';
        s.textContent = `
            @keyframes toastIn  { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
            @keyframes toastOut { from { opacity:1; transform:translateY(0);     } to { opacity:0; transform:translateY(-6px); } }
        `;
        document.head.appendChild(s);
    }

    // Public function - call showToast('message', 'success'|'error'|'warning'|'info')
    window.showToast = function (message, type) {
        if (!message) return;
        ensureStyles();

        const cfg  = TYPE_MAP[type] ?? TYPE_MAP.success;
        const el   = buildToast(message, cfg);
        el.__dismiss = () => dismiss(el);

        getContainer().appendChild(el);

        const timer = setTimeout(() => dismiss(el), DURATION);

        // Pause auto-dismiss while hovering
        el.addEventListener('mouseenter', () => clearTimeout(timer));
        el.addEventListener('mouseleave', () => setTimeout(() => dismiss(el), 1000));
    };

})();
