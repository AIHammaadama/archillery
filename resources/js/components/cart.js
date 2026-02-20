/**
 * Alpine.js Cart Component for Procurement Requests
 * Provides cart-style interface for adding materials to requests
 * with persistence using Alpine Persist
 */

export default () => ({
    items: [],
    total: 0,
    projectId: null,

    init() {
        // Initialize cart for current project
        this.$watch('items', () => {
            this.calculateTotal();
            this.persistCart();
        });

        // Load persisted cart if exists
        this.loadCart();
    },

    /**
     * Add material to cart
     */
    addItem(material) {
        const existingItem = this.items.find(item => item.material_id === material.id);

        if (existingItem) {
            existingItem.quantity++;
        } else {
            this.items.push({
                material_id: material.id,
                material_code: material.code,
                material_name: material.name,
                category: material.category,
                unit_of_measurement: material.unit_of_measurement,
                quantity: 1,
                estimated_unit_price: 0,
                estimated_total: 0,
                notes: ''
            });
        }

        this.showNotification('Item added to cart', 'success');
    },

    /**
     * Remove item from cart
     */
    removeItem(index) {
        this.items.splice(index, 1);
        this.showNotification('Item removed from cart', 'info');
    },

    /**
     * Update item quantity
     */
    updateQuantity(index, quantity) {
        if (quantity < 1) {
            this.removeItem(index);
            return;
        }

        this.items[index].quantity = parseInt(quantity);
        this.calculateItemTotal(index);
    },

    /**
     * Update estimated price (Site Managers enter estimates, not actual prices)
     */
    updateEstimatedPrice(index, price) {
        this.items[index].estimated_unit_price = parseFloat(price) || 0;
        this.calculateItemTotal(index);
    },

    /**
     * Calculate item total
     */
    calculateItemTotal(index) {
        const item = this.items[index];
        item.estimated_total = item.quantity * item.estimated_unit_price;
    },

    /**
     * Calculate cart total
     */
    calculateTotal() {
        this.total = this.items.reduce((sum, item) => {
            return sum + (item.quantity * item.estimated_unit_price);
        }, 0);
    },

    /**
     * Clear cart
     */
    clearCart() {
        if (confirm('Are you sure you want to clear the cart?')) {
            this.items = [];
            this.total = 0;
            this.showNotification('Cart cleared', 'info');
        }
    },

    /**
     * Get cart item count
     */
    get itemCount() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    },

    /**
     * Check if cart is empty
     */
    get isEmpty() {
        return this.items.length === 0;
    },

    /**
     * Persist cart to localStorage
     */
    persistCart() {
        if (this.projectId) {
            localStorage.setItem(`cart_project_${this.projectId}`, JSON.stringify({
                items: this.items,
                total: this.total,
                timestamp: new Date().toISOString()
            }));
        }
    },

    /**
     * Load cart from localStorage
     */
    loadCart() {
        if (this.projectId) {
            const stored = localStorage.getItem(`cart_project_${this.projectId}`);
            if (stored) {
                const data = JSON.parse(stored);
                this.items = data.items || [];
                this.calculateTotal();
            }
        }
    },

    /**
     * Clear persisted cart
     */
    clearPersistedCart() {
        if (this.projectId) {
            localStorage.removeItem(`cart_project_${this.projectId}`);
        }
    },

    /**
     * Show notification (integrates with notifications component)
     */
    showNotification(message, type = 'info') {
        window.dispatchEvent(new CustomEvent('show-notification', {
            detail: { message, type }
        }));
    },

    /**
     * Prepare data for submission
     */
    prepareForSubmission() {
        return {
            project_id: this.projectId,
            items: this.items.map(item => ({
                material_id: item.material_id,
                quantity: item.quantity,
                estimated_unit_price: item.estimated_unit_price,
                notes: item.notes
            })),
            total_estimated_amount: this.total
        };
    },

    /**
     * Format currency (Nigerian Naira)
     */
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-NG', {
            style: 'currency',
            currency: 'NGN',
            minimumFractionDigits: 2
        }).format(amount);
    }
});
