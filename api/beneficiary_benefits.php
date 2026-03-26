<?php
/**
 * SIGAT API - Benefícios de Beneficiários
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
$beneficiary_id = $_GET['beneficiary_id'] ?? null;

switch ($method) {
    case 'GET':
        if ($beneficiary_id) {
            $stmt = $pdo->prepare("SELECT * FROM beneficiary_benefits WHERE beneficiary_id = ? ORDER BY date_received ASC, created_at ASC");
            $stmt->execute([$beneficiary_id]);
            jsonResponse($stmt->fetchAll());
        }
        jsonResponse(['error' => 'beneficiary_id não informado'], 400);
        break;

    case 'POST':
        requireRole(['ADMIN', 'COORDENAÇÃO'], true);
        $data = getJsonBody();
        if (empty($data['beneficiary_id']) || empty($data['benefit_name']) || empty($data['date_received'])) {
            jsonResponse(['error' => 'Campos obrigatórios: beneficiary_id, benefit_name, date_received'], 400);
        }

        $benefitId = generateId();
        
        $stmt = $pdo->prepare("INSERT INTO beneficiary_benefits (id, beneficiary_id, benefit_name, date_received, observations) VALUES (?,?,?,?,?)");
        $stmt->execute([
            $benefitId,
            $data['beneficiary_id'],
            sanitize($data['benefit_name']),
            $data['date_received'],
            sanitize($data['observations'] ?? null)
        ]);

        addAuditLog($pdo, $user['id'], $user['nome'], 'ADD_BENEFIT', 'beneficiary', $data['beneficiary_id'], $data['benefit_name']);

        $stmt = $pdo->prepare("SELECT * FROM beneficiary_benefits WHERE id = ?");
        $stmt->execute([$benefitId]);
        jsonResponse($stmt->fetch(), 201);
        break;

    case 'DELETE':
        requireRole(['ADMIN', 'COORDENAÇÃO'], true);
        if (!$id) jsonResponse(['error' => 'ID obrigatório'], 400);
        
        $stmt = $pdo->prepare("SELECT beneficiary_id, benefit_name FROM beneficiary_benefits WHERE id = ?");
        $stmt->execute([$id]);
        $benefit = $stmt->fetch();
        
        if ($benefit) {
            $pdo->prepare("DELETE FROM beneficiary_benefits WHERE id = ?")->execute([$id]);
            addAuditLog($pdo, $user['id'], $user['nome'], 'REMOVE_BENEFIT', 'beneficiary', $benefit['beneficiary_id'], $benefit['benefit_name']);
        }
        
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
