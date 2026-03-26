<?php
/**
 * SIGAT - Sidebar Template
 */
$menuItems = [
    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fas fa-chart-pie', 'roles' => ['ADMIN', 'COORDENAÇÃO', 'PROFESSOR', 'FINANCEIRO']],
    ['id' => 'beneficiaries', 'label' => 'Beneficiários', 'icon' => 'fas fa-users', 'roles' => ['ADMIN', 'COORDENAÇÃO']],
    ['id' => 'classes', 'label' => 'Turmas', 'icon' => 'fas fa-chalkboard', 'roles' => ['ADMIN', 'COORDENAÇÃO', 'PROFESSOR']],
    ['id' => 'lessons', 'label' => 'Plano de Aula', 'icon' => 'fas fa-book-open', 'roles' => ['ADMIN', 'COORDENAÇÃO', 'PROFESSOR']],
    ['id' => 'projects', 'label' => 'Projetos', 'icon' => 'fas fa-bullseye', 'roles' => ['ADMIN', 'COORDENAÇÃO']],
    ['id' => 'portfolio', 'label' => 'Portfólio', 'icon' => 'fas fa-images', 'roles' => ['ADMIN', 'COORDENAÇÃO']],
    ['id' => 'activities', 'label' => 'Agenda da Diretoria', 'icon' => 'fas fa-calendar-days', 'roles' => ['ADMIN', 'COORDENAÇÃO', 'PROFESSOR']],
    ['id' => 'events', 'label' => 'Eventos', 'icon' => 'fas fa-calendar', 'roles' => ['ADMIN', 'COORDENAÇÃO']],
    ['id' => 'reports', 'label' => 'Relatórios', 'icon' => 'fas fa-file-pdf', 'roles' => ['ADMIN', 'COORDENAÇÃO', 'FINANCEIRO']],
    ['id' => 'documents', 'label' => 'Documentos', 'icon' => 'fas fa-file-lines', 'roles' => ['ADMIN', 'COORDENAÇÃO', 'FINANCEIRO']],
    ['id' => 'organization', 'label' => 'Organização', 'icon' => 'fas fa-building', 'roles' => ['ADMIN', 'COORDENAÇÃO']],
    ['id' => 'diagnosis', 'label' => 'Diagnóstico', 'icon' => 'fas fa-chart-line', 'roles' => ['ADMIN', 'COORDENAÇÃO']],
    ['id' => 'fundraising', 'label' => 'Captação', 'icon' => 'fas fa-coins', 'roles' => ['ADMIN', 'COORDENAÇÃO']],
    ['id' => 'finance', 'label' => 'Financeiro', 'icon' => 'fas fa-wallet', 'roles' => ['ADMIN', 'FINANCEIRO']],
    ['id' => 'users', 'label' => 'Usuários', 'icon' => 'fas fa-user-plus', 'roles' => ['ADMIN']],
    ['id' => 'audit', 'label' => 'Auditoria', 'icon' => 'fas fa-history', 'roles' => ['ADMIN']],
    ['id' => 'trash', 'label' => 'Lixeira', 'icon' => 'fas fa-trash-alt', 'roles' => ['ADMIN']],
];
?>

<!-- Mobile menu toggle overlay -->
<div id="sidebarOverlay" class="sidebar-overlay d-none" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="sigat-sidebar d-flex flex-column">
    <div class="sidebar-header p-4">
        <div class="d-flex align-items-center gap-3">
            <div class="sidebar-logo-icon">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div>
                <h5 class="text-white fw-bold mb-0" style="font-size:18px;">SIGAT</h5>
                <small style="color:rgba(148,163,184,0.7);font-size:11px;">Gestão de ONG</small>
            </div>
        </div>
        <button class="btn btn-sm text-white d-md-none sidebar-close" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="sidebar-nav flex-grow-1 overflow-auto px-3 py-2">
        <?php foreach ($menuItems as $item): ?>
            <?php if (in_array($userRole, $item['roles'])): ?>
                <a href="?page=<?= $item['id'] ?>" class="sidebar-link <?= $activePage === $item['id'] ? 'active' : '' ?>"
                    id="nav-<?= $item['id'] ?>">
                    <i class="<?= $item['icon'] ?> sidebar-link-icon"></i>
                    <span>
                        <?= $item['label'] ?>
                    </span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer p-3 border-top" style="border-color:rgba(255,255,255,0.08)!important;">
        <div class="d-flex align-items-center gap-2 px-2">
            <div class="sidebar-user-avatar">
                <?php if ($userAvatar): ?>
                    <?php $avatarUrl = (strpos($userAvatar, 'http') === 0) ? $userAvatar : 'api/file.php?path=' . $userAvatar; ?>
                    <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="rounded-circle" width="36"
                        height="36">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <?= strtoupper(substr($userName, 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="text-white fw-medium text-truncate" style="font-size:13px;">
                    <?= htmlspecialchars($userName) ?>
                </div>
                <div style="color:rgba(148,163,184,0.6);font-size:11px;">
                    <?= htmlspecialchars($userRole) ?>
                </div>
            </div>
            <a href="api/logout.php" class="btn btn-sm" style="color:rgba(248,113,113,0.8);" title="Sair"
                onclick="return confirm('Deseja realmente sair?')">
                <i class="fas fa-right-from-bracket"></i>
            </a>
        </div>
    </div>
</aside>

<!-- Main content wrapper -->
<div class="flex-grow-1 d-flex flex-column overflow-hidden">
    <!-- Top bar -->
    <header class="sigat-topbar d-flex align-items-center justify-content-between px-4 py-3">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm d-md-none text-white" onclick="toggleSidebar()">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <h4 class="mb-0 fw-bold text-white" style="font-size:20px;" id="pageTitle">
                <?php
                $titles = [
                    'dashboard' => 'Dashboard',
                    'beneficiaries' => 'Beneficiários',
                    'classes' => 'Turmas',
                    'lessons' => 'Plano de Aula',
                    'projects' => 'Projetos',
                    'portfolio' => 'Portfólio',
                    'activities' => 'Agenda da Diretoria',
                    'events' => 'Eventos',
                    'reports' => 'Relatórios',
                    'documents' => 'Documentos',
                    'organization' => 'Organização',
                    'diagnosis' => 'Diagnóstico',
                    'fundraising' => 'Captação de Recursos',
                    'finance' => 'Financeiro',
                    'users' => 'Gestão de Usuários',
                    'audit' => 'Auditoria',
                    'trash' => 'Lixeira'
                ];
                echo $titles[$activePage] ?? 'SIGAT';
                ?>
            </h4>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge tw-bg-teal-700 tw-text-teal-100 tw-px-3 tw-py-1.5 tw-rounded-full"
                style="font-size:12px;">
                <i class="fas fa-circle tw-text-teal-400 me-1" style="font-size:6px;vertical-align:middle;"></i>
                Online
            </span>
        </div>
    </header>

    <!-- Page content -->
    <main class="flex-grow-1 overflow-auto p-4" style="background: #0f172a;">
        <div class="container-fluid" style="max-width: 1400px;">