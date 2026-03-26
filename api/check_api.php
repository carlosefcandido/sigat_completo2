<?php
/**
 * SIGAT - Conexão Check
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

echo "<h1>🔍 SIGAT - Diagnóstico de Conexão</h1>";

try {
    $pdo = getConnection();
    echo "<p style='color:green'>✅ Conexão com o Banco de Dados: OK</p>";

    if (function_exists('getRequestMethod')) {
        echo "<p style='color:green'>✅ Função getRequestMethod: OK</p>";
    } else {
        echo "<p style='color:red'>❌ Função getRequestMethod: NÃO ENCONTRADA (Verifique se enviou o arquivo includes/functions.php atualizado)</p>";
    }

    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    echo "<p style='color:green'>✅ Tabela 'users': OK (" . $stmt->fetchColumn() . " usuários)</p>";

    if (function_exists('random_bytes')) {
        echo "<p style='color:green'>✅ Função random_bytes: OK</p>";
    } else {
        echo "<p style='color:orange'>⚠️ Função random_bytes: NÃO ENCONTRADA (Usando fallback compatível)</p>";
    }

} catch (Exception $e) {
    echo "<p style='color:red'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<hr><p>Verifique se todos os arquivos da pasta <strong>api/</strong>, <strong>includes/</strong> e <strong>templates/</strong> foram enviados corretamente.</p>";
