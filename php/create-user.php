<?php

require_once __DIR__ . '/auth.php';
requireAdmin();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$fullName = trim($_POST['full_name'] ?? '');

if ($username === '' || strlen($password) < 6) {
    header('Location: admin-users.php?error=invalid');
    exit;
}

try {
    $pdo = getDB();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(
        'INSERT INTO users (username, password_hash, full_name) VALUES (:u, :h, :n)'
    );
    $stmt->execute([
        ':u' => $username,
        ':h' => $hash,
        ':n' => $fullName ?: $username,
    ]);
    header('Location: admin-users.php?created=1');
} catch (PDOException $e) {
    if ((int) $e->errorInfo[1] === 1062) {
        header('Location: admin-users.php?error=duplicate');
        exit;
    }
    header('Location: admin-users.php?error=db');
}
exit;
