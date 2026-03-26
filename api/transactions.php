<?php
/**
 * SIGAT API - Transações Financeiras
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth(true);
$pdo = getConnection();
$user = getCurrentUser();
$method = getRequestMethod();
$id = $_GET['id'] ?? null;

switch ($method) {
    case 'GET':
        requireRole(['ADMIN', 'FINANCEIRO'], true);
        $stmt = $pdo->query("SELECT t.*, p.name as project_name FROM transactions t LEFT JOIN projects p ON t.project_id = p.id ORDER BY t.date DESC, t.created_at DESC");
        jsonResponse($stmt->fetchAll());
        break;

    case 'POST':
        requireRole(['ADMIN', 'FINANCEIRO'], true);
        $data = getJsonBody();
        $tId = generateId();

        $stmt = $pdo->prepare("INSERT INTO transactions (id, description, type, category, value, payment_method, date, due_date, is_recurring, recurrence_period, project_id, status, attachment_url, observations, created_by, created_by_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $tId,
            sanitize($data['description'] ?? ''),
            $data['type'] ?? 'DESPESA',
            $data['category'] ?? '',
            $data['value'] ?? 0,
            $data['payment_method'] ?? '',
            $data['date'] ?? date('Y-m-d'),
            $data['due_date'] ?? null,
            $data['is_recurring'] ?? 0,
            $data['recurrence_period'] ?? null,
            $data['project_id'] ?? null,
            $data['status'] ?? 'Pendente',
            $data['attachment_url'] ?? null,
            $data['observations'] ?? '',
            $user['nome'],
            $user['id']
        ]);

        addAuditLog($pdo, $user['id'], $user['nome'], 'FINANCE_CREATE', 'transaction', $tId);

        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ?");
        $stmt->execute([$tId]);
        jsonResponse($stmt->fetch(), 201);
        break;

    case 'DELETE':
        requireRole(['ADMIN', 'FINANCEIRO'], true);
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $pdo->prepare("DELETE FROM transactions WHERE id = ?")->execute([$id]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'FINANCE_DELETE', 'transaction', $id);
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
