<?php
/**
 * SIGAT API - Servidor Seguro de Arquivos
 * Serve os arquivos da pasta uploads apenas para usuários autenticados.
 */
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Acesso negado: É necessário estar logado.');
}

$path = $_GET['path'] ?? '';

// Tratamento básico de path traversal
if (empty($path) || strpos($path, '..') !== false || strpos($path, 'uploads/') !== 0) {
    http_response_code(400);
    exit('Caminho de arquivo inválido.');
}

// Diretório base seguro
$baseDir = realpath(__DIR__ . '/../');
$fullPath = realpath($baseDir . '/' . $path);

// Garante que o arquivo existe e que está estritamente dentro da pasta uploads do projeto
if (!$fullPath || !file_exists($fullPath) || strpos($fullPath, realpath($baseDir . '/uploads/')) !== 0) {
    http_response_code(404);
    exit('Arquivo não encontrado ou removido.');
}

$mime = mime_content_type($fullPath);
if (!$mime) {
    $mime = 'application/octet-stream';
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($fullPath));
header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');
header('Cache-Control: private, max-age=86400'); // Cache seguro ativado

// Serve o conteúdo do arquivo
readfile($fullPath);
exit;
