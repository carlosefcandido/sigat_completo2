<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - SIGAT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { prefix: 'tw-', corePlugins: { preflight: false } }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 440px;
            padding: 40px;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control-dark {
            background: rgba(255, 255, 255, 0.07) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #e2e8f0 !important;
            border-radius: 12px;
            padding: 14px 16px;
        }

        .btn-sigat {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
        }

        .btn-sigat:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div
                class="tw-bg-teal-500 tw-w-16 tw-h-16 tw-rounded-2xl tw-mx-auto tw-flex tw-items-center tw-justify-center tw-mb-4 tw-shadow-lg tw-shadow-teal-500/30">
                <i class="fas fa-key tw-text-2xl tw-text-white"></i>
            </div>
            <h2 class="tw-text-white tw-font-bold tw-text-2xl">Recuperar Senha</h2>
            <p class="tw-text-slate-400 tw-text-sm tw-mt-2">Insira seu e-mail para receber as instruções.</p>
        </div>

        <div id="alertBox" class="tw-hidden tw-mb-4 tw-p-3 tw-rounded-xl tw-text-sm tw-text-center"></div>

        <form id="requestForm">
            <div class="mb-4">
                <label class="tw-text-slate-300 tw-text-sm tw-mb-2 tw-block">E-mail Cadastrado</label>
                <input type="email" id="email" class="form-control form-control-dark" placeholder="exemplo@email.com"
                    required>
            </div>
            <button type="submit" class="btn btn-sigat w-100 mb-3" id="btnSubmit">
                <span id="btnText">Enviar Instruções</span>
                <span id="btnSpinner" class="tw-hidden"><i class="fas fa-spinner fa-spin tw-mr-2"></i>Enviando...</span>
            </button>
            <div class="text-center">
                <a href="login.php" class="tw-text-teal-400 tw-text-sm tw-no-underline hover:tw-underline">
                    <i class="fas fa-arrow-left tw-mr-1"></i>Voltar ao Login
                </a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('requestForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSubmit');
            const alertBox = document.getElementById('alertBox');
            const email = document.getElementById('email').value;

            document.getElementById('btnText').classList.add('tw-hidden');
            document.getElementById('btnSpinner').classList.remove('tw-hidden');
            btn.disabled = true;

            try {
                const res = await fetch('api/forgot_password.php?action=request', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();

                alertBox.className = data.success ? 'tw-block tw-bg-teal-500/20 tw-text-teal-300 tw-border tw-border-teal-500/30 tw-p-3 tw-rounded-xl tw-mb-4 tw-text-sm tw-text-center' : 'tw-block tw-bg-red-500/20 tw-text-red-300 tw-border tw-border-red-500/30 tw-p-3 tw-rounded-xl tw-mb-4 tw-text-sm tw-text-center';
                alertBox.innerHTML = data.message || data.error;

                if (data.success && data.debug_token) {
                    console.log("DEBUG: Token de recuperação gerado:", data.debug_token);
                    alertBox.innerHTML += `<br><br><strong class="tw-text-white">DEBUG MODE:</strong><br>Clique no link abaixo para testar o reset (já que não temos envio de email real configurado):<br><a href="reset_password.php?token=${data.debug_token}" class="tw-text-white tw-underline">Redefinir Senha Agora</a>`;
                }
            } catch (err) {
                alertBox.className = 'tw-block tw-bg-red-500/20 tw-text-red-300 tw-border tw-border-red-500/30 tw-p-3 tw-rounded-xl tw-mb-4 tw-text-sm tw-text-center';
                alertBox.textContent = 'Erro de conexão';
            } finally {
                document.getElementById('btnText').classList.remove('tw-hidden');
                document.getElementById('btnSpinner').classList.add('tw-hidden');
                btn.disabled = false;
            }
        });
    </script>
</body>

</html>