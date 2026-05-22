<?php
/**
 * MySQL connection — edit these if your XAMPP MySQL password is not empty.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'sd_staffing');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    return $pdo;
}

function jobRowToArray(array $row): array
{
    return [
        'id' => (string) $row['id'],
        'title' => $row['title'],
        'company' => $row['company'],
        'location' => $row['location'],
        'locationDisplay' => $row['location_display'],
        'area' => $row['area'] ?? '',
        'salary' => $row['salary'],
        'experience' => $row['experience'],
        'category' => $row['category'] ?? 'General',
        'keywords' => $row['keywords'] ?? '',
        'createdAt' => $row['created_at'],
    ];
}
