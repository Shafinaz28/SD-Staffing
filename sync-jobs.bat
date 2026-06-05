@echo off
title Sync jobs from Google Sheet
powershell -ExecutionPolicy Bypass -File "%~dp0scripts\sync-jobs-from-sheet.ps1"
echo.
echo data\jobs.json is updated. Refresh job.html in the browser.
pause
