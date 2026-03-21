/**
 * ═══════════════════════════════════════════════════════════════
 * Boleto Admin Pro — Application Core (app.js)
 * Supabase integration, audit tools, and UI interactions
 * ═══════════════════════════════════════════════════════════════
 */

// ─── Supabase Client Init ─────────────────────────────────────
const supabaseClient = window.supabase.createClient(
    window.SUPABASE_URL,
    window.SUPABASE_KEY
);

// ─── State ────────────────────────────────────────────────────
let allData = [];
let hidingNoDNI = false;
let duplicateIds = new Set();

// ─── DOM Ready ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    loadParticipants();
    initSearch();
});

// ═══════════════════════════════════════════════════════════════
// DATA LOADING
// ═══════════════════════════════════════════════════════════════

async function loadParticipants() {
    showTableSkeleton();

    try {
        const { data, error } = await supabaseClient
                .from(window.SUPABASE_TABLE)
                .select('*, participantes(nombre_completo, dni)')
                .order('id', { ascending: false });

            if (error) throw error;

            // Unimos los datos cruzados para que la pantalla los entienda
            allData = data.map(fila => ({
                ...fila,
                nombre_completo: fila.participantes?.nombre_completo || 'Sin nombre',
                dni: fila.participantes?.dni || 'Sin DNI'
            })) || [];
        renderTable(allData);
        updateStats(allData);
        showToast('success', `${allData.length} registros cargados`);
    } catch (err) {
        console.error('Error loading data:', err);
        showToast('error', 'Error al cargar datos: ' + err.message);
        showEmptyState();
    }
}

// ═══════════════════════════════════════════════════════════════
// TABLE RENDERING
// ═══════════════════════════════════════════════════════════════

function renderTable(data) {
    const tbody = document.getElementById('table-body');
    if (!tbody) return;

    if (data.length === 0) {
        showEmptyState();
        return;
    }

    tbody.innerHTML = data.map((row, i) => {
        const hasDNI = row.dni && row.dni.toString().trim() !== '';
        const isDuplicate = duplicateIds.has(row.id);
        const noDniClass = !hasDNI ? 'row-no-dni' : '';
        const hiddenClass = !hasDNI && hidingNoDNI ? 'row-hidden' : '';
        const duplicateClass = isDuplicate ? 'row-duplicate' : '';

        return `
            <tr class="animate-fade-in-up animate-delay-${(i % 4) + 1} ${noDniClass} ${hiddenClass} ${duplicateClass}" 
                data-id="${row.id}" 
                data-dni="${hasDNI ? 'yes' : 'no'}"
                data-numero="${row.numero_operacion || ''}">
                <td class="font-mono text-xs text-gray-500">${row.id}</td>
                <td>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-accent/30 to-purple-500/30 flex items-center justify-center text-xs font-bold text-accent-light">
                            ${(row.nombre_completo || 'N/A').charAt(0).toUpperCase()}
                        </div>
                        <span class="font-medium">${row.nombre_completo || '<span class="text-gray-500 italic">Sin nombre</span>'}</span>
                    </div>
                </td>
                <td>
                    ${hasDNI 
                        ? `<span class="badge badge-success">${row.dni}</span>` 
                        : `<span class="badge badge-danger">Sin DNI</span>`
                    }
                </td>
                <td class="revenue-amount text-lg">S/ ${parseFloat(row.monto_pagado || 0).toFixed(2)}</td>
                <td>
                    <span class="font-mono text-sm text-gray-400">${row.numero_operacion || '<span class="text-gray-600">—</span>'}</span>
                    ${isDuplicate ? '<span class="ml-2 badge badge-danger">DUPLICADO</span>' : ''}
                </td>
                <td class="text-xs text-gray-500">${row.created_at ? formatDate(row.created_at) : '—'}</td>
            </tr>
        `;
    }).join('');

    // Update row count
    const countEl = document.getElementById('row-count');
    if (countEl) {
        const visibleCount = data.filter(r => {
            const hasDNI = r.dni && r.dni.toString().trim() !== '';
            return !hidingNoDNI || hasDNI;
        }).length;
        countEl.textContent = `${visibleCount} de ${data.length}`;
    }
}

