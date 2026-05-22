<?php

require_once __DIR__ . '/auth.php';
requireAdmin();
$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

if ($id < 1) {
    header('Location: admin-job.php?error=missing');
    exit;
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare('DELETE FROM jobs WHERE id = :id');
    $stmt->execute([':id' => $id]);
    header('Location: admin-job.php?deleted=1');
} catch (Throwable $e) {
    header('Location: admin-job.php?error=db');
}
exit;
