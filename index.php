<?php
/**
 * Boleto Admin Pro — Main Dashboard
 * Audit table with DNI toggle, duplicate scanner, and CSV export
 */
require_once __DIR__ . '/includes/header.php';
?>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- ═══ Stats Cards ═══ -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

            <!-- Total Revenue -->
            <div class="glass-card stat-card p-5 animate-fade-in-up animate-delay-1" style="--accent-color: #10b981;">
                <div class="flex items-center justify-between mb-3">
                    <span class="revenue-label">Ingresos Totales</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p id="stat-revenue" class="revenue-amount text-2xl">S/ 0.00</p>
            </div>

            <!-- Total Participants -->
            <div class="glass-card stat-card p-5 animate-fade-in-up animate-delay-2" style="--accent-color: #6366f1;">
                <div class="flex items-center justify-between mb-3">
                    <span class="revenue-label">Total Compras</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                <p id="stat-total" class="text-2xl font-extrabold text-white counter-animate">0</p>
            </div>

            <!-- With DNI -->
            <div class="glass-card stat-card p-5 animate-fade-in-up animate-delay-3" style="--accent-color: #10b981;">
                <div class="flex items-center justify-between mb-3">
                    <span class="revenue-label">Con DNI</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p id="stat-dni" class="text-2xl font-extrabold text-emerald-400 counter-animate">0</p>
            </div>

            <!-- Without DNI -->
            <div class="glass-card stat-card p-5 animate-fade-in-up animate-delay-4" style="--accent-color: #f59e0b;">
                <div class="flex items-center justify-between mb-3">
                    <span class="revenue-label">Sin DNI</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <p id="stat-no-dni" class="text-2xl font-extrabold text-amber-400 counter-animate">0</p>
            </div>

        </section>

        <!-- ═══ Audit Toolbar ═══ -->
        <section class="glass-card p-4 mb-6 animate-fade-in-up">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">

                <!-- Search -->
                <div class="relative w-full lg:w-80">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input id="search-input" type="text" placeholder="Buscar por nombre, DNI, operación..." class="search-input">
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-3">

                    <!-- Toggle DNI -->
                    <button id="btn-toggle-dni" onclick="toggleNoDNI()" class="btn-action btn-toggle">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        <span class="btn-text">Ocultar sin DNI</span>
                    </button>

                    <!-- Scan Duplicates -->
                    <button id="btn-scan" onclick="scanDuplicates()" class="btn-action btn-scan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                        <span class="btn-text">Escanear Duplicados</span>
                        <span class="hidden items-center gap-1 ml-1" id="duplicate-badge">
                            <span id="duplicate-count" class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">0</span>
                        </span>
                    </button>

                    <!-- Export CSV -->
                    <button id="btn-export" onclick="exportToCSV()" class="btn-action btn-export">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="btn-text">Exportar CSV</span>
                    </button>

                    <!-- Refresh -->
                    <button onclick="refreshData()" class="btn-action border border-white/10 text-gray-400 hover:text-white hover:border-white/20" title="Refrescar datos">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>

            </div>

            <!-- Row count -->
            <div class="mt-3 pt-3 border-t border-white/5 flex items-center justify-between">
                <p class="text-xs text-gray-500">
                    Mostrando <span id="row-count" class="text-gray-400 font-medium">0</span> registros
                </p>
            </div>
        </section>

        <!-- ═══ Audit Table ═══ -->
        <section class="glass-card overflow-hidden animate-fade-in-up">
            <div class="overflow-x-auto">
                <table class="audit-table">
                    <thead>
                        <tr>
                            <th class="w-16">ID</th>
                            <th>Nombre Completo</th>
                            <th>DNI</th>
                            <th>Monto Pagado</th>
                            <th>Nº Operación</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <!-- Data loaded via JS -->
                    </tbody>
                </table>
            </div>
        </section>

    </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
