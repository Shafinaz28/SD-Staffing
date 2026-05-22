<?php

require_once __DIR__ . '/db.php';

function sd_load_jobs(): array
{
    try {
        $pdo = getDB();
        $stmt = $pdo->query(
            'SELECT id, title, company, location, location_display, area, salary, experience, category, keywords, created_at
             FROM jobs ORDER BY created_at DESC'
        );
        return array_map('jobRowToArray', $stmt->fetchAll());
    } catch (Throwable $e) {
        return [];
    }
}
