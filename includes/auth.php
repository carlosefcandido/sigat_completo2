<?php
/**
 * SIGAT - Autenticação e Sessão
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

/**
 * Verifica se o usuário está autenticado
 */
function isAuthenticated(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Requer autenticação - redireciona ou retorna 401
 */
function requireAuth(bool $isApi = false): void
{
    if (!isAuthenticated()) {
        if ($isApi) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Não autenticado']);
            exit;
        } else {
            header('Location: login.php');
            exit;
        }
    }

    // Verificar timeout de sessão (30 min)
    if (isset($_SESSION['last_activity'])) {
        $timeout = 30 * 60; // 30 minutos
        if (time() - $_SESSION['last_activity'] > $timeout) {
            session_destroy();
            if ($isApi) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Sessão expirada']);
                exit;
            } else {
                header('Location: login.php?expired=1');
                exit;
            }
        }
    }
    $_SESSION['last_activity'] = time();
}

/**
 * Verifica se o usuário tem um dos papéis permitidos
 */
function requireRole(array $allowedRoles, bool $isApi = false): void
{
    requireAuth($isApi);
    $userRole = $_SESSION['user_role'] ?? '';

    if (!in_array($userRole, $allowedRoles)) {
        if ($isApi) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Acesso negado']);
            exit;
        } else {
            header('Location: index.php?page=dashboard&error=access_denied');
            exit;
        }
    }
}

/**
 * Retorna dados do usuário atual
 */
function getCurrentUser(): ?array
{
    if (!isAuthenticated())
        return null;
    return [
        'id' => $_SESSION['user_id'],
        'nome' => $_SESSION['user_nome'],
        'email' => $_SESSION['user_email'],
        'perfil' => $_SESSION['user_role'],
        'avatar' => $_SESSION['user_avatar'] ?? null
    ];
}

/**
 * Faz login do usuário
 */
function loginUser(array $user): void
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nome'] = $user['nome'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['perfil'];
    $_SESSION['user_avatar'] = $user['avatar'] ?? null;
    $_SESSION['last_activity'] = time();
    $_SESSION['must_change'] = $user['must_change_password'] ?? 0;
}

/**
 * Faz logout do usuário
 */
function logoutUser(): void
{
    session_destroy();
}
