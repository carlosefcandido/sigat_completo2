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

$action = $_GET['action'] ?? null;
$dateStart = $_GET['date_start'] ?? null;
$dateEnd = $_GET['date_end'] ?? null;
$userName = $_GET['user_name'] ?? null;

$sql = "SELECT * FROM audit_logs WHERE 1=1";
$params = [];

if ($action) {
    $sql .= " AND action = ?";
    $params[] = $action;
}
if ($dateStart) {
    $sql .= " AND date(created_at) >= ?";
    $params[] = $dateStart;
}
if ($dateEnd) {
    $sql .= " AND date(created_at) <= ?";
    $params[] = $dateEnd;
}
if ($userName) {
    $sql .= " AND user_name LIKE ?";
    $params[] = '%' . $userName . '%';
}

$sql .= " ORDER BY created_at DESC LIMIT 500";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
jsonResponse($stmt->fetchAll());
