<?php

require_once __DIR__ . '/auth.php';

if (isAdminLoggedIn()) {
    header('Location: admin-job.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (attemptLogin($username, $password)) {
            header('Location: admin-job.php');
            exit;
        }
        header('Location: ../admin-login.html?error=1');
        exit;
    } catch (Throwable $e) {
        header('Location: ../admin-login.html?error=db');
        exit;
    }
}

header('Location: ../admin-login.html');
exit;