function showTableSkeleton() {
    const tbody = document.getElementById('table-body');
    if (!tbody) return;
    tbody.innerHTML = Array.from({ length: 6 }, () => `
        <tr>
            <td><div class="skeleton h-4 w-8"></div></td>
            <td><div class="skeleton h-4 w-32"></div></td>
            <td><div class="skeleton h-4 w-20"></div></td>
            <td><div class="skeleton h-4 w-16"></div></td>
            <td><div class="skeleton h-4 w-24"></div></td>
            <td><div class="skeleton h-4 w-20"></div></td>
        </tr>
    `).join('');
}

function showEmptyState() {
    const tbody = document.getElementById('table-body');
    if (!tbody) return;
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-gray-500 text-sm">No se encontraron registros</p>
                    <p class="text-gray-600 text-xs">Configura tu conexión a Supabase en config.php</p>
                </div>
            </td>
        </tr>
    `;
}

// ═══════════════════════════════════════════════════════════════
// STATS
// ═══════════════════════════════════════════════════════════════

function updateStats(data) {
    // Total revenue
    const totalRevenue = data.reduce((sum, r) => sum + parseFloat(r.monto_pagado || 0), 0);
    animateCounter('stat-revenue', totalRevenue, true);

    // Total participants
    animateCounter('stat-total', data.length);

    // With DNI
    const withDNI = data.filter(r => r.dni && r.dni.toString().trim() !== '').length;
    animateCounter('stat-dni', withDNI);

    // Without DNI
    animateCounter('stat-no-dni', data.length - withDNI);
}

function animateCounter(elementId, target, isCurrency = false) {
    const el = document.getElementById(elementId);
    if (!el) return;

    const duration = 1200;
    const start = performance.now();
    const startVal = 0;

    function update(currentTime) {
        const elapsed = currentTime - start;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 4); // easeOutQuart
        const current = startVal + (target - startVal) * eased;

        if (isCurrency) {
            el.textContent = `S/ ${current.toFixed(2)}`;
        } else {
            el.textContent = Math.round(current);
        }

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

// ═══════════════════════════════════════════════════════════════
// TOGGLE NO-DNI
// ═══════════════════════════════════════════════════════════════

function toggleNoDNI() {
    hidingNoDNI = !hidingNoDNI;
    const btn = document.getElementById('btn-toggle-dni');

    if (hidingNoDNI) {
        btn.classList.add('active');
        btn.querySelector('.btn-text').textContent = 'Mostrar sin DNI';
        showToast('info', 'Participantes sin DNI ocultos');
    } else {
        btn.classList.remove('active');
        btn.querySelector('.btn-text').textContent = 'Ocultar sin DNI';
        showToast('info', 'Mostrando todos los participantes');
    }

    renderTable(allData);
}

// ═══════════════════════════════════════════════════════════════
// SCAN DUPLICATES
// ═══════════════════════════════════════════════════════════════

async function scanDuplicates() {
    const btn = document.getElementById('btn-scan');
    btn.classList.add('scanning');
    btn.disabled = true;
    btn.querySelector('.btn-text').textContent = 'Escaneando...';

    // Clear previous duplicates
    duplicateIds.clear();

    try {
        // Find duplicates by numero_operacion
        const operationMap = new Map();

        allData.forEach(row => {
            const numOp = (row.numero_operacion || '').toString().trim();
            if (numOp === '') return;

            if (operationMap.has(numOp)) {
                operationMap.get(numOp).push(row.id);
            } else {
                operationMap.set(numOp, [row.id]);
            }
        });

        // Collect IDs of all duplicated entries
        let duplicateCount = 0;
        operationMap.forEach((ids, numOp) => {
            if (ids.length > 1) {
                ids.forEach(id => duplicateIds.add(id));
                duplicateCount += ids.length;
            }
        });

        // Re-render table with highlights
        renderTable(allData);

        if (duplicateCount > 0) {
            showToast('error', `⚠️ ${duplicateCount} tickets duplicados encontrados`);
        } else {
            showToast('success', '✓ No se encontraron duplicados');
        }

        // Update duplicate badge
        const badgeEl = document.getElementById('duplicate-count');
        if (badgeEl) {
            badgeEl.textContent = duplicateCount;
            badgeEl.parentElement.style.display = duplicateCount > 0 ? 'flex' : 'none';
        }

    } catch (err) {
        console.error('Scan error:', err);
        showToast('error', 'Error al escanear: ' + err.message);
    } finally {
        btn.classList.remove('scanning');
        btn.disabled = false;
        btn.querySelector('.btn-text').textContent = 'Escanear Duplicados';
    }
}

// ═══════════════════════════════════════════════════════════════
// EXPORT CSV
// ═══════════════════════════════════════════════════════════════

function exportToCSV() {
    if (allData.length === 0) {
        showToast('error', 'No hay datos para exportar');
        return;
    }

    const btn = document.getElementById('btn-export');
    btn.querySelector('.btn-text').textContent = 'Exportando...';

    try {
        // CSV headers
        const headers = ['ID', 'Nombre Completo', 'DNI', 'Monto Pagado', 'Número Operación', 'Fecha'];

        // CSV rows
        const rows = allData.map(row => [
            row.id,
            `"${(row.nombre_completo || '').replace(/"/g, '""')}"`,
            row.dni || '',
            row.monto_pagado || 0,
            row.numero_operacion || '',
            row.created_at || ''
        ]);

        // Build CSV string
        const csvContent = [
            headers.join(','),
            ...rows.map(r => r.join(','))
        ].join('\n');

        // Add BOM for Excel compatibility
        const BOM = '\uFEFF';
        const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });

        // Download
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `boleto-admin-export-${getDateStamp()}.csv`);
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);

        showToast('success', `✓ ${allData.length} registros exportados a CSV`);
    } catch (err) {
        showToast('error', 'Error al exportar: ' + err.message);
    } finally {
        btn.querySelector('.btn-text').textContent = 'Exportar CSV';
    }
}

