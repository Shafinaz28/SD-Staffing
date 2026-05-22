<?php

require_once __DIR__ . '/auth.php';
logoutAdmin();
header('Location: ../admin-login.html?logout=1');
exit;
