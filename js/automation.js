/**
 * Automation Rules - JavaScript (FIXED)
 * Smart Home HCMUT
 */

// ============================================
// GLOBAL VARIABLES
// ============================================
let allRules = [];
let refreshInterval = null;
let logsRefreshInterval = null;
const REFRESH_INTERVAL = 5000;  // 5 seconds (giảm từ 1s để tránh quá tải)
const LOGS_REFRESH_INTERVAL = 10000;  // 10 seconds

// API Base URL 
const API_BASE = '/iot-dashboar/api';

// ============================================
// TOAST NOTIFICATION SYSTEM
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
window.Toast = Toast;

// ============================================
// CONSTANTS
// ============================================
const triggerHelps = {
    time: '⏰ Nhập thời gian theo format <strong>HH:MM</strong> (VD: 18:00, 06:30)',
    temperature: '🌡️ Nhập nhiệt độ (°C). VD: 30 nghĩa là khi nhiệt độ > 30°C',
    humidity: '💧 Nhập độ ẩm (%). VD: 80 nghĩa là khi độ ẩm > 80%',
    motion: '🚶 Giá trị: <strong>detected</strong> (có người) hoặc <strong>none</strong> (không có)',
    light: '💡 Giá trị: <strong>dark</strong> (tối) hoặc <strong>bright</strong> (sáng)',
    fire: '🔥 Giá trị: <strong>detected</strong> (phát hiện cháy)'
};

const triggerIcons = {
    time: 'fa-clock',
    temperature: 'fa-thermometer-half',
    humidity: 'fa-tint',
    motion: 'fa-walking',
    light: 'fa-sun',
    fire: 'fa-fire'
};

const triggerLabels = {
    time: 'Thời gian',
    temperature: 'Nhiệt độ',
    humidity: 'Độ ẩm',
    motion: 'Chuyển động',
    light: 'Ánh sáng',
    fire: 'Cháy'
};

const deviceLabels = {
    light1: 'Đèn 1',
    light2: 'Đèn 2',
    light3: 'Đèn 3',
    light4: 'Đèn 4',
    all_lights: 'Tất cả đèn',
    door: 'Cửa',
    buzzer: 'Còi'
};

// ============================================
// INITIALIZATION
// ============================================
$(document).ready(function() {
    loadRules();
    loadLogs();
    startAutoRefresh();
    document.addEventListener('visibilitychange', handleVisibilityChange);
});

// ============================================
// TRIGGER TYPE CHANGE HANDLER
// ============================================
function onTriggerTypeChange() {
    const type = $('#triggerType').val();
    
    if (type && triggerHelps[type]) {
        $('#triggerHelp').html('<i class="fas fa-info-circle mr-2"></i>' + triggerHelps[type]);
        $('#triggerHelp').removeClass('d-none');
    } else {
        $('#triggerHelp').addClass('d-none');
    }
    
    if (['motion', 'light', 'fire', 'time'].includes(type)) {
        $('#triggerOperator').val('=').prop('disabled', true);
    } else {
        $('#triggerOperator').prop('disabled', false);
    }
    
    const placeholders = {
        time: 'VD: 18:00',
        temperature: 'VD: 30',
        humidity: 'VD: 80',
        motion: 'detected hoặc none',
        light: 'dark hoặc bright',
        fire: 'detected'
    };
    $('#triggerValue').attr('placeholder', placeholders[type] || '');
    
    handleAutoRevertForTriggerType(type);
}

function handleAutoRevertForTriggerType(type) {
    const $autoRevert = $('#autoRevert');
    const $warning = $('#autoRevertWarning');
    
    if (type === 'fire') {
        $warning.slideDown();
        $autoRevert.prop('checked', false);
        $autoRevert.closest('.custom-control').addClass('opacity-50');
    } else {
        $warning.slideUp();
        $autoRevert.closest('.custom-control').removeClass('opacity-50');
        
        if (type === 'motion' || type === 'light') {
            $autoRevert.prop('checked', true);
        }
    }
}

// ============================================
// AUTO-REFRESH SYSTEM
// ============================================
function startAutoRefresh() {
    stopAutoRefresh();
    
    refreshInterval = setInterval(function() {
        loadRules(true);
    }, REFRESH_INTERVAL);
    
    logsRefreshInterval = setInterval(function() {
        loadLogs(true);
    }, LOGS_REFRESH_INTERVAL);
    
    console.log('🔄 Auto-refresh started');
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
        refreshInterval = null;
    }
    if (logsRefreshInterval) {
        clearInterval(logsRefreshInterval);
        logsRefreshInterval = null;
    }
    console.log('⏸️ Auto-refresh stopped');
}

