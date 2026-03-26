<?php
/**
 * SIGAT - Main Router (index.php)
 */
require_once __DIR__ . '/includes/auth.php';
requireAuth();

$page = $_GET['page'] ?? 'dashboard';
$validPages = ['dashboard', 'beneficiaries', 'classes', 'lessons', 'projects', 'portfolio', 'activities', 'events', 'documents', 'organization', 'diagnosis', 'fundraising', 'finance', 'users', 'audit', 'trash', 'reports'];

if (!in_array($page, $validPages)) {
    $page = 'dashboard';
}

// Role-based access
$currentUser = getCurrentUser();
$role = $currentUser['perfil'];

$roleAccess = [
    'dashboard' => ['ADMIN', 'COORDENAÇÃO', 'PROFESSOR', 'FINANCEIRO'],
    'beneficiaries' => ['ADMIN', 'COORDENAÇÃO'],
    'classes' => ['ADMIN', 'COORDENAÇÃO', 'PROFESSOR'],
    'lessons' => ['ADMIN', 'COORDENAÇÃO', 'PROFESSOR'],
    'projects' => ['ADMIN', 'COORDENAÇÃO'],
    'portfolio' => ['ADMIN', 'COORDENAÇÃO'],
    'activities' => ['ADMIN', 'COORDENAÇÃO', 'PROFESSOR'],
    'events' => ['ADMIN', 'COORDENAÇÃO'],
    'documents' => ['ADMIN', 'COORDENAÇÃO', 'FINANCEIRO'],
    'organization' => ['ADMIN', 'COORDENAÇÃO'],
    'diagnosis' => ['ADMIN', 'COORDENAÇÃO'],
    'fundraising' => ['ADMIN', 'COORDENAÇÃO'],
    'finance' => ['ADMIN', 'FINANCEIRO'],
    'users' => ['ADMIN'],
    'audit' => ['ADMIN'],
    'trash' => ['ADMIN'],
    'reports' => ['ADMIN', 'COORDENAÇÃO', 'FINANCEIRO'],
];

if (!in_array($role, $roleAccess[$page] ?? [])) {
    $page = 'dashboard';
}

include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/sidebar.php';
include __DIR__ . '/pages/' . $page . '.php';
include __DIR__ . '/templates/footer.php';
