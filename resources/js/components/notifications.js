/**
 * Alpine.js Notifications Component
 * Toast notifications and notification bell dropdown
 */

export default () => ({
    // Toast notifications
    toasts: [],
    toastId: 0,

    // Notification bell
    notifications: [],
    unreadCount: 0,
    loading: false,
    dropdownOpen: false,

    init() {
        // Listen for custom notification events
        window.addEventListener("show-notification", (event) => {
            this.showToast(event.detail.message, event.detail.type || "info");
        });

        // Load notifications on init if user is authenticated
        if (this.isAuthenticated()) {
            this.loadNotifications();

            // Poll for new notifications every 60 seconds
            setInterval(() => {
                this.loadNotifications();
            }, 60000);
        }
    },

    /**
     * Show toast notification
     */
    showToast(message, type = "info", duration = 4000) {
        const id = ++this.toastId;
        const toast = {
            id,
            message,
            type, // success, error, warning, info
            visible: false,
        };

        this.toasts.push(toast);

        // Trigger animation
        setTimeout(() => {
            const toastElement = this.toasts.find((t) => t.id === id);
            if (toastElement) toastElement.visible = true;
        }, 10);

        // Auto-hide after duration
        setTimeout(() => {
            this.hideToast(id);
        }, duration);
    },

    /**
     * Hide toast
     */
    hideToast(id) {
        const toast = this.toasts.find((t) => t.id === id);
        if (toast) {
            toast.visible = false;

            // Remove from array after animation
            setTimeout(() => {
                this.toasts = this.toasts.filter((t) => t.id !== id);
            }, 300);
        }
    },

    /**
     * Get toast icon based on type
     */
    getToastIcon(type) {
        const icons = {
            success: '<i class="bi bi-check-circle-fill"></i>',
            error: '<i class="bi bi-x-circle-fill"></i>',
            warning: '<i class="bi bi-exclamation-triangle-fill"></i>',
            info: '<i class="bi bi-info-circle-fill"></i>',
        };
        return icons[type] || icons.info;
    },

    /**
     * Get toast CSS classes
     */
    getToastClass(type) {
        const classes = {
            success: "bg-success text-white",
            error: "bg-danger text-white",
            warning: "bg-warning text-dark",
            info: "bg-info text-white",
        };
        return classes[type] || classes.info;
    },

    /**
     * Load notifications from server
     */
    async loadNotifications() {
        if (this.loading) return;

        this.loading = true;
        try {
            const response = await fetch("/api/notifications", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            });

            if (response.ok) {
                const data = await response.json();
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
            }
        } catch (error) {
            console.error("Failed to load notifications:", error);
        } finally {
            this.loading = false;
        }
    },

    /**
     * Mark notification as read
     */
    async markAsRead(notificationId) {
        try {
            const response = await fetch(
                `/api/notifications/${notificationId}/read`,
                {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": this.getCsrfToken(),
                        Accept: "application/json",
                    },
                }
            );

            if (response.ok) {
                // Update local state
                const notification = this.notifications.find(
                    (n) => n.id === notificationId
                );
                if (notification) {
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            }
        } catch (error) {
            console.error("Failed to mark notification as read:", error);
        }
    },

    /**
     * Mark all notifications as read
     */
    async markAllAsRead() {
        try {
            const response = await fetch("/api/notifications/read-all", {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": this.getCsrfToken(),
                    Accept: "application/json",
                },
            });

            if (response.ok) {
                this.notifications.forEach((n) => {
                    n.read_at = new Date().toISOString();
                });
                this.unreadCount = 0;
            }
        } catch (error) {
            console.error("Failed to mark all as read:", error);
        }
    },

    /**
     * Toggle dropdown
     */
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;

        // Load notifications when opening dropdown
        if (this.dropdownOpen && this.notifications.length === 0) {
            this.loadNotifications();
        }
    },

    /**
     * Get notification icon based on type
     */
    getNotificationIcon(notification) {
        // Parse notification data to determine icon
        const type = notification.type || "";

        if (type.includes("Request")) {
            return "bi-file-earmark-text";
        } else if (type.includes("Project")) {
            return "bi-folder";
        } else if (type.includes("Delivery")) {
            return "bi-truck";
        } else if (type.includes("Approval")) {
            return "bi-check-circle";
        }

        return "bi-bell";
    },

    /**
     * Format time ago
     */
    timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        const intervals = {
            year: 31536000,
            month: 2592000,
            week: 604800,
            day: 86400,
            hour: 3600,
            minute: 60,
        };

        for (const [unit, secondsInUnit] of Object.entries(intervals)) {
            const interval = Math.floor(seconds / secondsInUnit);
            if (interval >= 1) {
                return `${interval} ${unit}${interval > 1 ? "s" : ""} ago`;
            }
        }

        return "Just now";
    },

    /**
     * Check if user is authenticated
     */
    isAuthenticated() {
        return document.querySelector('meta[name="user-id"]') !== null;
    },

    /**
     * Get CSRF token
     */
    getCsrfToken() {
        return (
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || ""
        );
    },
});
