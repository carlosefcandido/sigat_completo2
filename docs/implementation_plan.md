# SIGAT – Rewrite to PHP/MySQL with Bootstrap + Tailwind

Rewrite the existing SIGAT ONG management system (React/TypeScript) as a **PHP + MySQL** application with **Bootstrap 5 + Tailwind CSS** for the frontend and **vanilla JavaScript** only. No Vite, React, Next.js, or any JS framework.

The project will live inside `c:\wamp64\www\sigat completo\` as a new PHP structure, running on WAMP's Apache + PHP + MySQL stack.

---

## Proposed Changes

### Folder Structure

```
sigat completo/
├── config/
│   └── database.php          # PDO MySQL connection
├── includes/
│   ├── auth.php               # Session/token helpers
│   └── functions.php          # Shared utility functions
├── api/
│   ├── login.php              # POST login
│   ├── logout.php             # POST logout
│   ├── me.php                 # GET current user
│   ├── users.php              # CRUD users
│   ├── beneficiaries.php      # CRUD beneficiaries
│   ├── classes.php            # CRUD classes
│   ├── projects.php           # CRUD projects
│   ├── transactions.php       # CRUD transactions
│   ├── documents.php          # CRUD documents
│   ├── lesson_plans.php       # CRUD lesson plans
│   ├── lesson_reports.php     # CRUD lesson reports
│   ├── attendances.php        # CRUD attendances
│   ├── activities.php         # CRUD activities
│   ├── events.php             # CRUD events
│   ├── portfolio.php          # CRUD portfolio items
│   ├── organization.php       # GET/PUT organization
│   ├── diagnosis.php          # CRUD diagnosis
│   ├── fundraising.php        # CRUD fundraising
│   └── forgot_password.php    # POST generate token / POST reset password
├── assets/
│   └── css/
│       └── custom.css         # Custom styles on top of Bootstrap+Tailwind
├── pages/
│   ├── dashboard.php
│   ├── beneficiaries.php
│   ├── classes.php
│   ├── lessons.php
│   ├── projects.php
│   ├── portfolio.php
│   ├── activities.php
│   ├── events.php
│   ├── documents.php
│   ├── organization.php
│   ├── diagnosis.php
│   ├── fundraising.php
│   ├── finance.php
│   ├── users.php
│   ├── audit.php
│   ├── trash.php
│   ├── forgot_password.php    # Public page to request reset
│   └── reset_password.php     # Public page to set new password
├── templates/
│   ├── header.php             # HTML head, navbar, sidebar
│   ├── footer.php             # Scripts, closing tags
│   └── sidebar.php            # Navigation sidebar
├── database.sql               # Full MySQL schema
├── index.php                  # Entry point (router/login redirect)
└── login.php                  # Login page
```

---

### Database Schema (MySQL)

#### [NEW] [database.sql](file:///c:/wamp64/www/sigat%20completo/database.sql)

Creates `sigat_db` with tables:

| Table | Key Columns |
|---|---|
| `users` | id, nome, email, senha_hash, perfil, ativo, avatar, must_change_password, login_attempts, reset_token, reset_expires, created_at, last_login |
| `beneficiaries` | id (AT+ano+seq), name, birth_date, cpf_rg, responsible_name/cpf, address, phone, school, grade, religion, race_color, photo_url, is_pcd, pcd_type/desc, needs_follow_up, medical_notes, has_cad_unico, nis_number, is_deleted, created_at, updated_at |
| `projects` | id, name, general_objective, justification, specific_objectives, methodology, communication_plan, sustainability_plan, schedule_json, budget_json, extended_fields_json, status, created_at |
| `classes` | id, name, project_id (FK), teacher_id (FK), schedule, days_of_week_json, created_at |
| `class_beneficiaries` | class_id, beneficiary_id (M2M) |
| `attendances` | id, class_id, date, records_json |
| `lesson_plans` | id, class_id, month, objective, content, methodology, materials, observations, professor_id, created_at, updated_at, updated_by |
| `lesson_reports` | id, class_id, month, entries_json, professor_id, created_at, updated_at, updated_by |
| `transactions` | id, description, type (RECEITA/DESPESA), category, value, payment_method, date, due_date, project_id, status, attachment_url, observations, created_by, created_by_id, created_at |
| `documents` | id, title, category, issue_date, expiry_date, observations, file_url, file_type, status, is_deleted, uploaded_by, uploaded_by_id, updated_by, created_at, updated_at |
| `portfolio_items` | id, project_id, year, location, beneficiaries_count, description, photos_json, videos_json, results_json, testimonials_json, partners_json, created_at, updated_at |
| `activity_board` | id, name, teacher, day_of_week, start_time, end_time, location, description, created_at, updated_at |
| `events` | id, title, date, time, location, description, organizer, status, created_at, updated_at |
| `organization` | id=1 (singleton), all org fields |
| `diagnoses` | id, project_id, strengths, weaknesses, opportunities, threats, updated_at |
| `fundraising` | id, title, funder, deadline, total_value, requested_value, status, link, description, observations, created_at |
| `audit_logs` | id, entity_type, entity_id, user_id, user_name, action, details, timestamp |

Default admin: `admin@sigat.com` / `SIGAT-Admin-2024`.

---

### Backend (PHP)

#### [NEW] [database.php](file:///c:/wamp64/www/sigat%20completo/config/database.php)
PDO connection to `sigat_db` with UTF-8 and error handling.

#### [NEW] [auth.php](file:///c:/wamp64/www/sigat%20completo/includes/auth.php)
PHP session-based auth: `startSession()`, `requireAuth()`, `requireRole()`, `getCurrentUser()`.

#### [NEW] [functions.php](file:///c:/wamp64/www/sigat%20completo/includes/functions.php)
Helpers: `generateId()`, `calculateDocStatus()`, `generateBeneficiaryId()`, `jsonResponse()`, `sanitize()`.

#### [NEW] API files in `api/`
Each file handles GET/POST/PUT/DELETE via `$_SERVER['REQUEST_METHOD']` switch. All return JSON. Auth checked on every request.

---

### Frontend (Bootstrap 5 + Tailwind + Vanilla JS)

#### [NEW] [index.php](file:///c:/wamp64/www/sigat%20completo/index.php)
SPA-like router: checks session → if not logged in, redirect to `login.php`. Otherwise loads layout with sidebar and dynamically includes the active page via `?page=` parameter.

#### [NEW] [login.php](file:///c:/wamp64/www/sigat%20completo/login.php)
Full-screen login with gradient background, animated card, SIGAT branding. AJAX form submission to `/api/login.php`.

#### [NEW] Templates (`templates/`)
- `header.php` – CDN includes for Bootstrap 5.3 + Tailwind CDN + Font Awesome, meta tags
- `sidebar.php` – Dark gradient sidebar with role-based menu filtering, active state, mobile responsive
- `footer.php` – JS includes, initialization

#### [NEW] Pages (`pages/`)
Each page uses Bootstrap cards/tables/modals + Tailwind utility classes for premium styling. CRUD operations via `fetch()` to `api/*.php` endpoints. All vanilla JS.

**Key modules replicated:**
1. **Dashboard** – Summary cards (beneficiaries, classes, projects, finances), charts
2. **Beneficiários** – DataTable with search/filter, add/edit modal, PCD fields, CadÚnico
3. **Turmas** – Class list, teacher assignment, enroll beneficiaries, attendance
4. **Plano de Aula** – Lesson plans + reports per class/month
5. **Projetos** – Full project form with structured edital fields
6. **Portfólio** – Gallery view with photos, results, testimonials
7. **Quadro de Atividades** – Weekly schedule board
8. **Eventos** – Event list with status badges (Agendado/Realizado/Cancelado)
9. **Documentos** – Categorized document manager with status indicators
10. **Organização** – Organization profile editor
11. **Diagnóstico** – SWOT analysis per project
12. **Captação** – Fundraising pipeline with status tracking
13. **Financeiro** – Income/expense tracker, charts, filters
14. **Usuários** – User CRUD (admin only)
15. **Auditoria** – Audit log viewer with filters
16. **Lixeira** – Soft-deleted items restoration

---

### Novas Funcionalidades e Melhorias

#### [MODIFY] [classes.php](file:///c:/wamp64/www/sigat%20completo/Projeto_PHP/pages/classes.php)
- **Botões de Gestão**: Adicionar botões "Editar" e "Excluir" nos cards das turmas (visíveis apenas para Admin/Coordenação).
- **Modal de Turma**: Ajustar o modal para suportar edição, populando campos e checkboxes de dias da semana.
- **Lógica de Salvamento**: Atualizar o script para usar o método `PUT` quando estiver editando uma turma existente.
- **Exclusão**: Adicionar confirmação e chamada para a API de deleção.

#### [MODIFY] [finance.php](file:///c:/wamp64/www/sigat%20completo/Projeto_PHP/pages/finance.php)
- **Modal de Transação**: Adicionar campo "Data de Vencimento" e opções de "Recorrência" (Mensal, Semanal).
- **Listagem**: Exibir data de vencimento e ícone indicador para despesas recorrentes.
- **Lógica**: Implementar flag de recorrência para facilitar o controle de gastos fixos.

#### [MODIFY] [transactions.php](file:///c:/wamp64/www/sigat%20completo/Projeto_PHP/api/transactions.php)
- Atualizar o `POST` para processar campos de vencimento e recorrência.

#### [MODIFY] [organization.php](file:///c:/wamp64/www/sigat%20completo/Projeto_PHP/pages/organization.php)
- **UI**: Adicionar campo de upload de logo com preview.
- **API**: Atualizar `api/organization.php` para processar e salvar o arquivo da logo.

#### [MODIFY] [reports.php](file:///c:/wamp64/www/sigat%20completo/Projeto_PHP/pages/reports.php)
- **UI**: Redesenhar interface para suportar seleção de múltiplos tipos de relatório e filtros (Período, Turma, Projeto).
- **Lógica**: Implementar geração dinâmica para:
    - **Frequência**: Gráfico/Tabela de presença de alunos por período.
    - **Financeiro**: Extrato detalhado entre duas datas.
    - **Portfólio**: Exibição de fotos e resultados extraídos do JSON.
    - **Projetos/Planos**: Documentação completa para apresentação.
    - **Captação**: Pipeline de captação em período específico.

#### [MODIFY] [sidebar.php](file:///c:/wamp64/www/sigat%20completo/Projeto_PHP/templates/sidebar.php)
- Adicionar o novo item "Relatórios" ao menu para perfis autorizados.

#### [NEW] [custom.css](file:///c:/wamp64/www/sigat%20completo/assets/css/custom.css)
Custom overrides: gradient sidebar, card shadows, hover animations, responsive adjustments. Uses CSS variables for the SIGAT color palette.

---

## User Review Required

> [!IMPORTANT]
> The existing React/TypeScript files will remain in the folder alongside the new PHP files. The new system will operate independently. If desired, old files can be cleaned up after verification.

> [!IMPORTANT]
> Bootstrap 5 and Tailwind CSS will both be loaded via CDN. Tailwind's Preflight (CSS reset) will be disabled via the <SAME> play mode to avoid conflicts with Bootstrap's base styles.

---

## Verification Plan

### Browser Testing
1. Start WAMP and open `http://localhost/sigat completo/` in the browser
2. Verify redirect to login page
3. Login with `admin@sigat.com` / `SIGAT-Admin-2024`
4. Navigate through each sidebar module and verify page loads
5. Test CRUD: create a beneficiary, edit, soft-delete
6. Test role permissions: create a non-admin user, login, verify restricted modules are hidden

### Manual Verification
- After implementation, I will use the browser tool to navigate through the application and verify each page loads and functions correctly
- The user can also manually test by opening `http://localhost/sigat completo/` in their browser with WAMP running
