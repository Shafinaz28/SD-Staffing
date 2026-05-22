<?php
/**
 * One-time setup: http://localhost/SDstaffing/php/setup-database.php
 */
require_once __DIR__ . '/db.php';

header('Content-Type: text/html; charset=utf-8');

$messages = [];
$ok = true;

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo->exec(
        'CREATE DATABASE IF NOT EXISTS sd_staffing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
    );
    $pdo->exec('USE sd_staffing');

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          username VARCHAR(80) NOT NULL UNIQUE,
          password_hash VARCHAR(255) NOT NULL,
          full_name VARCHAR(120) DEFAULT "",
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($userCount === 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $insUser = $pdo->prepare(
            'INSERT INTO users (username, password_hash, full_name) VALUES (?, ?, ?)'
        );
        $insUser->execute(['admin', $hash, 'SD Admin']);
        $messages[] = 'Created admin user: <strong>admin</strong> / password: <strong>admin123</strong> (change after first login).';
    } else {
        $messages[] = 'Users table ready (' . $userCount . ' account(s)).';
    }

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS jobs (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          title VARCHAR(200) NOT NULL,
          company VARCHAR(200) NOT NULL,
          location VARCHAR(50) NOT NULL,
          location_display VARCHAR(100) NOT NULL,
          area VARCHAR(120) DEFAULT "",
          salary VARCHAR(100) NOT NULL,
          experience VARCHAR(100) NOT NULL,
          category VARCHAR(50) DEFAULT "General",
          keywords TEXT,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          INDEX idx_created (created_at DESC),
          INDEX idx_location (location)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $count = (int) $pdo->query('SELECT COUNT(*) FROM jobs')->fetchColumn();

    if ($count === 0) {
        $seed = [
            ['Sr. Sales Officer', 'Essae – Teraoka Pvt. Ltd.', 'chennai', 'Chennai', 'Gopalapuram', '25k – 35k', '2–4 yrs', 'Sales', 'sr sales officer sales officer retail field sales'],
            ['Service Engineer', 'Essae – Teraoka Pvt. Ltd.', 'chennai', 'Chennai', 'Ambattur', '21,500', '3 Positions', 'Technical', 'service engineer electronics iti diploma'],
            ['Service Engineer', 'Essae – Teraoka Pvt. Ltd.', 'goa', 'Goa', 'Panjim', '21,500', '0-1 yr', 'Technical', 'service engineer electronics iti diploma goa'],
            ['Sales Officer', 'Essae – Teraoka Pvt. Ltd.', 'bangalore', 'Bangalore', 'Koramangala', '23,000', '1-2 yrs', 'Sales', 'sales officer b2b industrial sales'],
        ];
        $ins = $pdo->prepare(
            'INSERT INTO jobs (title, company, location, location_display, area, salary, experience, category, keywords)
             VALUES (?,?,?,?,?,?,?,?,?)'
        );
        foreach ($seed as $row) {
            $ins->execute($row);
        }
        $messages[] = 'Added 4 sample jobs.';
    } else {
        $messages[] = "Table already has <strong>$count</strong> job(s) — skipped sample data.";
    }

    $count = (int) $pdo->query('SELECT COUNT(*) FROM jobs')->fetchColumn();
    $messages[] = 'Database <strong>sd_staffing</strong> is connected.';
    $messages[] = 'Total jobs: <strong>' . $count . '</strong>';
    $messages[] = '<a href="../admin-login.html" class="text-pink-600 underline">Admin login</a> · <a href="../job.php" class="text-pink-600 underline">Jobs page</a>';
} catch (Throwable $e) {
    $ok = false;
    $messages[] = htmlspecialchars($e->getMessage());
    $messages[] = 'Start <strong>Apache</strong> and <strong>MySQL</strong> in XAMPP, then open this page again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Database Setup | SD Staffing</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 font-sans">
  <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border p-8">
    <h1 class="text-xl font-bold mb-4 <?= $ok ? 'text-green-700' : 'text-red-700' ?>">
      <?= $ok ? 'Database ready' : 'Setup failed' ?>
    </h1>
    <ul class="space-y-2 text-sm text-slate-600 list-disc pl-5">
      <?php foreach ($messages as $m): ?>
        <li><?= $m ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</body>
</html>
