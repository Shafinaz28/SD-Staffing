@echo off

title SD Staffing

set "SRC=%~dp0"

set "DEST=C:\xampp\htdocs\SDstaffing"

set "XAMPP=C:\xampp"



echo Syncing to XAMPP...

if not exist "%DEST%" mkdir "%DEST%"

robocopy "%SRC%" "%DEST%" /E /XD .git /XF .gitignore /NFL /NDL /NJH /NJS /NP >nul



if exist "%XAMPP%\apache_start.bat" call "%XAMPP%\apache_start.bat" >nul 2>&1

if exist "%XAMPP%\mysql_start.bat" call "%XAMPP%\mysql_start.bat" >nul 2>&1

timeout /t 3 /nobreak >nul



start "" "http://localhost/SDstaffing/job.php"

echo.

echo Jobs:  http://localhost/SDstaffing/job.php

echo Home:  http://localhost/SDstaffing/index.html

echo Setup: http://localhost/SDstaffing/php/setup-database.php

echo.

echo Keep Apache + MySQL running in XAMPP.

pause

