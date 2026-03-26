<?php
/**
 * SIGAT API - Forgot Password
 * Suporta solicitação de token e reset de senha
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getConnection();
$method = getRequestMethod();
$data = getJsonBody();

if ($method === 'POST') {
    $action = $_GET['action'] ?? 'request';

    if ($action === 'request') {
        $email = $data['email'] ?? '';
        if (empty($email))
            jsonResponse(['error' => 'Email é obrigatório'], 400);

        $stmt = $pdo->prepare("SELECT id, nome FROM users WHERE email = ? AND ativo = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            // Por segurança, não confirmamos se o email existe ou não
            jsonResponse(['success' => true, 'message' => 'Se o email existir, as instruções foram enviadas.']);
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+2 hours'));

        $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?")
            ->execute([$token, $expires, $user['id']]);

        // No ambiente real, aqui enviaria um email. 
        // Como não temos SMTP configurado, vamos registrar no log de auditoria para fins de teste/emergência
        addAuditLog($pdo, $user['id'], $user['nome'], 'PASSWORD_RESET_REQUESTED', 'users', $user['id'], "Token: $token");

        // Simulação de envio de email
        // mail($email, "Recuperação de Senha - SIGAT", "Seu token: $token");

        jsonResponse([
            'success' => true,
            'message' => 'Instruções de recuperação enviadas para o seu email.',
            'debug_token' => $token // Remova em produção real se tiver email funcional
        ]);

    } elseif ($action === 'reset') {
        $token = $data['token'] ?? '';
        $newPassword = $data['password'] ?? '';

        if (empty($token) || empty($newPassword)) {
            jsonResponse(['error' => 'Token e nova senha são obrigatórios'], 400);
        }

        if (strlen($newPassword) < 6) {
            jsonResponse(['error' => 'A senha deve ter pelo menos 6 caracteres'], 400);
        }

        $stmt = $pdo->prepare("SELECT id, nome FROM users WHERE reset_token = ? AND reset_expires > NOW() AND ativo = 1");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            jsonResponse(['error' => 'Token inválido ou expirado'], 400);
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET senha_hash = ?, reset_token = NULL, reset_expires = NULL, must_change_password = 0 WHERE id = ?")
            ->execute([$hash, $user['id']]);

        addAuditLog($pdo, $user['id'], $user['nome'], 'PASSWORD_RESET_SUCCESS', 'users', $user['id']);

        jsonResponse(['success' => true, 'message' => 'Senha alterada com sucesso! Agora você pode entrar.']);
    }
} else {
    jsonResponse(['error' => 'Método não permitido'], 405);
}
