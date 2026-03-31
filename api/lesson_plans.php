<?php
/**
 * SIGAT API - Planos de Aula
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
            $stmt = $pdo->prepare("SELECT lp.*, c.name as class_name FROM lesson_plans lp LEFT JOIN classes c ON lp.class_id = c.id WHERE lp.id = ?");
            $stmt->execute([$id]);
            $plan = $stmt->fetch();
            if (!$plan) jsonResponse(['error' => 'Plano não encontrado'], 404);
            jsonResponse($plan);
        } else {
            $stmt = $pdo->query("SELECT lp.*, c.name as class_name FROM lesson_plans lp LEFT JOIN classes c ON lp.class_id = c.id ORDER BY lp.month DESC, lp.created_at DESC");
            jsonResponse($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = getJsonBody();
        $lpId = generateId();
        $stmt = $pdo->prepare("INSERT INTO lesson_plans (id, class_id, month, objective, content, methodology, materials, observations, professor_id) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$lpId, $data['class_id'] ?? '', $data['month'] ?? '', $data['objective'] ?? '', $data['content'] ?? '', $data['methodology'] ?? '', $data['materials'] ?? '', $data['observations'] ?? '', $user['id']]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'LESSON_PLAN_CREATE', 'lesson_plan', $lpId);
        $stmt = $pdo->prepare("SELECT * FROM lesson_plans WHERE id = ?");
        $stmt->execute([$lpId]);
        jsonResponse($stmt->fetch(), 201);
        break;

    case 'PUT':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $data = getJsonBody();
        $fields = [];
        $params = [];
        foreach (['objective', 'content', 'methodology', 'materials', 'observations', 'month'] as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        $fields[] = 'updated_by = ?';
        $params[] = $user['nome'];
        $params[] = $id;
        $pdo->prepare("UPDATE lesson_plans SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
        $stmt = $pdo->prepare("SELECT * FROM lesson_plans WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse($stmt->fetch());
        break;

    case 'DELETE':
        if (!$id) jsonResponse(['error' => 'ID obrigatório'], 400);
        $stmt = $pdo->prepare("SELECT id FROM lesson_plans WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) jsonResponse(['error' => 'Plano não encontrado'], 404);
        $pdo->prepare("DELETE FROM lesson_plans WHERE id = ?")->execute([$id]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'LESSON_PLAN_DELETE', 'lesson_plan', $id);
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
