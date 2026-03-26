<?php
/**
 * SIGAT - Login Page
 */
if (session_status() === PHP_SESSION_NONE)
    session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGAT - Login</title>
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
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 25%, #0f766e 50%, #134e4a 75%, #0f172a 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .login-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
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
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-control-dark:focus {
            background: rgba(255, 255, 255, 0.12) !important;
            border-color: #14b8a6 !important;
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.25) !important;
            color: #f8fafc !important;
        }

        .form-control-dark::placeholder {
            color: rgba(148, 163, 184, 0.7);
        }

        .btn-login {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: 16px;
            color: white;
            transition: all 0.3s;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #0d9488, #0f766e);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
            color: white;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 8px 30px rgba(20, 184, 166, 0.3);
        }

        .floating-shapes .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        .input-group-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(148, 163, 184, 0.7);
            z-index: 4;
            pointer-events: none;
        }

        .input-with-icon {
            padding-left: 44px !important;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            border-radius: 12px;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center p-3">
    <!-- Floating decorations -->
    <div class="floating-shapes">
        <div class="shape" style="width:300px;height:300px;background:#14b8a6;top:10%;left:5%;animation-delay:0s;">
        </div>
        <div class="shape" style="width:200px;height:200px;background:#0d9488;bottom:10%;right:10%;animation-delay:2s;">
        </div>
        <div class="shape" style="width:150px;height:150px;background:#14b8a6;top:50%;right:20%;animation-delay:4s;">
        </div>
    </div>

    <div class="login-card p-5" style="width: 100%; max-width: 440px; position: relative; z-index: 10;">
        <div class="logo-icon">
            <i class="fas fa-shield-halved fa-2x text-white"></i>
        </div>
        <h2 class="text-center text-white fw-bold mb-1" style="font-size: 28px;">SIGAT</h2>
        <p class="text-center mb-4" style="color: rgba(148,163,184,0.8); font-size: 14px;">Sistema de Gerenciamento de
            ONG</p>

        <div id="alertBox" class="alert alert-error d-none mb-3 py-2 px-3 text-center" style="font-size:14px;"></div>

        <form id="loginForm" autocomplete="off">
            <div class="mb-3 position-relative">
                <span class="input-group-icon"><i class="fas fa-envelope"></i></span>
                <input type="email" id="email" class="form-control form-control-dark input-with-icon"
                    placeholder="Seu email" required>
            </div>
            <div class="mb-4 position-relative">
                <span class="input-group-icon"><i class="fas fa-lock"></i></span>
                <input type="password" id="senha" class="form-control form-control-dark input-with-icon"
                    placeholder="Sua senha" required>
                <span
                    style="position:absolute;right:16px;top:50%;transform:translateY(-50%);cursor:pointer;color:rgba(148,163,184,0.7);z-index:4;"
                    onclick="togglePass()">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </span>
            </div>
            <button type="submit" class="btn btn-login w-100" id="btnLogin">
                <span id="btnText">Entrar</span>
                <span id="btnSpinner" class="d-none"><i class="fas fa-spinner fa-spin me-2"></i>Autenticando...</span>
            </button>
            <div class="text-center mt-3">
                <a href="forgot_password.php" class="text-decoration-none"
                    style="color: rgba(20, 184, 166, 0.8); font-size: 14px;">
                    <i class="fas fa-question-circle me-1"></i>Esqueceu a senha?
                </a>
            </div>
        </form>

        <div class="text-center mt-4" style="color:rgba(148,163,184,0.5);font-size:12px;">
            <i class="fas fa-lock me-1"></i> Conexão segura · SIGAT v2.0
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePassModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="background:#1e293b;border:1px solid rgba(255,255,255,0.1);border-radius:20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-white"><i class="fas fa-key me-2 text-warning"></i>Altere sua Senha</h5>
                </div>
                <div class="modal-body">
                    <p class="text-light mb-3" style="font-size:14px;opacity:0.7;">Este é seu primeiro acesso. Por
                        segurança, crie uma nova senha.</p>
                    <input type="password" id="newPass" class="form-control form-control-dark mb-3"
                        placeholder="Nova senha (mín. 6 caracteres)">
                    <input type="password" id="confirmPass" class="form-control form-control-dark"
                        placeholder="Confirme a nova senha">
                    <div id="passError" class="text-danger mt-2 d-none" style="font-size:13px;"></div>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn btn-login" onclick="changePassword()">Salvar Nova Senha</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentUserId = null;

        function togglePass() {
            const el = document.getElementById('senha');
            const icon = document.getElementById('eyeIcon');
            if (el.type === 'password') { el.type = 'text'; icon.className = 'fas fa-eye-slash'; }
            else { el.type = 'password'; icon.className = 'fas fa-eye'; }
        }

        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('btnLogin');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const alertBox = document.getElementById('alertBox');

            alertBox.classList.add('d-none');
            btnText.classList.add('d-none');
            btnSpinner.classList.remove('d-none');
            btn.disabled = true;

            try {
                const res = await fetch('api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        senha: document.getElementById('senha').value
                    })
                });
                const data = await res.json();

                if (data.success) {
                    if (data.user.must_change_password) {
                        currentUserId = data.user.id;
                        new bootstrap.Modal(document.getElementById('changePassModal')).show();
                    } else {
                        window.location.href = 'index.php';
                    }
                } else {
                    alertBox.textContent = data.error || 'Credenciais inválidas';
                    alertBox.classList.remove('d-none');
                }
            } catch (err) {
                alertBox.textContent = 'Erro de conexão com o servidor';
                alertBox.classList.remove('d-none');
            } finally {
                btnText.classList.remove('d-none');
                btnSpinner.classList.add('d-none');
                btn.disabled = false;
            }
        });

        async function changePassword() {
            const newPass = document.getElementById('newPass').value;
            const confirmPass = document.getElementById('confirmPass').value;
            const errEl = document.getElementById('passError');

            if (newPass.length < 6) { errEl.textContent = 'Senha deve ter pelo menos 6 caracteres'; errEl.classList.remove('d-none'); return; }
            if (newPass !== confirmPass) { errEl.textContent = 'As senhas não coincidem'; errEl.classList.remove('d-none'); return; }

            try {
                const res = await fetch('api/users.php?id=' + currentUserId, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ senha: newPass })
                });
                if (res.ok) {
                    window.location.href = 'index.php';
                }
            } catch (err) {
                errEl.textContent = 'Erro ao alterar senha';
                errEl.classList.remove('d-none');
            }
        }
    </script>
</body>

</html>