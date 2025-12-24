/**
 * Logs Page Script
 * System logs viewer with auto-refresh
 */

console.log("Logs.js loaded");

// ============================================
// TOAST NOTIFICATION (Lightweight version)
// ============================================
class ToastNotification {
    constructor() {
        this.container = this.createContainer();
        this.activeToasts = new Set();
    }

    createContainer() {
        const existing = document.getElementById('toast-container');
        if (existing) return existing;

        const container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
        return container;
    }

    show(message, type = 'info', duration = 4000) {
        const toast = this.createToast(message, type);
        this.container.appendChild(toast);
        this.activeToasts.add(toast);

        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        setTimeout(() => {
            this.remove(toast);
        }, duration);

        return toast;
    }

    createToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        const icon = icons[type] || icons.info;

        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${message}</span>
            <i class="fas fa-times"></i>
        `;

        toast.addEventListener('click', () => this.remove(toast));

        return toast;
    }

    remove(toast) {
        if (!this.activeToasts.has(toast)) return;

        toast.classList.remove('show');
        toast.classList.add('hide');

        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
            this.activeToasts.delete(toast);
        }, 300);
    }

    success(message, duration) {
        return this.show(message, 'success', duration);
    }

    error(message, duration) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration) {
        return this.show(message, 'info', duration);
    }

    confirm(message, onConfirm, onCancel) {
        const modal = this.createConfirmModal(message, onConfirm, onCancel);
        document.body.appendChild(modal);

        requestAnimationFrame(() => {
            modal.classList.add('show');
        });
    }

    createConfirmModal(message, onConfirm, onCancel) {
        const modal = document.createElement('div');
        modal.className = 'confirm-modal';

        modal.innerHTML = `
            <div class="confirm-box">
                <div class="confirm-message">
                    <i class="fas fa-question-circle"></i>
                    <span>${message}</span>
                </div>
                <div class="confirm-buttons">
                    <button class="btn-cancel">Hủy</button>
                    <button class="btn-confirm">Xác nhận</button>
                </div>
            </div>
        `;

        const closeModal = () => {
            modal.classList.remove('show');
            setTimeout(() => modal.remove(), 300);
        };

        modal.querySelector('.btn-confirm').addEventListener('click', () => {
            closeModal();
            if (onConfirm) onConfirm();
        });

        modal.querySelector('.btn-cancel').addEventListener('click', () => {
            closeModal();
            if (onCancel) onCancel();
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        return modal;
    }
}

// Global toast instance
const Toast = new ToastNotification();

// ============================================
// LOGS MANAGER
// ============================================
class LogsManager {
    constructor() {
        this.logsTableBody = document.getElementById('logs');
        this.btnClearLogs = document.getElementById('btnClearLogs');
        this.refreshInterval = null;
        this.isLoading = false;
    }

    init() {
        console.log("🚀 Initializing Logs Manager...");
        
        this.setupEventListeners();
        this.loadLogs();
        this.startAutoRefresh(3000); // Refresh every 3 seconds

        console.log("✅ Logs Manager ready!");
    }

    setupEventListeners() {
        if (this.btnClearLogs) {
            this.btnClearLogs.onclick = () => {
                Toast.confirm("XÓA TOÀN BỘ LOGS?", () => {
                    this.clearLogs();
                });
            };
        } else {
            console.error("❌ Button btnClearLogs not found!");
        }
    }

    async loadLogs() {
        if (this.isLoading) return;
        this.isLoading = true;

        try {
            const response = await fetch("/iot-dashboar/api/get_logs.php?limit=50&ts=" + Date.now());
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            this.renderLogs(data);

        } catch (error) {
            console.error("❌ Failed to load logs:", error);
            this.showError("Không thể tải logs");
        } finally {
            this.isLoading = false;
        }
    }

    renderLogs(logs) {
        if (!this.logsTableBody) {
            console.error("❌ Table body #logs not found!");
            return;
        }

        if (!logs || logs.length === 0) {
            this.logsTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted" style="padding: 20px;">
                        <i class="fas fa-inbox mr-2"></i>
                        Chưa có logs
                    </td>
                </tr>
            `;
            return;
        }

        let html = "";
        logs.forEach(log => {
            let rowStyle = "";
            let levelBadge = "";

            // Style based on level
            switch (log.level) {
                case "FIRE":
                case "ERROR":
                    rowStyle = "style='background-color: #f8d7da; color: #721c24; font-weight: bold;'";
                    levelBadge = `<span class="badge badge-danger">${log.level}</span>`;
                    break;
                case "WARNING":
                    rowStyle = "style='background-color: #fff3cd; color: #856404;'";
                    levelBadge = `<span class="badge badge-warning">${log.level}</span>`;
                    break;
                case "INFO":
                    levelBadge = `<span class="badge badge-info">${log.level}</span>`;
                    break;
                case "SUCCESS":
                    levelBadge = `<span class="badge badge-success">${log.level}</span>`;
                    break;
                default:
                    levelBadge = `<span class="badge badge-secondary">${log.level}</span>`;
            }

            html += `
                <tr ${rowStyle}>
                    <td style="white-space: nowrap;">${log.created_at}</td>
                    <td>${log.source}</td>
                    <td><code style="font-size: 11px;">${log.topic}</code></td>
                    <td>${levelBadge}</td>
                    <td>${this.escapeHtml(log.message)}</td>
                </tr>
            `;
        });

        this.logsTableBody.innerHTML = html;
    }

    showError(message) {
        if (this.logsTableBody) {
            this.logsTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger" style="padding: 20px;">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        ${message}
                    </td>
                </tr>
            `;
        }
    }

    async clearLogs() {
        try {
            const response = await fetch("/iot-dashboar/api/clear_logs.php", {
                method: "POST"
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const result = await response.text();
            console.log("Clear logs response:", result);

            Toast.success("Đã xóa toàn bộ logs");
            
            // Reload logs after clearing
            setTimeout(() => {
                this.loadLogs();
            }, 500);

        } catch (error) {
            console.error("❌ Failed to clear logs:", error);
            Toast.error("Xóa logs thất bại!");
        }
    }

    startAutoRefresh(interval) {
        // Clear existing interval if any
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }

        // Start new interval
        this.refreshInterval = setInterval(() => {
            this.loadLogs();
        }, interval);

        console.log(`🔄 Auto-refresh enabled (${interval}ms)`);
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
            console.log("⏸️ Auto-refresh stopped");
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// ============================================
// INITIALIZE ON DOM READY
// ============================================
document.addEventListener("DOMContentLoaded", function() {
    const logsManager = new LogsManager();
    logsManager.init();

    // Export for debugging
    window.LogsManager = logsManager;
    window.Toast = Toast;

    console.log("✅ Logs page initialized!");
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.LogsManager) {
        window.LogsManager.stopAutoRefresh();
    }
});