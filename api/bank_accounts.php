<?php
/**
 * SIGAT API - Contas Bancárias
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

requireRole(['ADMIN', 'FINANCEIRO'], true);

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT ba.*, p.name as project_name FROM bank_accounts ba LEFT JOIN projects p ON ba.project_id = p.id WHERE ba.id = ?");
            $stmt->execute([$id]);
            $account = $stmt->fetch();
            if (!$account) jsonResponse(['error' => 'Conta não encontrada'], 404);
            jsonResponse($account);
        }
        $stmt = $pdo->query("SELECT ba.*, p.name as project_name FROM bank_accounts ba LEFT JOIN projects p ON ba.project_id = p.id ORDER BY ba.bank_name, ba.account_number");
        jsonResponse($stmt->fetchAll());
        break;

    case 'POST':
        $data = getJsonBody();
        if (empty($data['bank_name']) || empty($data['account_number'])) {
            jsonResponse(['error' => 'Nome do banco e número da conta são obrigatórios'], 400);
        }
        $baId = generateId();
        $stmt = $pdo->prepare("INSERT INTO bank_accounts (id, bank_name, agency, account_number, account_type, holder_name, holder_document, pix_key, project_id, is_active, observations, created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $baId,
            sanitize($data['bank_name']),
            $data['agency'] ?? null,
            $data['account_number'],
            $data['account_type'] ?? 'Corrente',
            $data['holder_name'] ?? null,
            $data['holder_document'] ?? null,
            $data['pix_key'] ?? null,
            $data['project_id'] ?: null,
            $data['is_active'] ?? 1,
            $data['observations'] ?? null,
            $user['nome']
        ]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'BANK_ACCOUNT_CREATE', 'bank_account', $baId);
        $stmt = $pdo->prepare("SELECT ba.*, p.name as project_name FROM bank_accounts ba LEFT JOIN projects p ON ba.project_id = p.id WHERE ba.id = ?");
        $stmt->execute([$baId]);
        jsonResponse($stmt->fetch(), 201);
        break;

    case 'PUT':
        if (!$id) jsonResponse(['error' => 'ID obrigatório'], 400);
        $data = getJsonBody();
        $fields = [];
        $params = [];
        foreach (['bank_name', 'agency', 'account_number', 'account_type', 'holder_name', 'holder_document', 'pix_key', 'observations'] as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (array_key_exists('project_id', $data)) {
            $fields[] = 'project_id = ?';
            $params[] = $data['project_id'] ?: null;
        }
        if (isset($data['is_active'])) {
            $fields[] = 'is_active = ?';
            $params[] = $data['is_active'] ? 1 : 0;
        }
        if (!empty($fields)) {
            $params[] = $id;
            $pdo->prepare("UPDATE bank_accounts SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
        }
        addAuditLog($pdo, $user['id'], $user['nome'], 'BANK_ACCOUNT_UPDATE', 'bank_account', $id);
        $stmt = $pdo->prepare("SELECT ba.*, p.name as project_name FROM bank_accounts ba LEFT JOIN projects p ON ba.project_id = p.id WHERE ba.id = ?");
        $stmt->execute([$id]);
        jsonResponse($stmt->fetch());
        break;

    case 'DELETE':
        if (!$id) jsonResponse(['error' => 'ID obrigatório'], 400);
        // Check if account is used by transactions
        $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM transactions WHERE bank_account_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetch()['cnt'];
        if ($count > 0) {
            jsonResponse(['error' => "Esta conta possui $count transação(ões) vinculada(s). Desvincule-as antes de excluir."], 400);
        }
        $pdo->prepare("DELETE FROM bank_accounts WHERE id = ?")->execute([$id]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'BANK_ACCOUNT_DELETE', 'bank_account', $id);
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
