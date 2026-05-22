<?php
require_once __DIR__ . '/auth.php';
requireAdmin();
$adminName = htmlspecialchars(adminUsername(), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex, nofollow" />
  <title>Admin – Add Job | SD Staffing</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" sizes="16x16" href="../images/SD LOGO_ENHANCED PNG.png">

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Outfit', 'sans-serif'] },
          colors: {
            sd: {
              pink: 'rgb(233, 30, 99)',
              dark: '#0f172a',
              light: '#f8fafc',
              blue: 'rgb(66, 165, 245)'
            }
          }
        }
      }
    };
  </script>

  <style>
    html, body { overflow-x: hidden; }
    .field-wrap { position: relative; }
    .field-wrap i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 14px;
      pointer-events: none;
    }
    .admin-input {
      width: 100%;
      padding: 12px 14px 12px 42px;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      font-size: 14px;
      outline: none;
      background: #fff;
      color: #0f172a;
      transition: border-color .2s, box-shadow .2s;
    }
    select.admin-input { padding-left: 42px; cursor: pointer; }
    .admin-input:focus {
      border-color: rgba(233, 30, 99, .65);
      box-shadow: 0 0 0 4px rgba(233, 30, 99, .12);
    }
    .preview-card { transition: transform .2s ease, box-shadow .2s ease; }
    .preview-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    }
  </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen">

  <header class="bg-white border-b border-slate-100 shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-6 py-4 flex flex-wrap justify-between items-center gap-4">
      <a href="../index.html" class="flex items-center gap-3">
        <img src="../images/SD LOGO_ENHANCED PNG.png" alt="SD Staffing" class="h-10 md:h-11 w-auto object-contain">
        <div class="hidden sm:block">
          <p class="text-xs font-bold uppercase tracking-wider text-sd-pink">Admin</p>
          <p class="text-sm font-bold text-slate-800 leading-tight">Job Posting</p>
        </div>
      </a>
      <nav class="flex items-center gap-3 text-sm font-semibold flex-wrap">
        <span class="text-slate-500 text-xs hidden sm:inline">Hi, <?= $adminName ?></span>
        <a href="admin-users.php" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-100 text-slate-700 hover:bg-sd-pink/10 hover:text-sd-pink transition">
          <i class="fas fa-users"></i> Manage users
        </a>
        <a href="../job.php" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-100 text-slate-700 hover:bg-sd-pink/10 hover:text-sd-pink transition">
          <i class="fas fa-briefcase"></i> Public Jobs
        </a>
        <a href="logout.php" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </nav>
    </div>
  </header>

  <section class="bg-gradient-to-br from-sd-pink/10 via-white to-sd-blue/10 border-b border-slate-100 py-10">
    <div class="container mx-auto px-6 max-w-3xl text-center">
      <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600 mb-4">
        <span class="w-2 h-2 rounded-full bg-sd-pink"></span>
        Logged in
      </span>
      <h1 class="text-3xl md:text-4xl font-bold text-slate-900">Add New Job Opening</h1>
    </div>
  </section>

  <main class="container mx-auto px-6 py-10 max-w-3xl">

    <div id="alertSaved" class="hidden mb-6 flex items-center gap-3 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-800 text-sm font-semibold">
      <i class="fas fa-check-circle text-lg"></i> Job saved successfully.
    </div>
    <div id="alertError" class="hidden mb-6 flex items-center gap-3 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm font-semibold">
      <i class="fas fa-exclamation-circle text-lg"></i> <span id="alertErrorText"></span>
    </div>
    <div id="alertDeleted" class="hidden mb-6 flex items-center gap-3 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-800 text-sm font-semibold">
      <i class="fas fa-check-circle text-lg"></i> Job removed from database.
    </div>
    <div id="alertUpdated" class="hidden mb-6 flex items-center gap-3 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-800 text-sm font-semibold">
      <i class="fas fa-check-circle text-lg"></i> Job updated successfully.
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
      <div class="bg-sd-dark px-6 py-5 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center text-white text-lg">
          <i class="fas fa-plus-circle"></i>
        </div>
        <div>
          <h2 class="text-white font-bold text-lg leading-tight">Job details</h2>
          <p class="text-slate-400 text-xs">Fields marked * are required</p>
        </div>
      </div>

      <form action="save-job.php" method="POST" class="p-6 md:p-8 space-y-5">
        <div class="field-wrap">
          <i class="fas fa-user-tie"></i>
          <label for="title" class="block text-sm font-semibold text-slate-700 mb-1.5 ml-1">Job Title *</label>
          <input type="text" id="title" name="title" required class="admin-input" placeholder="e.g. Sr. Sales Officer">
        </div>
        <div class="field-wrap">
          <i class="fas fa-building"></i>
          <label for="company" class="block text-sm font-semibold text-slate-700 mb-1.5 ml-1">Company *</label>
          <input type="text" id="company" name="company" required class="admin-input" placeholder="e.g. Essae – Teraoka Pvt. Ltd.">
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
          <div class="field-wrap">
            <i class="fas fa-tag"></i>
            <label for="category" class="block text-sm font-semibold text-slate-700 mb-1.5 ml-1">Category</label>
            <select id="category" name="category" class="admin-input">
              <option value="Sales">Sales</option>
              <option value="Technical">Technical</option>
              <option value="HR">HR</option>
              <option value="Management">Management</option>
              <option value="General">General</option>
            </select>
          </div>
          <div class="field-wrap">
            <i class="fas fa-map-marker-alt"></i>
            <label for="location" class="block text-sm font-semibold text-slate-700 mb-1.5 ml-1">Location *</label>
            <select id="location" name="location" required class="admin-input">
              <option value="" disabled selected>Select city</option>
              <option value="chennai">Chennai</option>
              <option value="bangalore">Bangalore</option>
              <option value="mumbai">Mumbai</option>
              <option value="goa">Goa</option>
            </select>
          </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
          <div class="field-wrap">
            <i class="fas fa-location-crosshairs"></i>
            <label for="area" class="block text-sm font-semibold text-slate-700 mb-1.5 ml-1">Area / Locality</label>
            <input type="text" id="area" name="area" class="admin-input" placeholder="e.g. Koramangala">
          </div>
          <div class="field-wrap">
            <i class="fas fa-indian-rupee-sign"></i>
            <label for="salary" class="block text-sm font-semibold text-slate-700 mb-1.5 ml-1">Salary *</label>
            <input type="text" id="salary" name="salary" required class="admin-input" placeholder="e.g. 25k – 35k">
          </div>
        </div>
        <div class="field-wrap">
          <i class="fas fa-clock"></i>
          <label for="experience" class="block text-sm font-semibold text-slate-700 mb-1.5 ml-1">Experience *</label>
          <input type="text" id="experience" name="experience" required class="admin-input" placeholder="e.g. 2–4 yrs">
        </div>
        <div class="flex flex-col sm:flex-row gap-3 pt-2 border-t border-slate-100">
          <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-sd-pink hover:bg-pink-600 text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg transition">
            <i class="fas fa-save"></i> Save Job
          </button>
          <button type="reset" class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold py-3.5 px-6 rounded-2xl border border-slate-200 transition">
            <i class="fas fa-rotate-left"></i> Clear
          </button>
        </div>
      </form>
    </div>

    <div class="mt-12">
      <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-bold text-slate-900">Saved jobs</h2>
        <span id="jobCount" class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">Loading…</span>
      </div>
      <div id="jobsPreview" class="space-y-4"><p class="text-slate-500 text-sm text-center py-8">Loading…</p></div>
    </div>
  </main>

  <footer class="border-t border-slate-200 bg-white py-6 mt-8">
    <p class="text-center text-xs text-slate-500">&copy; SD Staffing Solutions — Admin panel</p>
  </footer>

  <script>
    const base = 'admin-job.php';
    const params = new URLSearchParams(window.location.search);
    if (params.get('saved') === '1') {
      document.getElementById('alertSaved').classList.remove('hidden');
      history.replaceState({}, '', base);
    }
    if (params.get('error') === 'missing') {
      document.getElementById('alertErrorText').textContent = 'Please fill in all required fields.';
      document.getElementById('alertError').classList.remove('hidden');
      history.replaceState({}, '', base);
    }
    if (params.get('error') === 'db') {
      document.getElementById('alertErrorText').innerHTML = 'Database error. Check MySQL and <a href="setup-database.php" class="underline font-bold">setup-database.php</a>.';
      document.getElementById('alertError').classList.remove('hidden');
      history.replaceState({}, '', base);
    }
    if (params.get('deleted') === '1') {
      document.getElementById('alertDeleted').classList.remove('hidden');
      history.replaceState({}, '', base);
    }
    if (params.get('updated') === '1') {
      document.getElementById('alertUpdated').classList.remove('hidden');
      history.replaceState({}, '', base);
    }

    fetch('get-jobs.php')
      .then((r) => r.json())
      .then((jobs) => {
        if (jobs.error) throw new Error(jobs.error);
        const list = Array.isArray(jobs) ? jobs : [];
        const box = document.getElementById('jobsPreview');
        document.getElementById('jobCount').textContent = list.length + ' job' + (list.length === 1 ? '' : 's');
        if (!list.length) {
          box.innerHTML = '<div class="text-center py-12 rounded-2xl border border-dashed border-slate-200 bg-white"><p class="text-slate-600 font-semibold">No jobs yet</p></div>';
          return;
        }
        box.innerHTML = list.map((job) => `
          <article class="preview-card bg-white border border-slate-100 rounded-2xl p-5 flex flex-wrap gap-4 justify-between items-center">
            <div class="flex gap-4 min-w-0 flex-1">
              <div class="w-12 h-12 shrink-0 rounded-xl bg-sd-pink/10 text-sd-pink flex items-center justify-center text-lg"><i class="fas fa-briefcase"></i></div>
              <div class="min-w-0">
                <h3 class="font-bold text-slate-900 text-lg">${escapeHtml(job.title)}</h3>
                <p class="text-sm text-slate-500">${escapeHtml(job.company)}</p>
                <div class="flex flex-wrap gap-2 mt-2 text-xs font-medium text-slate-600">
                  <span class="px-2.5 py-1 rounded-lg bg-slate-50">${escapeHtml(job.locationDisplay || job.location)}</span>
                  <span class="px-2.5 py-1 rounded-lg bg-slate-50">${escapeHtml(job.salary)}</span>
                  <span class="px-2.5 py-1 rounded-lg bg-slate-50">${escapeHtml(job.experience)}</span>
                </div>
              </div>
            </div>
            <div class="flex gap-2 shrink-0">
              <a href="admin-edit-job.php?id=${encodeURIComponent(job.id)}" class="text-sd-pink text-xs font-bold px-3 py-2 rounded-lg border border-pink-200 hover:bg-pink-50">Edit</a>
              <form method="POST" action="delete-job.php" onsubmit="return confirm('Delete this job?');">
                <input type="hidden" name="id" value="${escapeHtml(job.id)}">
                <button type="submit" class="text-red-600 text-xs font-bold px-3 py-2 rounded-lg border border-red-200 hover:bg-red-50">Delete</button>
              </form>
            </div>
          </article>`).join('');
      })
      .catch(() => {
        document.getElementById('jobsPreview').innerHTML = '<p class="text-red-600 text-sm text-center">Could not load jobs.</p>';
      });

    function escapeHtml(str) {
      return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
  </script>
</body>
</html>
