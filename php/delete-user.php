<?php

require_once __DIR__ . '/auth.php';
requireAdmin();

$id = (int) ($_POST['id'] ?? 0);
$selfId = (int) ($_SESSION['admin_user_id'] ?? 0);

if ($id < 1) {
    header('Location: admin-users.php?error=invalid');
    exit;
}

if ($id === $selfId) {
    header('Location: admin-users.php?error=self');
    exit;
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute([':id' => $id]);
    header('Location: admin-users.php?deleted=1');
} catch (Throwable $e) {
    header('Location: admin-users.php?error=db');
}
exit;
