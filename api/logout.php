<?php
/**
 * SIGAT API - Logout
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

if (isAuthenticated()) {
    $user = getCurrentUser();
    $pdo = getConnection();
    addAuditLog($pdo, $user['id'], $user['nome'], 'AUTH_LOGOUT');
}

logoutUser();
header('Location: ../login.php');
exit;