function handleVisibilityChange() {
    if (document.hidden) {
        stopAutoRefresh();
    } else {
        startAutoRefresh();
        loadRules(true);
        loadLogs(true);
    }
}

// ============================================
// LOAD RULES
// ============================================
function loadRules(silent = false) {
    if (!silent) {
        $('#rulesContainer').html(
            '<div class="loading-state">' +
            '<i class="fas fa-spinner fa-spin fa-2x"></i>' +
            '<p class="mt-3 text-muted">Đang tải...</p>' +
            '</div>'
        );
    }
    
    $.get(API_BASE + '/get_automation_rules.php')
        .done(function(data) {
            if (!silent || hasRulesChanged(data)) {
                const oldRules = allRules;
                allRules = data;
                renderRules(data, oldRules);
                updateStats(data);
                
                if (silent && hasRulesChanged(data)) {
                    showUpdateIndicator();
                }
            }
        })
        .fail(function(xhr) {
            if (!silent) {
                console.error('Failed to load rules:', xhr);
                $('#rulesContainer').html(
                    '<div class="alert alert-danger">' +
                    '<i class="fas fa-exclamation-triangle mr-2"></i>' +
                    'Không thể tải danh sách rules. Vui lòng thử lại.' +
                    '</div>'
                );
            }
        });
}

function hasRulesChanged(newRules) {
    if (allRules.length !== newRules.length) {
        return true;
    }
    
    for (let i = 0; i < newRules.length; i++) {
        const oldRule = allRules.find(r => r.id === newRules[i].id);
        if (!oldRule) return true;
        
        if (oldRule.trigger_count !== newRules[i].trigger_count ||
            oldRule.is_active !== newRules[i].is_active ||
            oldRule.last_triggered !== newRules[i].last_triggered) {
            return true;
        }
    }
    
    return false;
}

function showUpdateIndicator() {
    let $indicator = $('#updateIndicator');
    
    if ($indicator.length === 0) {
        $indicator = $('<div id="updateIndicator" class="update-indicator">' +
            '<i class="fas fa-sync-alt"></i> Đã cập nhật' +
            '</div>');
        $('body').append($indicator);
    }
    
    $indicator.addClass('show');
    
    setTimeout(function() {
        $indicator.removeClass('show');
    }, 2000);
}

// ============================================
// RENDER RULES
// ============================================
function renderRules(rules, oldRules = []) {
    if (rules.length === 0) {
        $('#rulesContainer').html(
            '<div class="empty-state">' +
            '<i class="fas fa-cogs"></i>' +
            '<p>Chưa có rule nào. Bấm "Thêm Rule" để tạo mới!</p>' +
            '</div>'
        );
        return;
    }

    let html = '';
    rules.forEach(function(rule) {
        const oldRule = oldRules.find(r => r.id === rule.id);
        const justTriggered = oldRule && oldRule.trigger_count < rule.trigger_count;
        
        html += createRuleCard(rule, justTriggered);
    });

    $('#rulesContainer').html(html);
    
    setTimeout(function() {
        $('.rule-card.just-triggered').removeClass('just-triggered');
    }, 2000);
}

