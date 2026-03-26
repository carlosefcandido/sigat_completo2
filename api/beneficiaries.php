<?php
/**
 * SIGAT API - Beneficiários
 * GET: Lista | POST: Cria | PUT: Atualiza | DELETE: Soft-delete
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
            $stmt = $pdo->prepare("SELECT * FROM beneficiaries WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            if (!$item)
                jsonResponse(['error' => 'Não encontrado'], 404);
            jsonResponse($item);
        }
        $showDeleted = isset($_GET['deleted']) && $_GET['deleted'] === '1';
        $sql = $showDeleted
            ? "SELECT * FROM beneficiaries WHERE is_deleted = 1 ORDER BY name"
            : "SELECT * FROM beneficiaries WHERE is_deleted = 0 ORDER BY name";
        $stmt = $pdo->query($sql);
        jsonResponse($stmt->fetchAll());
        break;

    case 'POST':
        requireRole(['ADMIN', 'COORDENAÇÃO'], true);
        $data = getJsonBody();

        $seq = getNextBeneficiarySeq($pdo);
        $benefId = generateBeneficiaryId($seq);

        $nis = !empty($data['nis_number']) ? encryptNis($data['nis_number']) : null;

        $stmt = $pdo->prepare("INSERT INTO beneficiaries (id, name, birth_date, cpf_rg, responsible_name, responsible_cpf, address, phone, school, grade, religion, race_color, photo_url, image_term_url, exit_term_url, is_pcd, pcd_type, pcd_description, needs_follow_up, medical_notes, has_cad_unico, nis_number, last_cad_unico_update) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $benefId,
            sanitize($data['name'] ?? ''),
            $data['birth_date'] ?? null,
            sanitize($data['cpf_rg'] ?? ''),
            sanitize($data['responsible_name'] ?? ''),
            sanitize($data['responsible_cpf'] ?? ''),
            sanitize($data['address'] ?? ''),
            sanitize($data['phone'] ?? ''),
            sanitize($data['school'] ?? ''),
            sanitize($data['grade'] ?? ''),
            $data['religion'] ?? 'Sem religião',
            $data['race_color'] ?? 'Prefere não declarar',
            $data['photo_url'] ?? null,
            $data['image_term_url'] ?? null,
            $data['exit_term_url'] ?? null,
            $data['is_pcd'] ?? 0,
            sanitize($data['pcd_type'] ?? null),
            sanitize($data['pcd_description'] ?? null),
            $data['needs_follow_up'] ?? 0,
            sanitize($data['medical_notes'] ?? null),
            $data['has_cad_unico'] ?? 0,
            $nis,
            $data['last_cad_unico_update'] ?? null
        ]);

        addAuditLog($pdo, $user['id'], $user['nome'], 'BENEFICIARY_CREATE', 'beneficiary', $benefId);

        $stmt = $pdo->prepare("SELECT * FROM beneficiaries WHERE id = ?");
        $stmt->execute([$benefId]);
        jsonResponse($stmt->fetch(), 201);
        break;

    case 'PUT':
        requireRole(['ADMIN', 'COORDENAÇÃO'], true);
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);

        $data = getJsonBody();
        $fields = [];
        $params = [];

        $allowedFields = ['name', 'birth_date', 'cpf_rg', 'responsible_name', 'responsible_cpf', 'address', 'phone', 'school', 'grade', 'religion', 'race_color', 'photo_url', 'image_term_url', 'exit_term_url', 'is_pcd', 'pcd_type', 'pcd_description', 'needs_follow_up', 'medical_notes', 'has_cad_unico', 'nis_number', 'last_cad_unico_update', 'is_deleted'];

        foreach ($allowedFields as $f) {
            if (array_key_exists($f, $data)) {
                $value = $data[$f];
                if ($f === 'nis_number' && $value && strpos($value, 'ENC_') !== 0) {
                    $value = encryptNis($value);
                }
                $fields[] = "$f = ?";
                $params[] = $value;
            }
        }

        if (!empty($fields)) {
            $params[] = $id;
            $sql = "UPDATE beneficiaries SET " . implode(', ', $fields) . " WHERE id = ?";
            $pdo->prepare($sql)->execute($params);
        }

        $action = (isset($data['is_deleted']) && $data['is_deleted']) ? 'BENEFICIARY_DELETE' : 'BENEFICIARY_UPDATE';
        addAuditLog($pdo, $user['id'], $user['nome'], $action, 'beneficiary', $id);

        $stmt = $pdo->prepare("SELECT * FROM beneficiaries WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse($stmt->fetch());
        break;

    case 'DELETE':
        requireRole(['ADMIN'], true);
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $pdo->prepare("UPDATE beneficiaries SET is_deleted = 1 WHERE id = ?")->execute([$id]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'BENEFICIARY_DELETE', 'beneficiary', $id);
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
