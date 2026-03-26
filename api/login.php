<?php
/**
 * SIGAT API - Login
 * POST: Autenticação do usuário
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$method = getRequestMethod();
if ($method !== 'POST') {
    jsonResponse(['error' => 'Método não permitido'], 405);
}

$data = getJsonBody();
$email = $data['email'] ?? '';
$senha = $data['senha'] ?? '';

if (empty($email) || empty($senha)) {
    jsonResponse(['error' => 'Email e senha são obrigatórios'], 400);
}

$pdo = getConnection();

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND ativo = 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    jsonResponse(['error' => 'Credenciais inválidas'], 401);
}

// Verificar senha
if (!password_verify($senha, $user['senha_hash'])) {
    // Incrementar tentativas
    $pdo->prepare("UPDATE users SET login_attempts = login_attempts + 1 WHERE id = ?")->execute([$user['id']]);
    jsonResponse(['error' => 'Credenciais inválidas'], 401);
}

// Resetar tentativas e atualizar last_login
$pdo->prepare("UPDATE users SET login_attempts = 0, last_login = NOW() WHERE id = ?")->execute([$user['id']]);

// Fazer login na sessão
loginUser($user);

// Log de auditoria
addAuditLog($pdo, $user['id'], $user['nome'], 'AUTH_LOGIN');

jsonResponse([
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'nome' => $user['nome'],
        'email' => $user['email'],
        'perfil' => $user['perfil'],
        'avatar' => $user['avatar'],
        'must_change_password' => (bool) $user['must_change_password']
    ]
]);
