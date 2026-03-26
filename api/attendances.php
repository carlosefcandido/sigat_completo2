<?php
/**
 * SIGAT API - Frequências
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth(true);
$pdo = getConnection();
$user = getCurrentUser();
$method = getRequestMethod();

switch ($method) {
    case 'GET':
        $classId = $_GET['class_id'] ?? null;
        $date = $_GET['date'] ?? null;

        $sql = "SELECT * FROM attendances WHERE 1=1";
        $params = [];

        if ($classId) {
            $sql .= " AND class_id = ?";
            $params[] = $classId;
        }
        if ($date) {
            $sql .= " AND date = ?";
            $params[] = $date;
        }

        // Se for professor, opcionalmente filtrar apenas as dele
        if ($user['perfil'] === 'PROFESSOR') {
            $sql .= " AND professor_id = ?";
            $params[] = $user['id'];
        }

        $sql .= " ORDER BY date DESC, created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['records'] = json_decode($r['records_json'] ?? '[]', true);
        }
        jsonResponse($rows);
        break;

    case 'POST':
        $data = getJsonBody();
        $classId = $data['class_id'] ?? null;
        $date = $data['date'] ?? date('Y-m-d');

        if (!$classId)
            jsonResponse(['error' => 'ID da turma obrigatório'], 400);

        // Check if exists (Upsert)
        $stmt = $pdo->prepare("SELECT id FROM attendances WHERE class_id = ? AND date = ?");
        $stmt->execute([$classId, $date]);
        $existing = $stmt->fetch();

        if ($existing) {
            $attId = $existing['id'];
            $stmt = $pdo->prepare("UPDATE attendances SET records_json = ?, professor_id = ? WHERE id = ?");
            $stmt->execute([json_encode($data['records'] ?? []), $user['id'], $attId]);
            $action = 'ATTENDANCE_UPDATE';
        } else {
            $attId = generateId();
            $stmt = $pdo->prepare("INSERT INTO attendances (id, class_id, date, records_json, professor_id) VALUES (?,?,?,?,?)");
            $stmt->execute([$attId, $classId, $date, json_encode($data['records'] ?? []), $user['id']]);
            $action = 'ATTENDANCE_RECORD';
        }

        addAuditLog($pdo, $user['id'], $user['nome'], $action, 'attendance', $attId, "Turma: $classId, Data: $date");

        $stmt = $pdo->prepare("SELECT * FROM attendances WHERE id = ?");
        $stmt->execute([$attId]);
        $row = $stmt->fetch();
        $row['records'] = json_decode($row['records_json'] ?? '[]', true);
        jsonResponse($row, $existing ? 200 : 201);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
