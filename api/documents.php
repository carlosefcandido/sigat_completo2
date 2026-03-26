<?php
/**
 * SIGAT API - Documentos
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
        requireRole(['ADMIN', 'COORDENAÇÃO', 'FINANCEIRO'], true);
        $showDeleted = isset($_GET['deleted']) && $_GET['deleted'] === '1';
        $sql = $showDeleted
            ? "SELECT * FROM documents WHERE is_deleted = 1 ORDER BY created_at DESC"
            : "SELECT * FROM documents WHERE is_deleted = 0 ORDER BY created_at DESC";
        $stmt = $pdo->query($sql);
        jsonResponse($stmt->fetchAll());
        break;

    case 'POST':
        $data = getJsonBody();
        $docId = generateId();
        $status = calculateDocStatus($data['expiry_date'] ?? null);

        $stmt = $pdo->prepare("INSERT INTO documents (id, title, category, issue_date, expiry_date, observations, file_url, file_type, status, uploaded_by, uploaded_by_id) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $docId,
            sanitize($data['title'] ?? ''),
            $data['category'] ?? '',
            $data['issue_date'] ?? null,
            $data['expiry_date'] ?? null,
            $data['observations'] ?? '',
            $data['file_url'] ?? '',
            $data['file_type'] ?? 'pdf',
            $status,
            $user['nome'],
            $user['id']
        ]);

        addAuditLog($pdo, $user['id'], $user['nome'], 'UPLOAD', 'document', $docId, $data['title'] ?? '');

        $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
        $stmt->execute([$docId]);
        jsonResponse($stmt->fetch(), 201);
        break;

    case 'PUT':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $data = getJsonBody();
        $fields = [];
        $params = [];

        $allowed = ['title', 'category', 'issue_date', 'expiry_date', 'observations', 'file_url', 'file_type', 'is_deleted', 'updated_by'];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }

        // Recalcular status se data de vencimento mudar
        if (isset($data['expiry_date'])) {
            $fields[] = 'status = ?';
            $params[] = calculateDocStatus($data['expiry_date']);
        }

        if (!empty($fields)) {
            $params[] = $id;
            $pdo->prepare("UPDATE documents SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
        }

        $action = (isset($data['is_deleted']) && $data['is_deleted']) ? 'DELETE' : 'EDIT';
        addAuditLog($pdo, $user['id'], $user['nome'], $action, 'document', $id);

        $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse($stmt->fetch());
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
