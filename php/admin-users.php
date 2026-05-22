<?php

require_once __DIR__ . '/auth.php';

requireAdmin();

$adminName = htmlspecialchars(adminUsername(), ENT_QUOTES, 'UTF-8');

$currentId = (int) ($_SESSION['admin_user_id'] ?? 0);



$pdo = getDB();

$users = $pdo->query('SELECT id, username, full_name, created_at FROM users ORDER BY id ASC')->fetchAll();

?>

<!DOCTYPE html>

<html lang="en">

<head>

  <meta charset="UTF-8" />

  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <meta name="robots" content="noindex, nofollow" />

  <title>Manage Users | SD Staffing</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">

  <script>

    tailwind.config = { theme: { extend: { fontFamily: { sans: ['Outfit', 'sans-serif'] }, colors: { sd: { pink: 'rgb(233, 30, 99)', dark: '#0f172a' } } } } } };

  </script>

  <style>

    .admin-input { width: 100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 14px; }

    .admin-input:focus { border-color: rgba(233,30,99,.65); box-shadow: 0 0 0 4px rgba(233,30,99,.12); outline: none; }

  </style>

</head>

<body class="bg-slate-50 font-sans text-slate-800 min-h-screen">



  <header class="bg-white border-b shadow-sm">

    <div class="container mx-auto px-6 py-4 flex flex-wrap justify-between items-center gap-3">

      <a href="admin-job.php" class="font-bold text-slate-800">← Back to jobs</a>

      <nav class="flex gap-3 text-sm font-semibold">

        <a href="admin-job.php" class="text-sd-pink">Post jobs</a>

        <a href="logout.php" class="text-slate-600 hover:text-red-600">Logout</a>

      </nav>

    </div>

  </header>



  <main class="container mx-auto px-6 py-10 max-w-3xl">

    <h1 class="text-2xl font-bold mb-2">Manage staff access</h1>

    <p class="text-sm text-slate-600 mb-6">Create, update, or remove logins. Each user can post and edit jobs after signing in.</p>



    <div id="alertOk" class="hidden mb-4 p-4 rounded-xl bg-green-50 text-green-800 text-sm font-semibold">User created.</div>

    <div id="alertUpd" class="hidden mb-4 p-4 rounded-xl bg-green-50 text-green-800 text-sm font-semibold">User updated.</div>

    <div id="alertDel" class="hidden mb-4 p-4 rounded-xl bg-green-50 text-green-800 text-sm font-semibold">User deleted.</div>

    <div id="alertDup" class="hidden mb-4 p-4 rounded-xl bg-red-50 text-red-700 text-sm">Username already exists.</div>

    <div id="alertInv" class="hidden mb-4 p-4 rounded-xl bg-red-50 text-red-700 text-sm">Check username and password (min 6 characters when changing password).</div>

    <div id="alertSelf" class="hidden mb-4 p-4 rounded-xl bg-amber-50 text-amber-900 text-sm">You cannot delete your own account while logged in.</div>

    <div id="alertDb" class="hidden mb-4 p-4 rounded-xl bg-red-50 text-red-700 text-sm">Database error.</div>



    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm">

      <p class="font-bold text-amber-900 mb-1">Share with your client:</p>

      <p>Login: <code class="bg-white px-1 rounded text-xs break-all">http://localhost/SDstaffing/admin-login.html</code></p>

    </div>



    <div class="bg-white rounded-2xl shadow-lg border p-6 mb-8">

      <h2 class="font-bold mb-4">Create new user</h2>

      <form action="create-user.php" method="POST" class="space-y-4">

        <div>

          <label class="block text-sm font-semibold mb-1">Username *</label>

          <input type="text" name="username" required class="admin-input" placeholder="e.g. client1">

        </div>

        <div>

          <label class="block text-sm font-semibold mb-1">Password * (min 6 characters)</label>

          <input type="password" name="password" required minlength="6" class="admin-input">

        </div>

        <div>

          <label class="block text-sm font-semibold mb-1">Full name (optional)</label>

          <input type="text" name="full_name" class="admin-input" placeholder="e.g. HR Manager">

        </div>

        <button type="submit" class="w-full bg-sd-pink hover:bg-pink-600 text-white font-bold py-3 rounded-xl">Create user</button>

      </form>

    </div>



    <h2 class="font-bold mb-3">Existing users (<?= count($users) ?>)</h2>

    <ul class="space-y-4">

      <?php foreach ($users as $u):

        $uid = (int) $u['id'];

        $isSelf = $uid === $currentId;

        $uname = htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8');

        $fname = htmlspecialchars($u['full_name'] ?? '', ENT_QUOTES, 'UTF-8');

      ?>

        <li class="bg-white border rounded-2xl overflow-hidden">

          <div class="px-4 py-3 flex flex-wrap justify-between items-center gap-2 text-sm">

            <span>

              <strong><?= $uname ?></strong>

              <?php if ($fname): ?> — <?= $fname ?><?php endif; ?>

              <?php if ($isSelf): ?><span class="text-xs font-bold text-sd-pink ml-1">(you)</span><?php endif; ?>

            </span>

            <div class="flex gap-2">

              <button type="button" class="edit-toggle text-xs font-bold px-3 py-2 rounded-lg border border-slate-200 hover:bg-slate-50" data-target="edit-<?= $uid ?>">Edit</button>

              <?php if (!$isSelf): ?>

                <form method="POST" action="delete-user.php" class="inline" onsubmit="return confirm('Delete user <?= $uname ?>? They will no longer be able to log in.');">

                  <input type="hidden" name="id" value="<?= $uid ?>">

                  <button type="submit" class="text-red-600 text-xs font-bold px-3 py-2 rounded-lg border border-red-200 hover:bg-red-50">Delete</button>

                </form>

              <?php endif; ?>

            </div>

          </div>

          <div id="edit-<?= $uid ?>" class="hidden border-t bg-slate-50 px-4 py-4">

            <form action="update-user.php" method="POST" class="space-y-3 max-w-md">

              <input type="hidden" name="id" value="<?= $uid ?>">

              <div>

                <label class="block text-xs font-semibold mb-1">Username *</label>

                <input type="text" name="username" required class="admin-input" value="<?= $uname ?>">

              </div>

              <div>

                <label class="block text-xs font-semibold mb-1">New password (leave blank to keep current)</label>

                <input type="password" name="password" minlength="6" class="admin-input" placeholder="Optional">

              </div>

              <div>

                <label class="block text-xs font-semibold mb-1">Full name</label>

                <input type="text" name="full_name" class="admin-input" value="<?= $fname ?>">

              </div>

              <button type="submit" class="bg-sd-pink text-white font-bold text-sm px-4 py-2 rounded-lg">Save changes</button>

            </form>

          </div>

        </li>

      <?php endforeach; ?>

    </ul>

  </main>



  <script>

    document.querySelectorAll('.edit-toggle').forEach((btn) => {

      btn.addEventListener('click', () => {

        const el = document.getElementById(btn.dataset.target);

        el.classList.toggle('hidden');

      });

    });

    const p = new URLSearchParams(location.search);
    if (p.get('created') === '1') document.getElementById('alertOk').classList.remove('hidden');
    if (p.get('updated') === '1') document.getElementById('alertUpd').classList.remove('hidden');
    if (p.get('deleted') === '1') document.getElementById('alertDel').classList.remove('hidden');
    if (p.get('error') === 'duplicate') document.getElementById('alertDup').classList.remove('hidden');
    if (p.get('error') === 'invalid') document.getElementById('alertInv').classList.remove('hidden');
    if (p.get('error') === 'self') document.getElementById('alertSelf').classList.remove('hidden');
    if (p.get('error') === 'db') document.getElementById('alertDb').classList.remove('hidden');
    if (p.get('created') || p.get('updated') || p.get('deleted') || p.get('error')) {
      history.replaceState({}, '', 'admin-users.php');
    }

  </script>

</body>

</html>

