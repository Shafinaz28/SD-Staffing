@echo off

title SD Staffing

set "SRC=%~dp0"

set "DEST=C:\xampp\htdocs\SDstaffing"

set "XAMPP=C:\xampp"

echo Updating jobs.json from Google Sheet...
powershell -ExecutionPolicy Bypass -File "%SRC%scripts\sync-jobs-from-sheet.ps1"

echo Syncing to XAMPP...

if not exist "%DEST%" mkdir "%DEST%"

robocopy "%SRC%" "%DEST%" /E /XD .git /XF .gitignore /NFL /NDL /NJH /NJS /NP >nul

if exist "%XAMPP%\apache_start.bat" call "%XAMPP%\apache_start.bat" >nul 2>&1

timeout /t 2 /nobreak >nul

start "" "http://localhost/SDstaffing/index.html"

echo.
echo Home:  http://localhost/SDstaffing/index.html
echo Jobs:  http://localhost/SDstaffing/job.html
echo.
echo Static site — open HTML files directly or use Apache in XAMPP (MySQL not required).

pause
