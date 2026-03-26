<?php
/**
 * SIGAT API - Log de Auditoria
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth(true);
requireRole(['ADMIN'], true);
$pdo = getConnection();

$stmt = $pdo->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 500");
jsonResponse($stmt->fetchAll());
