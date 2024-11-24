<?php
session_start();

function generateCsrfToken()
{
    return bin2hex(random_bytes(32));
}

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generateCsrfToken();
}
?>