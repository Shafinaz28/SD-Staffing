# SD Staffing website

## What you need (dependencies)

**Only [XAMPP](https://www.apachefriends.org/)** — Apache, MySQL, and PHP.  
There is **no** `package.json`, **no** Composer, and **no** extra libraries to install.

| Piece        | Role                          |
|-------------|-------------------------------|
| Apache      | Serves HTML + PHP             |
| MySQL       | Stores jobs and admin users   |
| PHP (PDO)   | Built into XAMPP              |
| Tailwind CDN| Loaded in admin pages only    |

## Quick start

1. Copy this folder to `C:\xampp\htdocs\SDstaffing`
2. XAMPP → start **Apache** and **MySQL**
3. Open once: http://localhost/SDstaffing/php/setup-database.php
4. Admin login: http://localhost/SDstaffing/admin-login.html  
   Default: **admin** / **admin123**

Double-click `start-website.bat` to open the home page (Apache + MySQL must be running).

## Folder layout

```
SDstaffing/
├── index.html, job.html, about.html, …   ← public website (HTML only)
├── admin-login.html                      ← staff login page
├── start-website.bat                     ← opens site in browser
├── php/                                  ← all backend code lives here
│   ├── db.php                            ← MySQL settings + connection
│   ├── auth.php                          ← login sessions
│   ├── setup-database.php                ← run once to create tables
│   ├── login.php, logout.php
│   ├── get-jobs.php, jobs-embed.php      ← public job listings
│   ├── save-job.php, update-job.php, delete-job.php
│   └── admin-job.php, admin-edit-job.php, admin-users.php
└── database/setup.sql                    ← optional reference (setup uses PHP)
```

## How it works

1. Run setup once → creates database `sd_staffing`, tables, default admin, sample jobs.
2. Staff log in at `admin-login.html` → post jobs in `php/admin-job.php`.
3. Visitors see jobs on `job.html` (data from `php/get-jobs.php`).

To change MySQL username/password, edit `php/db.php`.
