<?php
/**
 * Boleto Admin Pro — Scan Duplicates API
 * Finds duplicate tickets by numero_operacion in Supabase 'compras' table
 */
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

// Fetch all data from Supabase
$url = SUPABASE_URL . '/rest/v1/' . SUPABASE_TABLE . '?select=id,numero_operacion&order=id.desc';

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
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Error connecting to Supabase',
        'status'  => $httpCode
    ]);
    exit;
}

$data = json_decode($response, true);

if (!is_array($data)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid response from Supabase'
    ]);
    exit;
}

// Find duplicates
$operationMap = [];
foreach ($data as $row) {
    $numOp = trim($row['numero_operacion'] ?? '');
    if ($numOp === '') continue;

    if (!isset($operationMap[$numOp])) {
        $operationMap[$numOp] = [];
    }
    $operationMap[$numOp][] = $row['id'];
}

// Filter only duplicated entries
$duplicates = [];
$duplicateIds = [];
foreach ($operationMap as $numOp => $ids) {
    if (count($ids) > 1) {
        $duplicates[] = [
            'numero_operacion' => $numOp,
            'count'           => count($ids),
            'ids'             => $ids
        ];
        $duplicateIds = array_merge($duplicateIds, $ids);
    }
}

echo json_encode([
    'success'        => true,
    'total_records'  => count($data),
    'duplicate_count'=> count($duplicateIds),
    'duplicate_groups'=> count($duplicates),
    'duplicates'     => $duplicates,
    'duplicate_ids'  => $duplicateIds
]);
exit;
