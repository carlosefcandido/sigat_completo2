<?php
/**
 * SIGAT API - Organização (singleton)
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
        $stmt = $pdo->query("SELECT * FROM organization WHERE id = 1");
        $org = $stmt->fetch();
        if (!$org)
            jsonResponse(['error' => 'Organização não encontrada'], 404);
        jsonResponse($org);
        break;

    case 'PUT':
        requireRole(['ADMIN', 'COORDENAÇÃO'], true);
        $data = getJsonBody();
        $fields = [];
        $params = [];
        foreach (['name', 'logo_url', 'cnpj', 'foundation_year', 'email', 'phone', 'address', 'territory', 'audience', 'beneficiaries_count', 'team_size', 'mission', 'vision', 'org_values', 'history'] as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (!empty($fields)) {
            $params[] = 1;
            $pdo->prepare("UPDATE organization SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
        }
        addAuditLog($pdo, $user['id'], $user['nome'], 'ORGANIZATION_UPDATE', 'organization', '1');
        $stmt = $pdo->query("SELECT * FROM organization WHERE id = 1");
        jsonResponse($stmt->fetch());
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
