# SD Staffing website

## Jobs: Google Sheet → JSON → website

```
Google Form  →  Google Sheet  →  jobs.json format  →  job.html
```

All sheet columns map to `data/jobs.json`:

| Sheet column | JSON field |
|--------------|------------|
| Timestamp | `timestamp` |
| Title | `title` |
| Company | `company` |
| Category | `category` |
| Location (Select city) | `location` |
| Area / Locality | `area` |
| Salary | `salary` |
| Experience | `experience` |

### On the website (automatic)

`job.html` fetches **all rows** from your Google Sheet as JSON, saves them in the browser cache, and shows job cards. It falls back to **`data/jobs.json`** if the sheet is not reachable.

Refresh every **45 seconds** for new Form entries.

### Store data in `data/jobs.json` (not only on screen)

The website **shows** JSON from Google. To **save** the same data into `data/jobs.json`:

| How you open the site | jobs.json updates? |
|-------------------------|-------------------|
| **XAMPP** `http://localhost/SDstaffing/job.html` | Yes — auto-saves every refresh (uses `api/save-jobs.php`) |
| **Double-click** `start-website.bat` | Yes — syncs file before opening browser |
| **Live Server** / `file://` | No auto file write — double-click **`sync-jobs.bat`** |

Manual sync anytime:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/sync-jobs-from-sheet.ps1
```

Or double-click **`sync-jobs.bat`**.

**Live JSON API:**  
https://script.google.com/macros/s/AKfycbyI_DYlw2ki-OkCjImjGqaeaontFMo8FWj6S0gxDNhhVZghPR4vIb7kEut_os9xF1BljQ/exec

Configured in `job.html` (`SHEETS_JOBS_API`) and `scripts/sync-jobs-from-sheet.ps1`.

**Your sheet:**  
https://docs.google.com/spreadsheets/d/1ndJnRvRoTkJcbXCn0gzOOQNPH_XzpcVd6JuIceXqaMo/edit?gid=274791102

## Quick start

Open `job.html` or run `start-website.bat`.