// ═══════════════════════════════════════════════════════════════
// SEARCH
// ═══════════════════════════════════════════════════════════════

function initSearch() {
    const searchInput = document.getElementById('search-input');
    if (!searchInput) return;

    let debounceTimer;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const query = e.target.value.toLowerCase().trim();
            if (query === '') {
                renderTable(allData);
            } else {
                const filtered = allData.filter(row =>
                    (row.nombre_completo || '').toLowerCase().includes(query) ||
                    (row.dni || '').toString().includes(query) ||
                    (row.numero_operacion || '').toString().includes(query) ||
                    (row.monto_pagado || '').toString().includes(query)
                );
                renderTable(filtered);
            }
        }, 250);
    });
}

// ═══════════════════════════════════════════════════════════════
// UTILITIES
// ═══════════════════════════════════════════════════════════════

function formatDate(dateStr) {
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('es-PE', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch {
        return dateStr;
    }
}

function getDateStamp() {
    const d = new Date();
    return `${d.getFullYear()}${String(d.getMonth() + 1).padStart(2, '0')}${String(d.getDate()).padStart(2, '0')}`;
}

// ─── Toast System ─────────────────────────────────────────────
function showToast(type, message) {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    const icons = {
        success: '✓',
        error: '✕',
        info: 'ℹ'
    };

    toast.innerHTML = `
        <span class="text-lg">${icons[type] || 'ℹ'}</span>
        <span>${message}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// ─── Refresh Data ─────────────────────────────────────────────
function refreshData() {
    duplicateIds.clear();
    hidingNoDNI = false;
    const btn = document.getElementById('btn-toggle-dni');
    if (btn) {
        btn.classList.remove('active');
        btn.querySelector('.btn-text').textContent = 'Ocultar sin DNI';
    }
    loadParticipants();
}
