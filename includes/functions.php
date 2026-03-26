<?php
/**
 * SIGAT - Funções utilitárias
 */

/**
 * Gera um ID único
 */
function generateId(): string
{
    return bin2hex(random_bytes(8));
}

/**
 * Gera ID de beneficiário no formato AT + ANO + sequencial
 */
function generateBeneficiaryId(int $seq): string
{
    $year = date('Y');
    return 'AT' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
}

/**
 * Calcula status de documento baseado na data de vencimento
 */
function calculateDocStatus(?string $expiryDate): string
{
    if (!$expiryDate)
        return 'Ativo e Regular';

    $expiry = new DateTime($expiryDate);
    $now = new DateTime();
    $diff = $now->diff($expiry);

    if ($expiry < $now) {
        return 'Vencido';
    } elseif ($diff->days <= 30) {
        return 'Próximo do Vencimento';
    }
    return 'Ativo e Regular';
}

/**
 * Retorna resposta JSON
 */
function jsonResponse($data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Lê o body JSON da requisição
 */
function getJsonBody(): array
{
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    return $data ?? [];
}

/**
 * Sanitiza string
 */
function sanitize(?string $str): ?string
{
    if ($str === null)
        return null;
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

/**
 * Criptografa NIS (simulado)
 */
function encryptNis(string $nis): string
{
    return 'ENC_' . base64_encode($nis);
}

/**
 * Descriptografa NIS (simulado)
 */
function decryptNis(string $encrypted): string
{
    if (strpos($encrypted, 'ENC_') === 0) {
        return base64_decode(substr($encrypted, 4));
    }
    return $encrypted;
}

/**
 * Registra log de auditoria
 */
function addAuditLog(PDO $pdo, ?int $userId, ?string $userName, string $action, ?string $entityType = null, ?string $entityId = null, ?string $details = null): void
{
    $stmt = $pdo->prepare("INSERT INTO audit_logs (entity_type, entity_id, user_id, user_name, action, details) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$entityType, $entityId, $userId, $userName, $action, $details]);
}

/**
 * Retorna próximo sequencial de beneficiário
 */
function getNextBeneficiarySeq(PDO $pdo): int
{
    $year = date('Y');
    $stmt = $pdo->query("SELECT id FROM beneficiaries WHERE id LIKE 'AT{$year}-%' ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetchColumn();
    if ($last) {
        $parts = explode('-', $last);
        return intval(end($parts)) + 1;
    }
    return 1;
}
/**
 * Retorna o método HTTP real (considerando X-HTTP-Method-Override)
 */
function getRequestMethod(): string
{
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST') {
        $override = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? '';
        if (in_array(strtoupper($override), ['PUT', 'DELETE', 'PATCH'])) {
            return strtoupper($override);
        }
    }
    return $method;
}
