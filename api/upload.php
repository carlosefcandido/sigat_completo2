<?php
/**
 * SIGAT API - General File Upload
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth(true);

$method = getRequestMethod();
if ($method !== 'POST') {
    jsonResponse(['error' => 'Método não permitido'], 405);
}

if (!isset($_FILES['file'])) {
    jsonResponse(['error' => 'Nenhum arquivo enviado'], 400);
}

$file = $_FILES['file'];
$targetFolder = $_POST['folder'] ?? 'images';
$uploadDir = __DIR__ . '/../uploads/' . $targetFolder . '/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'pdf', 'doc', 'docx'];

if (!in_array($ext, $allowed)) {
    jsonResponse(['error' => 'Formato de arquivo não permitido'], 400);
}

$prefix = ($targetFolder === 'documents') ? 'doc_' : 'img_';
$filename = uniqid($prefix) . '.' . $ext;
$targetPath = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    $relativePath = 'uploads/' . $targetFolder . '/' . $filename;
    jsonResponse(['success' => true, 'url' => $relativePath]);
} else {
    jsonResponse(['error' => 'Falha ao salvar arquivo'], 500);
}
