/**
 * TrustedU ERP — Premium UX Enhancements
 * Sidebar accordion + smooth interactions
 */
(function() {
    'use strict';

    // ── Sidebar Accordion ──
    function initAccordion() {
        const sidebar = document.querySelector('.fi-sidebar');
        if (!sidebar || sidebar.dataset.accordionReady) return;
        sidebar.dataset.accordionReady = '1';

        sidebar.addEventListener('click', function(e) {
            const btn = e.target.closest('.fi-sidebar-group-button');
            if (!btn) return;
            const allBtns = sidebar.querySelectorAll('.fi-sidebar-group-button');
            allBtns.forEach(function(other) {
                if (other !== btn && other.getAttribute('aria-expanded') === 'true') {
                    other.click();
                }
            });
        });
    }

    // ── KPI Card hover glow ──
    function initCardEffects() {
        document.querySelectorAll('.kpi-card').forEach(function(card) {
            if (card.dataset.fxReady) return;
            card.dataset.fxReady = '1';
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 8px 30px rgba(0,0,0,0.08)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '';
            });
        });
    }

    // ── Initialize ──
    function init() {
        initAccordion();
        initCardEffects();
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Re-init after Livewire navigation
    document.addEventListener('livewire:navigated', function() {
        setTimeout(init, 200);
    });

    // Fallback for older Livewire
    if (typeof window.Livewire !== 'undefined') {
        try {
            window.Livewire.hook('morph.updated', function() {
                setTimeout(init, 200);
            });
        } catch(e) {}
    }
})();
