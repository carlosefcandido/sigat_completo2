<?php
/**
 * SIGAT API - Diagnóstico (SWOT)
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
        $stmt = $pdo->query("SELECT d.*, p.name as project_name FROM diagnoses d LEFT JOIN projects p ON d.project_id = p.id ORDER BY d.updated_at DESC");
        jsonResponse($stmt->fetchAll());
        break;

    case 'POST':
    case 'PUT':
        $data = getJsonBody();
        $projectId = $data['project_id'] ?? '';
        if (empty($projectId))
            jsonResponse(['error' => 'project_id obrigatório'], 400);

        // Upsert
        $existing = $pdo->prepare("SELECT id FROM diagnoses WHERE project_id = ?");
        $existing->execute([$projectId]);

        if ($existing->fetch()) {
            $pdo->prepare("UPDATE diagnoses SET strengths = ?, weaknesses = ?, opportunities = ?, threats = ? WHERE project_id = ?")
                ->execute([$data['strengths'] ?? '', $data['weaknesses'] ?? '', $data['opportunities'] ?? '', $data['threats'] ?? '', $projectId]);
        } else {
            $dId = $id ?? generateId();
            $pdo->prepare("INSERT INTO diagnoses (id, project_id, strengths, weaknesses, opportunities, threats) VALUES (?,?,?,?,?,?)")
                ->execute([$dId, $projectId, $data['strengths'] ?? '', $data['weaknesses'] ?? '', $data['opportunities'] ?? '', $data['threats'] ?? '']);
        }

        addAuditLog($pdo, $user['id'], $user['nome'], 'DIAGNOSIS_UPDATE', 'diagnosis', $projectId);

        $stmt = $pdo->prepare("SELECT * FROM diagnoses WHERE project_id = ?");
        $stmt->execute([$projectId]);
        jsonResponse($stmt->fetch());
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
