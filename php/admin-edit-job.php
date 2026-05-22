<?php
require_once __DIR__ . '/auth.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
if ($id < 1) {
    header('Location: admin-job.php');
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare('SELECT * FROM jobs WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$job = $stmt->fetch();
if (!$job) {
    header('Location: admin-job.php?error=missing');
    exit;
}

$adminName = htmlspecialchars(adminUsername(), ENT_QUOTES, 'UTF-8');
$title = htmlspecialchars($job['title'], ENT_QUOTES, 'UTF-8');
$company = htmlspecialchars($job['company'], ENT_QUOTES, 'UTF-8');
$area = htmlspecialchars($job['area'] ?? '', ENT_QUOTES, 'UTF-8');
$salary = htmlspecialchars($job['salary'], ENT_QUOTES, 'UTF-8');
$experience = htmlspecialchars($job['experience'], ENT_QUOTES, 'UTF-8');
$location = htmlspecialchars($job['location'], ENT_QUOTES, 'UTF-8');
$category = htmlspecialchars($job['category'] ?? 'General', ENT_QUOTES, 'UTF-8');
$cities = ['chennai', 'bangalore', 'mumbai', 'goa'];
$categories = ['Sales', 'Technical', 'HR', 'Management', 'General'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex, nofollow" />
  <title>Edit Job | SD Staffing</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  <script>
    tailwind.config = { theme: { extend: { fontFamily: { sans: ['Outfit', 'sans-serif'] }, colors: { sd: { pink: 'rgb(233, 30, 99)', dark: '#0f172a' } } } } } };
  </script>
  <style>
    .field-wrap { position: relative; }
    .field-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; pointer-events: none; }
    .admin-input { width: 100%; padding: 12px 14px 12px 42px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px; }
    select.admin-input { padding-left: 42px; }
    .admin-input:focus { border-color: rgba(233,30,99,.65); box-shadow: 0 0 0 4px rgba(233,30,99,.12); outline: none; }
  </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 min-h-screen">

  <header class="bg-white border-b shadow-sm">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
      <a href="admin-job.php" class="font-bold text-slate-800">← Back to jobs</a>
      <span class="text-sm text-slate-500">Hi, <?= $adminName ?></span>
    </div>
  </header>

  <main class="container mx-auto px-6 py-10 max-w-3xl">
    <h1 class="text-2xl font-bold mb-6">Edit job</h1>

    <?php if (isset($_GET['updated']) && $_GET['updated'] === '1'): ?>
      <p class="mb-4 p-4 rounded-xl bg-green-50 text-green-700 text-sm font-semibold">Job updated. <a href="admin-job.php" class="underline">Back to all jobs</a></p>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] === 'db'): ?>
      <p class="mb-4 p-4 rounded-xl bg-red-50 text-red-700 text-sm">Could not save. Check database.</p>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] === 'missing'): ?>
      <p class="mb-4 p-4 rounded-xl bg-red-50 text-red-700 text-sm">Fill in all required fields.</p>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-lg border p-6 md:p-8">
      <form action="update-job.php" method="POST" class="space-y-5">
        <input type="hidden" name="id" value="<?= (int) $job['id'] ?>">
        <div class="field-wrap">
          <i class="fas fa-user-tie"></i>
          <label class="block text-sm font-semibold mb-1 ml-1">Job Title *</label>
          <input type="text" name="title" required class="admin-input" value="<?= $title ?>">
        </div>
        <div class="field-wrap">
          <i class="fas fa-building"></i>
          <label class="block text-sm font-semibold mb-1 ml-1">Company *</label>
          <input type="text" name="company" required class="admin-input" value="<?= $company ?>">
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
          <div class="field-wrap">
            <i class="fas fa-tag"></i>
            <label class="block text-sm font-semibold mb-1 ml-1">Category</label>
            <select name="category" class="admin-input">
              <?php foreach ($categories as $c): ?>
                <option value="<?= $c ?>"<?= $category === $c ? ' selected' : '' ?>><?= $c ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field-wrap">
            <i class="fas fa-map-marker-alt"></i>
            <label class="block text-sm font-semibold mb-1 ml-1">Location *</label>
            <select name="location" required class="admin-input">
              <?php foreach ($cities as $c): ?>
                <option value="<?= $c ?>"<?= $location === $c ? ' selected' : '' ?>><?= ucfirst($c) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
          <div class="field-wrap">
            <i class="fas fa-location-crosshairs"></i>
            <label class="block text-sm font-semibold mb-1 ml-1">Area</label>
            <input type="text" name="area" class="admin-input" value="<?= $area ?>">
          </div>
          <div class="field-wrap">
            <i class="fas fa-indian-rupee-sign"></i>
            <label class="block text-sm font-semibold mb-1 ml-1">Salary *</label>
            <input type="text" name="salary" required class="admin-input" value="<?= $salary ?>">
          </div>
        </div>
        <div class="field-wrap">
          <i class="fas fa-clock"></i>
          <label class="block text-sm font-semibold mb-1 ml-1">Experience *</label>
          <input type="text" name="experience" required class="admin-input" value="<?= $experience ?>">
        </div>
        <button type="submit" class="w-full bg-sd-pink hover:bg-pink-600 text-white font-bold py-3 rounded-xl">
          <i class="fas fa-save"></i> Update job
        </button>
      </form>
    </div>
  </main>
</body>
</html>
