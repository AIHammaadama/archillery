import './bootstrap';

// Import Alpine.js
import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';

// Import HTMX
import htmx from 'htmx.org';

// Import Alpine components
import cart from './components/cart';
import notifications from './components/notifications';
import themeToggle from './components/theme-toggle';

// Register Alpine Persist plugin
Alpine.plugin(persist);

// Register Alpine components
Alpine.data('cart', cart);
Alpine.data('notifications', notifications);
Alpine.data('themeToggle', themeToggle);

// Make Alpine and HTMX available globally
window.Alpine = Alpine;
window.htmx = htmx;

// Start Alpine
Alpine.start();

// Configure HTMX
htmx.config.timeout = 30000; // 30 seconds timeout
htmx.config.defaultSwapStyle = 'innerHTML';
htmx.config.defaultSwapDelay = 100;
htmx.config.scrollIntoViewOnBoost = false;

// HTMX event listeners for global behaviors
document.addEventListener('htmx:configRequest', (event) => {
    // Add CSRF token to all HTMX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        event.detail.headers['X-CSRF-TOKEN'] = csrfToken;
    }
});

document.addEventListener('htmx:afterSwap', (event) => {
    // Reinitialize tooltips, popovers, etc. after HTMX swap
    if (typeof window.initializeTooltips === 'function') {
        window.initializeTooltips();
    }
});

document.addEventListener('htmx:responseError', (event) => {
    // Show error notification
    window.dispatchEvent(new CustomEvent('show-notification', {
        detail: {
            message: 'An error occurred. Please try again.',
            type: 'error'
        }
    }));
});

document.addEventListener('htmx:sendError', (event) => {
    // Show network error notification
    window.dispatchEvent(new CustomEvent('show-notification', {
        detail: {
            message: 'Network error. Please check your connection.',
            type: 'error'
        }
    }));
});
