<?php
/**
 * SIGAT API - Usuário atual
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth(true);

$user = getCurrentUser();
jsonResponse($user);
