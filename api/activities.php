<?php
/**
 * SIGAT API - Quadro de Atividades
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
        $stmt = $pdo->query("SELECT * FROM activity_board ORDER BY day_of_week, start_time");
        jsonResponse($stmt->fetchAll());
        break;

    case 'POST':
        $data = getJsonBody();
        $aId = generateId();
        $stmt = $pdo->prepare("INSERT INTO activity_board (id, name, teacher, day_of_week, start_time, end_time, location, description) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$aId, sanitize($data['name'] ?? ''), sanitize($data['teacher'] ?? ''), $data['day_of_week'] ?? 1, $data['start_time'] ?? '', $data['end_time'] ?? '', sanitize($data['location'] ?? ''), $data['description'] ?? '']);
        addAuditLog($pdo, $user['id'], $user['nome'], 'ACTIVITY_CREATE', 'activity', $aId);
        $stmt = $pdo->prepare("SELECT * FROM activity_board WHERE id = ?");
        $stmt->execute([$aId]);
        jsonResponse($stmt->fetch(), 201);
        break;

    case 'PUT':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $data = getJsonBody();
        $fields = [];
        $params = [];
        foreach (['name', 'teacher', 'day_of_week', 'start_time', 'end_time', 'location', 'description'] as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (!empty($fields)) {
            $params[] = $id;
            $pdo->prepare("UPDATE activity_board SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
        }
        addAuditLog($pdo, $user['id'], $user['nome'], 'ACTIVITY_UPDATE', 'activity', $id);
        $stmt = $pdo->prepare("SELECT * FROM activity_board WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse($stmt->fetch());
        break;

    case 'DELETE':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $pdo->prepare("DELETE FROM activity_board WHERE id = ?")->execute([$id]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'ACTIVITY_DELETE', 'activity', $id);
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