// ============================================
// CREATE RULE CARD HTML
// ============================================
function createRuleCard(rule, justTriggered = false) {
    const triggerIcon = triggerIcons[rule.trigger_type] || 'fa-cog';
    const activeClass = rule.is_active ? '' : 'inactive';
    const highlightClass = justTriggered ? 'just-triggered' : '';
    const checkedAttr = rule.is_active ? 'checked' : '';
    
    const autoRevertBadge = rule.auto_revert == 1 
        ? '<span class="badge badge-revert ml-2" title="Tự động đảo ngược khi điều kiện không còn đúng"><i class="fas fa-sync-alt"></i> Auto</span>'
        : '';
    
    return `
        <div class="card rule-card shadow mb-3 ${activeClass} ${highlightClass}" data-id="${rule.id}">
            <div class="card-body">
                <div class="rule-header mb-2">
                    <div class="d-flex align-items-center">
                        <div class="trigger-icon ${rule.trigger_type} mr-3">
                            <i class="fas ${triggerIcon}"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold">
                                ${escapeHtml(rule.name)}
                                ${autoRevertBadge}
                            </h6>
                            <small class="text-muted">${escapeHtml(rule.description) || 'Không có mô tả'}</small>
                        </div>
                    </div>
                    <div class="custom-control custom-switch custom-switch-lg">
                        <input type="checkbox" class="custom-control-input" 
                               id="switch${rule.id}" ${checkedAttr}
                               onchange="toggleRule(${rule.id})">
                        <label class="custom-control-label" for="switch${rule.id}"></label>
                    </div>
                </div>
                
                <div class="rule-trigger">
                    <strong>Khi:</strong> ${formatTrigger(rule)}
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div>
                        <span class="rule-action">
                            <i class="fas fa-arrow-right mr-1"></i>
                            ${formatAction(rule)}
                        </span>
                    </div>
                    <div>
                        <span class="badge badge-light stats-badge mr-2" title="Số lần kích hoạt">
                            <i class="fas fa-bolt mr-1"></i>${rule.trigger_count} lần
                        </span>
                        <button class="btn btn-sm btn-outline-primary" onclick="editRule(${rule.id})" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteRule(${rule.id})" title="Xóa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// ============================================
// FORMAT FUNCTIONS
// ============================================
function formatTrigger(rule) {
    const label = triggerLabels[rule.trigger_type] || rule.trigger_type;
    let value = rule.trigger_value;
    
    if (rule.trigger_type === 'temperature') {
        value += '°C';
    } else if (rule.trigger_type === 'humidity') {
        value += '%';
    } else if (rule.trigger_value === 'detected') {
        value = 'Phát hiện';
    } else if (rule.trigger_value === 'dark') {
        value = 'Tối';
    } else if (rule.trigger_value === 'bright') {
        value = 'Sáng';
    } else if (rule.trigger_value === 'none') {
        value = 'Không có';
    }
    
    return `${label} ${rule.trigger_operator} <strong>${value}</strong>`;
}

function formatAction(rule) {
    const device = deviceLabels[rule.action_type] || rule.action_type;
    const state = rule.action_value === 'on' ? 'BẬT' : 'TẮT';
    return `${device} → ${state}`;
}

function updateStats(rules) {
    const total = rules.length;
    const active = rules.filter(r => r.is_active).length;
    const triggered = rules.reduce((sum, r) => sum + r.trigger_count, 0);
    
    $('#statTotal').text(total);
    $('#statActive').text(active);
    $('#statTriggered').text(triggered);
}

// ============================================
// LOAD LOGS
// ============================================
let lastLogId = 0;

function loadLogs(silent = false) {
    $.get(API_BASE + '/get_automation_logs.php?limit=20')
        .done(function(data) {
            const hasNewLogs = data.length > 0 && data[0].id !== lastLogId;
            
            if (hasNewLogs && silent) {
                $('#logsContainer').addClass('flash-update');
                setTimeout(function() {
                    $('#logsContainer').removeClass('flash-update');
                }, 500);
            }
            
            if (data.length > 0) {
                lastLogId = data[0].id;
            }
            
            renderLogs(data);
        })
        .fail(function() {
            if (!silent) {
                $('#logsContainer').html('<p class="text-muted text-center">Không thể tải lịch sử</p>');
            }
        });
}

function renderLogs(logs) {
    if (logs.length === 0) {
        $('#logsContainer').html('<p class="text-muted text-center">Chưa có lịch sử</p>');
        return;
    }

    let html = '';
    let todayCount = 0;
    const today = new Date().toDateString();

    logs.forEach(function(log) {
        const logDate = new Date(log.created_at).toDateString();
        if (logDate === today) todayCount++;
        
        const statusClass = log.status === 'success' ? '' : 'failed';
        const time = new Date(log.created_at).toLocaleTimeString('vi-VN');
        const date = new Date(log.created_at).toLocaleDateString('vi-VN');
        
        html += `
            <div class="log-item ${statusClass}">
                <div class="d-flex justify-content-between">
                    <strong>${escapeHtml(log.rule_name)}</strong>
                    <small class="text-muted" title="${date}">${time}</small>
                </div>
                <small class="text-muted">
                    ${log.trigger_type}: ${log.trigger_value_actual} → ${log.action_executed}
                </small>
            </div>
        `;
    });

    $('#logsContainer').html(html);
    $('#statToday').text(todayCount);
}

// ============================================
// MODAL FUNCTIONS
// ============================================
function openCreateModal() {
    $('#modalTitle').html('<i class="fas fa-plus-circle mr-2"></i>Thêm Rule mới');
    $('#ruleForm')[0].reset();
    $('#ruleId').val('');
    $('#triggerOperator').prop('disabled', false);
    $('#triggerHelp').html('<i class="fas fa-info-circle mr-2"></i>Chọn loại trigger để xem hướng dẫn');
    $('#autoRevert').prop('checked', false);
    $('#autoRevertWarning').hide();
}

function editRule(id) {
    const rule = allRules.find(r => r.id === id);
    if (!rule) {
        Toast.error("Không tìm thấy rule");
        return;
    }

    $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Sửa Rule');
    $('#ruleId').val(rule.id);
    $('#ruleName').val(rule.name);
    $('#ruleDescription').val(rule.description || '');
    $('#triggerType').val(rule.trigger_type);
    $('#triggerOperator').val(rule.trigger_operator);
    $('#triggerValue').val(rule.trigger_value);
    $('#actionType').val(rule.action_type);
    $('#actionValue').val(rule.action_value);
    $('#autoRevert').prop('checked', rule.auto_revert == 1);

    onTriggerTypeChange();
    $('#ruleModal').modal('show');
}

function saveRule() {
    const data = {
        id: $('#ruleId').val() || null,
        name: $('#ruleName').val().trim(),
        description: $('#ruleDescription').val().trim(),
        trigger_type: $('#triggerType').val(),
        trigger_operator: $('#triggerOperator').val(),
        trigger_value: $('#triggerValue').val().trim(),
        action_type: $('#actionType').val(),
        action_value: $('#actionValue').val(),
        auto_revert: $('#autoRevert').is(':checked') ? 1 : 0
    };

    if (!data.name) {
        Toast.error("Vui lòng nhập tên rule");
        $('#ruleName').focus();
        return;
    }
    
    if (!data.trigger_type) {
        Toast.error("Vui lòng chọn loại trigger");
        $('#triggerType').focus();
        return;
    }
    
    if (!data.trigger_value) {
       Toast.error("Vui lòng nhập giá trị trigger");
        return;
    }
    
    if (!data.action_type) {
        Toast.error("Vui lòng chọn thiết bị");
        $('#actionType').focus();
        return;
    }

    const $saveBtn = $('#ruleModal .btn-primary');
    $saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Đang lưu...');

    $.ajax({
        url: API_BASE + '/save_automation_rule.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data)
    })
    .done(function(res) {
        $('#ruleModal').modal('hide');
        loadRules(false);
        loadLogs(false);
        Toast.success("Lưu rule thành công!");
    })
    .fail(function(xhr) {
        const res = xhr.responseJSON;
        Toast.error(res?.message || "Lưu thất bại!");
    })
    .always(function() {
        $saveBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Lưu Rule');
    });
}

function toggleRule(id) {
    $.ajax({
        url: API_BASE + '/toggle_automation_rule.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id: id })
    })
    .done(function(res) {
        loadRules(false);
        const status = res.is_active ? 'bật' : 'tắt';
        Toast.success(`Đã ${status} rule`);
    })
    .fail(function() {
        Toast.error("Không thể thay đổi trạng thái!");
        loadRules(false);
    });
}

function deleteRule(id) {
    const rule = allRules.find(r => r.id === id);
    const ruleName = rule ? rule.name : 'rule này';
    
    Toast.confirm(`Bạn có chắc muốn xóa "${ruleName}"?`, () => {
        $.ajax({
            url: API_BASE + '/delete_automation_rule.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: id })
        })
        .done(function(res) {
            loadRules(false);
            loadLogs(false);
            Toast.success("Đã xóa automation");
        })
        .fail(function() {
            Toast.error("Xóa thất bại!");
        });
    });
}

function manualRefresh() {
    const $btn = $('#refreshBtn');
    const $icon = $btn.find('i');
    
    $icon.addClass('fa-spin');
    $btn.prop('disabled', true);
    
    loadRules(false);
    loadLogs(false);
    
    setTimeout(function() {
        $icon.removeClass('fa-spin');
        $btn.prop('disabled', false);
        Toast.success('Đã làm mới!');
    }, 1000);
}

// ============================================
// UTILITY FUNCTIONS
// ============================================
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}