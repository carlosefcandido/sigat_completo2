<?php
/**
 * SIGAT API - Gestão de Usuários
 * GET: Lista usuários | POST: Cria usuário | PUT: Atualiza | DELETE: Remove
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth(true);
$pdo = getConnection();
$user = getCurrentUser();
$method = getRequestMethod();

// ID do usuário na URL (se houver)
$userId = isset($_GET['id']) ? intval($_GET['id']) : null;

switch ($method) {
    case 'GET':
        requireRole(['ADMIN'], true);
        $stmt = $pdo->query("SELECT id, nome, email, perfil, ativo, avatar, must_change_password, login_attempts, created_at, last_login FROM users ORDER BY nome");
        jsonResponse($stmt->fetchAll());
        break;

    case 'POST':
        requireRole(['ADMIN'], true);
        $data = getJsonBody();

        $nome = sanitize($data['nome'] ?? '');
        $email = sanitize($data['email'] ?? '');
        $senha = $data['senha'] ?? '';
        $perfil = $data['perfil'] ?? 'PROFESSOR';

        if (empty($nome) || empty($email) || empty($senha)) {
            jsonResponse(['error' => 'Nome, email e senha são obrigatórios'], 400);
        }

        // Verificar email único
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            jsonResponse(['error' => 'Email já cadastrado'], 409);
        }

        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha_hash, perfil, ativo, must_change_password) VALUES (?, ?, ?, ?, 1, 1)");
        $stmt->execute([$nome, $email, $hash, $perfil]);

        $newId = $pdo->lastInsertId();
        addAuditLog($pdo, $user['id'], $user['nome'], 'USER_CREATE', 'user', $newId, $email);

        jsonResponse(['success' => true, 'id' => $newId], 201);
        break;

    case 'PUT':
    case 'PATCH':
        $data = getJsonBody();
        $targetId = $userId ?? ($data['id'] ?? null);

        if (!$targetId) {
            jsonResponse(['error' => 'ID do usuário é obrigatório'], 400);
        }

        // Permitir que o próprio usuário mude senha, ou admin edite qualquer um
        if ($user['perfil'] !== 'ADMIN' && $user['id'] != $targetId) {
            jsonResponse(['error' => 'Acesso negado'], 403);
        }

        // Se é mudança de senha
        if (isset($data['senha'])) {
            $hash = password_hash($data['senha'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET senha_hash = ?, must_change_password = 0 WHERE id = ?");
            $stmt->execute([$hash, $targetId]);

            // Se é o próprio usuário, atualizar sessão
            if ($user['id'] == $targetId) {
                $_SESSION['must_change'] = 0;
            }

            addAuditLog($pdo, $user['id'], $user['nome'], 'PASSWORD_CHANGE', 'user', $targetId);
            jsonResponse(['success' => true]);
        }

        // Atualização geral (admin)
        if ($user['perfil'] === 'ADMIN') {
            $fields = [];
            $params = [];

            if (isset($data['nome'])) {
                $fields[] = 'nome = ?';
                $params[] = sanitize($data['nome']);
            }
            if (isset($data['email'])) {
                $fields[] = 'email = ?';
                $params[] = sanitize($data['email']);
            }
            if (isset($data['perfil'])) {
                $fields[] = 'perfil = ?';
                $params[] = $data['perfil'];
            }
            if (isset($data['ativo'])) {
                $fields[] = 'ativo = ?';
                $params[] = $data['ativo'] ? 1 : 0;
            }
            if (isset($data['avatar'])) {
                $fields[] = 'avatar = ?';
                $params[] = $data['avatar'];
            }

            if (!empty($fields)) {
                $params[] = $targetId;
                $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
                $pdo->prepare($sql)->execute($params);
                addAuditLog($pdo, $user['id'], $user['nome'], 'USER_UPDATE', 'user', $targetId);
            }
        }

        jsonResponse(['success' => true]);
        break;

    case 'DELETE':
        requireRole(['ADMIN'], true);
        $targetId = $userId;
        if (!$targetId)
            jsonResponse(['error' => 'ID obrigatório'], 400);

        $pdo->prepare("UPDATE users SET ativo = 0 WHERE id = ?")->execute([$targetId]);
        addAuditLog($pdo, $user['id'], $user['nome'], 'USER_DELETE', 'user', $targetId);
        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['error' => 'Método não permitido'], 405);
}
