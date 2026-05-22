<?php

require_once __DIR__ . '/auth.php';
requireAdmin();

$id = (int) ($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$company = trim($_POST['company'] ?? '');
$locationRaw = trim($_POST['location'] ?? '');
$salary = trim($_POST['salary'] ?? '');
$experience = trim($_POST['experience'] ?? '');

if ($id < 1 || $title === '' || $company === '' || $locationRaw === '' || $salary === '' || $experience === '') {
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
        'UPDATE jobs SET title = :title, company = :company, location = :location,
         location_display = :location_display, area = :area, salary = :salary,
         experience = :experience, category = :category, keywords = :keywords
         WHERE id = :id'
    );
    $stmt->execute([
        ':id' => $id,
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
    if ($stmt->rowCount() > 0) {
        header('Location: admin-job.php?updated=1');
    } else {
        header('Location: admin-edit-job.php?id=' . $id . '&error=missing');
    }
} catch (Throwable $e) {
    header('Location: admin-edit-job.php?id=' . $id . '&error=db');
}
exit;
