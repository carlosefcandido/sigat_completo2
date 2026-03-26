<?php
/**
 * SIGAT API - Turmas (Classes)
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
            $stmt = $pdo->prepare("SELECT c.*, GROUP_CONCAT(cb.beneficiary_id) as beneficiary_ids FROM classes c LEFT JOIN class_beneficiaries cb ON c.id = cb.class_id WHERE c.id = ? GROUP BY c.id");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            if (!$item)
                jsonResponse(['error' => 'Não encontrado'], 404);
            $item['beneficiary_ids'] = $item['beneficiary_ids'] ? explode(',', $item['beneficiary_ids']) : [];
            $item['days_of_week'] = json_decode($item['days_of_week_json'] ?? '[]', true);
            jsonResponse($item);
        }
        if ($user['perfil'] === 'PROFESSOR') {
            $stmt = $pdo->prepare("SELECT c.*, u.nome as teacher_name, p.name as project_name, GROUP_CONCAT(cb.beneficiary_id) as beneficiary_ids FROM classes c LEFT JOIN users u ON c.teacher_id = u.id LEFT JOIN projects p ON c.project_id = p.id LEFT JOIN class_beneficiaries cb ON c.id = cb.class_id WHERE c.teacher_id = ? GROUP BY c.id ORDER BY c.name");
            $stmt->execute([$user['id']]);
        } else {
            $stmt = $pdo->query("SELECT c.*, u.nome as teacher_name, p.name as project_name, GROUP_CONCAT(cb.beneficiary_id) as beneficiary_ids FROM classes c LEFT JOIN users u ON c.teacher_id = u.id LEFT JOIN projects p ON c.project_id = p.id LEFT JOIN class_beneficiaries cb ON c.id = cb.class_id GROUP BY c.id ORDER BY c.name");
        }
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $row['beneficiary_ids'] = $row['beneficiary_ids'] ? explode(',', $row['beneficiary_ids']) : [];
            $row['days_of_week'] = json_decode($row['days_of_week_json'] ?? '[]', true);
        }
        jsonResponse($rows);
        break;

    case 'POST':
        requireRole(['ADMIN', 'COORDENAÇÃO'], true);
        $data = getJsonBody();
        $classId = 'TURMA-' . strtoupper(generateId());

        $stmt = $pdo->prepare("INSERT INTO classes (id, name, project_id, teacher_id, schedule, days_of_week_json) VALUES (?,?,?,?,?,?)");
        $stmt->execute([
            $classId,
            sanitize($data['name'] ?? ''),
            $data['project_id'] ?? null,
            $data['teacher_id'] ?? null,
            sanitize($data['schedule'] ?? ''),
            json_encode($data['days_of_week'] ?? [])
        ]);

        // Vincular beneficiários
        if (!empty($data['beneficiary_ids'])) {
            $insertBenef = $pdo->prepare("INSERT IGNORE INTO class_beneficiaries (class_id, beneficiary_id) VALUES (?, ?)");
            foreach ($data['beneficiary_ids'] as $bId) {
                $insertBenef->execute([$classId, $bId]);
            }
        }

        addAuditLog($pdo, $user['id'], $user['nome'], 'CLASS_CREATE', 'class', $classId);

        $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
        $stmt->execute([$classId]);
        jsonResponse($stmt->fetch(), 201);
        break;

    case 'PUT':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $data = getJsonBody();

        $fields = [];
        $params = [];

        if (isset($data['name'])) {
            $fields[] = 'name = ?';
            $params[] = sanitize($data['name']);
        }
        if (isset($data['project_id'])) {
            $fields[] = 'project_id = ?';
            $params[] = $data['project_id'];
        }
        if (isset($data['teacher_id'])) {
            $fields[] = 'teacher_id = ?';
            $params[] = $data['teacher_id'];
        }
        if (isset($data['schedule'])) {
            $fields[] = 'schedule = ?';
            $params[] = sanitize($data['schedule']);
        }
        if (isset($data['days_of_week'])) {
            $fields[] = 'days_of_week_json = ?';
            $params[] = json_encode($data['days_of_week']);
        }

        if (!empty($fields)) {
            $params[] = $id;
            $pdo->prepare("UPDATE classes SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
        }

        // Atualizar beneficiários
        if (isset($data['beneficiary_ids'])) {
            $pdo->prepare("DELETE FROM class_beneficiaries WHERE class_id = ?")->execute([$id]);
            $insertBenef = $pdo->prepare("INSERT INTO class_beneficiaries (class_id, beneficiary_id) VALUES (?, ?)");
            foreach ($data['beneficiary_ids'] as $bId) {
                $insertBenef->execute([$id, $bId]);
            }
        }

        addAuditLog($pdo, $user['id'], $user['nome'], 'CLASS_UPDATE', 'class', $id);

        $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse($stmt->fetch());
        break;

    case 'DELETE':
        requireRole(['ADMIN'], true);
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $pdo->prepare("DELETE FROM classes WHERE id = ?")->execute([$id]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'CLASS_DELETE', 'class', $id);
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
