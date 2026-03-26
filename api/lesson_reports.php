<?php
/**
 * SIGAT API - Relatórios de Aula
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
        $stmt = $pdo->query("SELECT lr.*, c.name as class_name FROM lesson_reports lr LEFT JOIN classes c ON lr.class_id = c.id ORDER BY lr.month DESC");
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['entries'] = json_decode($r['entries_json'] ?? '[]', true);
        }
        jsonResponse($rows);
        break;

    case 'POST':
        $data = getJsonBody();
        $lrId = generateId();
        $stmt = $pdo->prepare("INSERT INTO lesson_reports (id, class_id, month, entries_json, professor_id) VALUES (?,?,?,?,?)");
        $stmt->execute([$lrId, $data['class_id'] ?? '', $data['month'] ?? '', json_encode($data['entries'] ?? []), $user['id']]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'LESSON_REPORT_CREATE', 'lesson_report', $lrId);
        $stmt = $pdo->prepare("SELECT * FROM lesson_reports WHERE id = ?");
        $stmt->execute([$lrId]);
        $row = $stmt->fetch();
        $row['entries'] = json_decode($row['entries_json'] ?? '[]', true);
        jsonResponse($row, 201);
        break;

    case 'PUT':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $data = getJsonBody();
        $fields = [];
        $params = [];
        if (isset($data['month'])) {
            $fields[] = 'month = ?';
            $params[] = $data['month'];
        }
        if (isset($data['entries'])) {
            $fields[] = 'entries_json = ?';
            $params[] = json_encode($data['entries']);
        }
        $fields[] = 'updated_by = ?';
        $params[] = $user['nome'];
        $params[] = $id;
        $pdo->prepare("UPDATE lesson_reports SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
        $stmt = $pdo->prepare("SELECT * FROM lesson_reports WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $row['entries'] = json_decode($row['entries_json'] ?? '[]', true);
        jsonResponse($row);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
