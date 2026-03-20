<?php
/**
 * Boleto-Admin-Pro — Configuration
 * Supabase credentials and project constants
 */

// ─── Supabase Configuration ───────────────────────────────────────
define('SUPABASE_URL',      'https://YOUR_PROJECT.supabase.co');
define('SUPABASE_ANON_KEY', 'YOUR_ANON_KEY_HERE');
define('SUPABASE_TABLE',    'compras');

// ─── Project Metadata ─────────────────────────────────────────────
define('APP_NAME',    'Boleto Admin Pro');
define('APP_VERSION', '3.0.0');
define('APP_AUTHOR',  'rodrigojch18-cyber');

// ─── Timezone ─────────────────────────────────────────────────────
date_default_timezone_set('America/Lima');
