<?php
/**
 * SIGAT API - Captação de Recursos (Fundraising)
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
        $stmt = $pdo->query("SELECT * FROM fundraising ORDER BY deadline ASC");
        jsonResponse($stmt->fetchAll());
        break;

    case 'POST':
        $data = getJsonBody();
        $fId = generateId();
        $stmt = $pdo->prepare("INSERT INTO fundraising (id, title, funder, deadline, total_value, requested_value, status, link, description, observations) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$fId, sanitize($data['title'] ?? ''), sanitize($data['funder'] ?? ''), $data['deadline'] ?? null, $data['total_value'] ?? 0, $data['requested_value'] ?? 0, $data['status'] ?? 'Identificado', $data['link'] ?? '', $data['description'] ?? '', $data['observations'] ?? '']);
        addAuditLog($pdo, $user['id'], $user['nome'], 'FUNDRAISING_CREATE', 'fundraising', $fId, $data['title'] ?? '');
        $stmt = $pdo->prepare("SELECT * FROM fundraising WHERE id = ?");
        $stmt->execute([$fId]);
        jsonResponse($stmt->fetch(), 201);
        break;

    case 'PUT':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $data = getJsonBody();
        $fields = [];
        $params = [];
        foreach (['title', 'funder', 'deadline', 'total_value', 'requested_value', 'status', 'link', 'description', 'observations'] as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (!empty($fields)) {
            $params[] = $id;
            $pdo->prepare("UPDATE fundraising SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
        }
        addAuditLog($pdo, $user['id'], $user['nome'], 'FUNDRAISING_UPDATE', 'fundraising', $id);
        $stmt = $pdo->prepare("SELECT * FROM fundraising WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse($stmt->fetch());
        break;

    case 'DELETE':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $pdo->prepare("DELETE FROM fundraising WHERE id = ?")->execute([$id]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'FUNDRAISING_DELETE', 'fundraising', $id);
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
