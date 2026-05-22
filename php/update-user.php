<?php

require_once __DIR__ . '/auth.php';
requireAdmin();

$id = (int) ($_POST['id'] ?? 0);
$username = trim($_POST['username'] ?? '');
$fullName = trim($_POST['full_name'] ?? '');
$password = $_POST['password'] ?? '';

if ($id < 1 || $username === '') {
    header('Location: admin-users.php?error=invalid');
    exit;
}

try {
    $pdo = getDB();

    if ($password !== '') {
        if (strlen($password) < 6) {
            header('Location: admin-users.php?error=invalid');
            exit;
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'UPDATE users SET username = :u, full_name = :n, password_hash = :h WHERE id = :id'
        );
        $stmt->execute([':u' => $username, ':n' => $fullName ?: $username, ':h' => $hash, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare('UPDATE users SET username = :u, full_name = :n WHERE id = :id');
        $stmt->execute([':u' => $username, ':n' => $fullName ?: $username, ':id' => $id]);
    }

    header('Location: admin-users.php?updated=1');
} catch (PDOException $e) {
    if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
        header('Location: admin-users.php?error=duplicate');
        exit;
    }
    header('Location: admin-users.php?error=db');
}
exit;
