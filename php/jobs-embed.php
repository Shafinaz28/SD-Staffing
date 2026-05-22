<?php
header('Content-Type: application/javascript; charset=utf-8');

require_once __DIR__ . '/load-jobs.php';

$jobs = sd_load_jobs();
echo 'window.__SD_JOBS__ = ' . json_encode($jobs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) . ';';
