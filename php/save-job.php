<?php

require_once __DIR__ . '/auth.php';
requireAdmin();
$title = trim($_POST['title'] ?? '');
$company = trim($_POST['company'] ?? '');
$locationRaw = trim($_POST['location'] ?? '');
$salary = trim($_POST['salary'] ?? '');
$experience = trim($_POST['experience'] ?? '');

if ($title === '' || $company === '' || $locationRaw === '' || $salary === '' || $experience === '') {
    header('Location: admin-job.php?error=missing');
    exit;
}

$location = strtolower($locationRaw);
$locationDisplay = ucfirst($location);
$area = trim($_POST['area'] ?? '');
$category = trim($_POST['category'] ?? 'General');
$keywords = strtolower(trim($_POST['keywords'] ?? '') ?: ($title . ' ' . $company . ' ' . $category));

try {
    $pdo = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO jobs (title, company, location, location_display, area, salary, experience, category, keywords)
         VALUES (:title, :company, :location, :location_display, :area, :salary, :experience, :category, :keywords)'
    );
    $stmt->execute([
        ':title' => $title,
        ':company' => $company,
        ':location' => $location,
        ':location_display' => $locationDisplay,
        ':area' => $area,
        ':salary' => $salary,
        ':experience' => $experience,
        ':category' => $category,
        ':keywords' => $keywords,
    ]);

    header('Location: admin-job.php?saved=1');
    exit;
} catch (Throwable $e) {
    header('Location: admin-job.php?error=db');
    exit;
}
