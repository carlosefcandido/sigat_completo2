# SIGAT PHP Rewrite

## Backend
- [x] Database schema (`database.sql`) — 18 tables
- [x] Config (`config/database.php`) — PDO connection
- [x] Auth system (`includes/auth.php`) — sessions, roles
- [x] Utility functions (`includes/functions.php`)
- [x] Install script (`install.php`) — auto-setup

## API Endpoints (16)
- [x] `api/login.php`, `api/logout.php`, `api/me.php`
- [x] `api/users.php`, `api/beneficiaries.php`, `api/classes.php`
- [x] `api/projects.php`, `api/transactions.php`, `api/documents.php`
- [x] `api/lesson_plans.php`, `api/lesson_reports.php`, `api/attendances.php`
- [x] `api/activities.php`, `api/events.php`, `api/portfolio.php`
- [x] `api/organization.php`, `api/diagnosis.php`, `api/fundraising.php`, `api/audit.php`

## Frontend
- [x] Login page (`login.php`) — animated gradient, glassmorphism
- [x] Router (`index.php`) — role-based access
- [x] Templates: `header.php`, `sidebar.php`, `footer.php`
- [x] CSS design system (`assets/css/custom.css`) — dark theme
- [x] Dashboard page — stats, documents, finance summary
- [x] Beneficiaries page — full CRUD, search, PCD/CadÚnico
- [x] Classes page — card layout, project/teacher links
- [x] Lessons page — plans & reports tabs
- [x] Projects page — card view, CRUD
- [x] Portfolio page — card gallery
- [x] Activities page — weekly grid board
- [x] Events page — status filters, CRUD
- [x] Documents page — category filter, status badges
- [x] Organization page — profile form
- [x] Diagnosis page — SWOT quadrants
- [x] Fundraising page — pipeline table
- [x] Finance page — income/expense stats, transactions
- [x] Users page — create, activate/deactivate
- [x] Audit page — filterable log
- [x] Trash page — restore functionality

- [x] Classes module: Multiple days of week support
- [x] Classes module: Edit and delete functionality
- [x] Classes module: Student enrollment (beneficiary selection in modal)
- [x] Classes module: Weekly schedule view for Professors
- [x] Attendance module: Student attendance recording per class session
- [x] Finance module: Add due date to transactions
- [x] Finance module: Add recurring expense support
- [x] Auth: Improved logout flow with redirect to login page
- [x] Dashboard: Add monthly financial transaction counter
- [x] Dashboard: Add list of transactions due in the next 7 days
- [x] Organization: Add logo upload functionality
- [x] Reports: Implement comprehensive reporting system
    - [x] Attendance Frequency report (per class)
    - [x] Financial Movement within period (start/end)
    - [x] Projects and Lesson Plans reports
    - [x] Portfolio report (including images and files)
    - [x] Fundraising report (within period)
    - [x] Detailed Beneficiaries report
- [x] API: Implement attendance upsert logic in `api/attendances.php`
- [x] Reports Module Refinement:
    - [x] Fix `records.forEach` error with defensive array checks
    - [x] Support attendance data mapping for new students (Object vs Array format)
    - [x] Fix missing progress bar colors in print mode (`print-color-adjust`)
    - [x] Group attendance by Turma (Class) with dynamic headers
    - [x] Separate Finance report into Receipts and Payments tables
    - [x] Update Finance report date labels (Data de Recebimento / Pagamento)

- [x] Forgot Password Feature:
    - [x] Update `users` table with `reset_token` and `reset_expires`
    - [x] Add "Esqueceu a senha?" link to `login.php`
    - [x] Implement `api/forgot_password.php` (request & verify)
    - [x] Create `forgot_password.php` request page
    - [x] Create `reset_password.php` new password page
- [x] Fix Production Login Errors:
    - [x] Verify `config/database.php` instructions in deployment guide
    - [x] Ensure `install.php` generates valid password hashes

## Verification
- [x] Verify project structure is complete
- [x] Verify attendance records are correctly saved and loaded
- [x] Deployment Readiness:
    - [x] Enhance `install.php` with safety lock (`install.lock`)
    - [x] Create `deployment_guide.md` with hosting instructions
    - [x] Verify relative paths in `apiCall` for production
    - [x] Sync documentation to `Projeto_PHP/docs/`
