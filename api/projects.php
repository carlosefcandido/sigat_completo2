<?php
/**
 * SIGAT API - Projetos
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
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            if (!$item)
                jsonResponse(['error' => 'Não encontrado'], 404);
            $item['schedule'] = json_decode($item['schedule_json'] ?? '[]', true);
            $item['budget'] = json_decode($item['budget_json'] ?? '[]', true);
            $item['extended'] = json_decode($item['extended_fields_json'] ?? '{}', true);
            jsonResponse($item);
        }
        $stmt = $pdo->query("SELECT * FROM projects ORDER BY name");
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $row['schedule'] = json_decode($row['schedule_json'] ?? '[]', true);
            $row['budget'] = json_decode($row['budget_json'] ?? '[]', true);
            $row['extended'] = json_decode($row['extended_fields_json'] ?? '{}', true);
        }
        jsonResponse($rows);
        break;

    case 'POST':
        requireRole(['ADMIN', 'COORDENAÇÃO'], true);
        $data = getJsonBody();
        $projId = generateId();

        $stmt = $pdo->prepare("INSERT INTO projects (id, name, general_objective, justification, specific_objectives, methodology, communication_plan, sustainability_plan, schedule_json, budget_json, extended_fields_json, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $projId,
            sanitize($data['name'] ?? ''),
            $data['general_objective'] ?? '',
            $data['justification'] ?? '',
            $data['specific_objectives'] ?? '',
            $data['methodology'] ?? '',
            $data['communication_plan'] ?? '',
            $data['sustainability_plan'] ?? '',
            json_encode($data['schedule'] ?? []),
            json_encode($data['budget'] ?? []),
            json_encode($data['extended'] ?? new stdClass()),
            $data['status'] ?? 'DRAFT'
        ]);

        addAuditLog($pdo, $user['id'], $user['nome'], 'PROJECT_CREATE', 'project', $projId);

        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$projId]);
        jsonResponse($stmt->fetch(), 201);
        break;

    case 'PUT':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $data = getJsonBody();

        $fields = [];
        $params = [];
        $textFields = ['name', 'general_objective', 'justification', 'specific_objectives', 'methodology', 'communication_plan', 'sustainability_plan', 'status'];

        foreach ($textFields as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (isset($data['schedule'])) {
            $fields[] = 'schedule_json = ?';
            $params[] = json_encode($data['schedule']);
        }
        if (isset($data['budget'])) {
            $fields[] = 'budget_json = ?';
            $params[] = json_encode($data['budget']);
        }
        if (isset($data['extended'])) {
            $fields[] = 'extended_fields_json = ?';
            $params[] = json_encode($data['extended']);
        }

        if (!empty($fields)) {
            $params[] = $id;
            $pdo->prepare("UPDATE projects SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
        }

        addAuditLog($pdo, $user['id'], $user['nome'], 'PROJECT_UPDATE', 'project', $id);

        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse($stmt->fetch());
        break;

    case 'DELETE':
        requireRole(['ADMIN'], true);
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'PROJECT_DELETE', 'project', $id);
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
