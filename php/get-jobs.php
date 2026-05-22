<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/db.php';

try {
    $pdo = getDB();
    $stmt = $pdo->query(
        'SELECT id, title, company, location, location_display, area, salary, experience, category, keywords, created_at
         FROM jobs
         ORDER BY created_at DESC'
    );

    $jobs = array_map('jobRowToArray', $stmt->fetchAll());
    echo json_encode($jobs, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error. Run setup-database.php first and start MySQL in XAMPP.',
        'detail' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
