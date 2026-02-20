/**
 * Alpine.js Theme Toggle Component
 * Dark/Light mode switcher with localStorage persistence
 */

export default () => ({
    theme: "light",

    init() {
        // Load theme from localStorage, default to light mode
        this.theme = this.getStoredTheme() || "light";
        this.applyTheme(this.theme);
    },

    /**
     * Toggle between light and dark theme
     */
    toggle() {
        this.theme = this.theme === "light" ? "dark" : "light";
        this.applyTheme(this.theme);
        this.storeTheme(this.theme);

        // Show notification
        window.dispatchEvent(
            new CustomEvent("show-notification", {
                detail: {
                    message: `${
                        this.theme === "dark" ? "Dark" : "Light"
                    } mode enabled`,
                    type: "info",
                },
            })
        );
    },

    /**
     * Set specific theme
     */
    setTheme(theme) {
        if (theme !== "light" && theme !== "dark") return;

        this.theme = theme;
        this.applyTheme(theme);
        this.storeTheme(theme);
    },

    /**
     * Apply theme to document
     */
    applyTheme(theme) {
        document.documentElement.setAttribute("data-theme", theme);

        // Also update Bootstrap classes if needed
        if (theme === "dark") {
            document.body.classList.add("dark-mode");
            document.body.classList.remove("light-mode");
        } else {
            document.body.classList.add("light-mode");
            document.body.classList.remove("dark-mode");
        }

        // Update meta theme-color for mobile browsers
        const metaThemeColor = document.querySelector(
            'meta[name="theme-color"]'
        );
        if (metaThemeColor) {
            metaThemeColor.setAttribute(
                "content",
                theme === "dark" ? "#1e1e2e" : "#ffffff"
            );
        }

        // Dispatch event for other components that might need to react
        window.dispatchEvent(
            new CustomEvent("theme-changed", {
                detail: { theme },
            })
        );
    },

    /**
     * Get stored theme from localStorage
     */
    getStoredTheme() {
        return localStorage.getItem("theme");
    },

    /**
     * Store theme in localStorage
     */
    storeTheme(theme) {
        localStorage.setItem("theme", theme);
    },

    /**
     * Get preferred theme from system
     */
    getPreferredTheme() {
        if (
            window.matchMedia &&
            window.matchMedia("(prefers-color-scheme: dark)").matches
        ) {
            return "dark";
        }
        return "light";
    },

    /**
     * Check if current theme is dark
     */
    get isDark() {
        return this.theme === "dark";
    },

    /**
     * Check if current theme is light
     */
    get isLight() {
        return this.theme === "light";
    },

    /**
     * Get icon for current theme
     */
    get icon() {
        return this.theme === "dark" ? "bi-moon-stars-fill" : "bi-sun-fill";
    },

    /**
     * Get label for toggle button
     */
    get label() {
        return this.theme === "dark"
            ? "Switch to Light Mode"
            : "Switch to Dark Mode";
    },
});
