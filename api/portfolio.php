<?php
/**
 * SIGAT API - Portfólio
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
        $stmt = $pdo->query("SELECT pi.*, p.name as project_name FROM portfolio_items pi LEFT JOIN projects p ON pi.project_id = p.id ORDER BY pi.year DESC");
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['photos'] = json_decode($r['photos_json'] ?? '[]', true);
            $r['videos'] = json_decode($r['videos_json'] ?? '[]', true);
            $r['results'] = json_decode($r['results_json'] ?? '{}', true);
            $r['testimonials'] = json_decode($r['testimonials_json'] ?? '[]', true);
            $r['partners'] = json_decode($r['partners_json'] ?? '[]', true);
        }
        jsonResponse($rows);
        break;

    case 'POST':
        $data = getJsonBody();
        $pId = generateId();
        $stmt = $pdo->prepare("INSERT INTO portfolio_items (id, project_id, year, location, beneficiaries_count, description, photos_json, videos_json, results_json, testimonials_json, partners_json) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$pId, $data['project_id'] ?? null, $data['year'] ?? date('Y'), sanitize($data['location'] ?? ''), $data['beneficiaries_count'] ?? '', $data['description'] ?? '', json_encode($data['photos'] ?? []), json_encode($data['videos'] ?? []), json_encode($data['results'] ?? new stdClass()), json_encode($data['testimonials'] ?? []), json_encode($data['partners'] ?? [])]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'PORTFOLIO_CREATE', 'portfolio', $pId);
        $stmt = $pdo->prepare("SELECT * FROM portfolio_items WHERE id = ?");
        $stmt->execute([$pId]);
        jsonResponse($stmt->fetch(), 201);
        break;

    case 'PUT':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $data = getJsonBody();
        $fields = [];
        $params = [];
        foreach (['project_id', 'year', 'location', 'beneficiaries_count', 'description'] as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        foreach (['photos' => 'photos_json', 'videos' => 'videos_json', 'results' => 'results_json', 'testimonials' => 'testimonials_json', 'partners' => 'partners_json'] as $k => $col) {
            if (isset($data[$k])) {
                $fields[] = "$col = ?";
                $params[] = json_encode($data[$k]);
            }
        }
        if (!empty($fields)) {
            $params[] = $id;
            $pdo->prepare("UPDATE portfolio_items SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
        }
        addAuditLog($pdo, $user['id'], $user['nome'], 'PORTFOLIO_UPDATE', 'portfolio', $id);
        $stmt = $pdo->prepare("SELECT * FROM portfolio_items WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse($stmt->fetch());
        break;

    case 'DELETE':
        if (!$id)
            jsonResponse(['error' => 'ID obrigatório'], 400);
        $pdo->prepare("DELETE FROM portfolio_items WHERE id = ?")->execute([$id]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'PORTFOLIO_DELETE', 'portfolio', $id);
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
