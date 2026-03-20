<?php
/**
 * Boleto-Admin-Pro — Configuration
 * Supabase credentials and project constants
 */

// ─── Supabase Configuration ───────────────────────────────────────
define('SUPABASE_URL',      'https://luvqlnwjfibljvqmrndx.supabase.co');
define('SUPABASE_ANON_KEY', 'sb_publishable_FlGRVkmU26vnlc2DBya1Eg_pTxwwmdN');
define('SUPABASE_TABLE',    'compras');

// ─── Project Metadata ─────────────────────────────────────────────
define('APP_NAME',    'Boleto Admin Pro');
define('APP_VERSION', '3.0.0');
define('APP_AUTHOR',  'rodrigojch18-cyber');

// ─── Timezone ─────────────────────────────────────────────────────
date_default_timezone_set('America/Lima');
