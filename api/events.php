<?php
/**
 * SIGAT API - Eventos
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
        $stmt = $pdo->query("SELECT * FROM events ORDER BY date DESC");
        jsonResponse($stmt->fetchAll());
        break;

    case 'POST':
        $data = getJsonBody();
        $eId = generateId();
        $stmt = $pdo->prepare("INSERT INTO events (id, title, date, time, location, description, organizer, status) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$eId, sanitize($data['title'] ?? ''), $data['date'] ?? date('Y-m-d'), $data['time'] ?? '', sanitize($data['location'] ?? ''), $data['description'] ?? '', sanitize($data['organizer'] ?? ''), $data['status'] ?? 'AGENDADO']);
        addAuditLog($pdo, $user['id'], $user['nome'], 'EVENT_CREATE', 'event', $eId);
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$eId]);
        jsonResponse($stmt->fetch(), 201);
        break;

    case 'PUT':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $data = getJsonBody();
        $fields = [];
        $params = [];
        foreach (['title', 'date', 'time', 'location', 'description', 'organizer', 'status'] as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (!empty($fields)) {
            $params[] = $id;
            $pdo->prepare("UPDATE events SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
        }
        addAuditLog($pdo, $user['id'], $user['nome'], 'EVENT_UPDATE', 'event', $id);
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse($stmt->fetch());
        break;

    case 'DELETE':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $pdo->prepare("DELETE FROM events WHERE id = ?")->execute([$id]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'EVENT_DELETE', 'event', $id);
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
