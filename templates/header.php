<?php
/**
 * SIGAT - Header Template
 */
$currentUser = getCurrentUser();
$userRole = $currentUser['perfil'] ?? '';
$userName = $currentUser['nome'] ?? '';
$userAvatar = $currentUser['avatar'] ?? null;
$activePage = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGAT - Sistema de Gerenciamento</title>
    <meta name="description" content="SIGAT - Sistema de Gerenciamento de ONG para Projeto Arte Transformadora">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind via CDN with prefix to avoid conflicts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { prefix: 'tw-', corePlugins: { preflight: false } }</script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">

    <!-- Core JS (in header to be available for page modules) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // API helper
        async function apiCall(url, method = 'GET', body = null) {
            let realMethod = method;
            const headers = { 'Content-Type': 'application/json' };

            // Support fallback for shared hosting that blocks PUT/DELETE
            if (method === 'PUT' || method === 'DELETE' || method === 'PATCH') {
                realMethod = 'POST';
                headers['X-HTTP-Method-Override'] = method;
            }

            const opts = {
                method: realMethod,
                headers: headers
            };

            if (body) opts.body = JSON.stringify(body);

            try {
                const res = await fetch(url, opts);

                if (res.status === 401) { window.location.href = 'login.php'; return null; }

                const contentType = res.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    const data = await res.json();
                    if (!res.ok) {
                        alert(data.error || data.message || 'Erro na operação');
                        return null;
                    }
                    return data;
                } else {
                    // Not JSON - probably an HTML error page from the server
                    const text = await res.text();
                    console.error('Non-JSON response:', text);
                    alert('Erro no servidor (Resposta inválida). Verifique as permissões de pasta ou se o método ' + method + ' está bloqueado.');
                    return null;
                }
            } catch (e) {
                console.error(e);
                alert('Erro de conexão com o servidor. Verifique sua internet ou se o arquivo api/ existe.');
                return null;
            }
        }

        // Toast notification
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `sigat-toast sigat-toast-${type}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => { toast.classList.add('show'); }, 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Formatters
        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            return isNaN(d) ? dateStr : d.toLocaleDateString('pt-BR');
        }

        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
        }

        function confirmDelete(msg = 'Tem certeza que deseja excluir?') {
            return confirm(msg);
        }
    </script>
</head>

<body class="sigat-body">
    <div class="d-flex vh-100 overflow-hidden">