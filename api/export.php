<?php
/**
 * Boleto Admin Pro — Export API
 * Generates CSV file from Supabase 'compras' table via REST API
 */
require_once __DIR__ . '/../config.php';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="boleto-admin-export-' . date('Ymd-His') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// BOM for Excel UTF-8 compatibility
echo "\xEF\xBB\xBF";

// Fetch data from Supabase REST API
$url = SUPABASE_URL . '/rest/v1/' . SUPABASE_TABLE . '?select=*&order=id.desc';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'apikey: ' . SUPABASE_ANON_KEY,
        'Authorization: Bearer ' . SUPABASE_ANON_KEY,
        'Content-Type: application/json'
    ],
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_TIMEOUT        => 30,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Error fetching data from Supabase', 'status' => $httpCode]);
    exit;
}

$data = json_decode($response, true);

if (!is_array($data)) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Invalid response from Supabase']);
    exit;
}

// Output CSV
$output = fopen('php://output', 'w');

// Headers
fputcsv($output, ['ID', 'Nombre Completo', 'DNI', 'Monto Pagado', 'Número Operación', 'Fecha']);

// Rows
foreach ($data as $row) {
    fputcsv($output, [
        $row['id'] ?? '',
        $row['nombre_completo'] ?? '',
        $row['dni'] ?? '',
        $row['monto_pagado'] ?? 0,
        $row['numero_operacion'] ?? '',
        $row['created_at'] ?? ''
    ]);
}

fclose($output);
exit;
