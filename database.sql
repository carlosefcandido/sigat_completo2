-- =============================================
-- SIGAT - Sistema de Gerenciamento de ONG
-- Banco de Dados MySQL
-- =============================================

-- =============================================
-- USUÁRIOS
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    perfil ENUM('ADMIN','COORDENAÇÃO','PROFESSOR','FINANCEIRO') NOT NULL DEFAULT 'PROFESSOR',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    avatar VARCHAR(500) DEFAULT NULL,
    must_change_password TINYINT(1) NOT NULL DEFAULT 1,
    login_attempts INT NOT NULL DEFAULT 0,
    reset_token VARCHAR(100) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin padrão (senha: SIGAT-Admin-2024)
INSERT INTO users (nome, email, senha_hash, perfil, ativo, must_change_password) VALUES
('Administrador SIGAT', 'admin@sigat.com', '$2y$10$PLACEHOLDER_HASH', 'ADMIN', 1, 1);

-- Coordenador de teste (senha: coord123)
INSERT INTO users (nome, email, senha_hash, perfil, ativo, must_change_password) VALUES
('Carlos Coordenação', 'coord@sigat.com', '$2y$10$PLACEHOLDER_HASH2', 'COORDENAÇÃO', 1, 0);

-- =============================================
-- BENEFICIÁRIOS
-- =============================================
CREATE TABLE IF NOT EXISTS beneficiaries (
    id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    birth_date DATE NOT NULL,
    cpf_rg VARCHAR(20) DEFAULT NULL,
    responsible_name VARCHAR(255) DEFAULT NULL,
    responsible_cpf VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    school VARCHAR(255) DEFAULT NULL,
    grade VARCHAR(50) DEFAULT NULL,
    religion VARCHAR(100) DEFAULT 'Sem religião',
    race_color VARCHAR(100) DEFAULT 'Prefere não declarar',
    photo_url VARCHAR(500) DEFAULT NULL,
    image_term_url VARCHAR(500) DEFAULT NULL,
    exit_term_url VARCHAR(500) DEFAULT NULL,
    is_pcd TINYINT(1) NOT NULL DEFAULT 0,
    pcd_type VARCHAR(100) DEFAULT NULL,
    pcd_description TEXT DEFAULT NULL,
    needs_follow_up TINYINT(1) NOT NULL DEFAULT 0,
    medical_notes TEXT DEFAULT NULL,
    has_cad_unico TINYINT(1) NOT NULL DEFAULT 0,
    nis_number VARCHAR(100) DEFAULT NULL,
    last_cad_unico_update DATE DEFAULT NULL,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- BENEFÍCIOS (HISTÓRICO)
-- =============================================
CREATE TABLE IF NOT EXISTS beneficiary_benefits (
    id VARCHAR(50) PRIMARY KEY,
    beneficiary_id VARCHAR(20) NOT NULL,
    benefit_name VARCHAR(255) NOT NULL,
    date_received DATE NOT NULL,
    observations TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- PROJETOS
-- =============================================
CREATE TABLE IF NOT EXISTS projects (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    general_objective TEXT DEFAULT NULL,
    justification TEXT DEFAULT NULL,
    specific_objectives TEXT DEFAULT NULL,
    methodology TEXT DEFAULT NULL,
    communication_plan TEXT DEFAULT NULL,
    sustainability_plan TEXT DEFAULT NULL,
    schedule_json JSON DEFAULT NULL,
    budget_json JSON DEFAULT NULL,
    extended_fields_json JSON DEFAULT NULL,
    status ENUM('DRAFT','FINAL') DEFAULT 'DRAFT',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TURMAS
-- =============================================
CREATE TABLE IF NOT EXISTS classes (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    project_id VARCHAR(50) DEFAULT NULL,
    teacher_id INT DEFAULT NULL,
    schedule VARCHAR(255) DEFAULT NULL,
    days_of_week_json JSON DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela M2M: Turma <-> Beneficiário
CREATE TABLE IF NOT EXISTS class_beneficiaries (
    class_id VARCHAR(50) NOT NULL,
    beneficiary_id VARCHAR(20) NOT NULL,
    PRIMARY KEY (class_id, beneficiary_id),
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- FREQUÊNCIA (ATTENDANCE)
-- =============================================
CREATE TABLE IF NOT EXISTS attendances (
    id VARCHAR(50) PRIMARY KEY,
    class_id VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    records_json JSON NOT NULL,
    professor_id INT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (professor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- PLANOS DE AULA
-- =============================================
CREATE TABLE IF NOT EXISTS lesson_plans (
    id VARCHAR(50) PRIMARY KEY,
    class_id VARCHAR(50) NOT NULL,
    month VARCHAR(7) NOT NULL,
    objective TEXT DEFAULT NULL,
    content TEXT DEFAULT NULL,
    methodology TEXT DEFAULT NULL,
    materials TEXT DEFAULT NULL,
    observations TEXT DEFAULT NULL,
    professor_id INT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (professor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- RELATÓRIOS DE AULA
-- =============================================
CREATE TABLE IF NOT EXISTS lesson_reports (
    id VARCHAR(50) PRIMARY KEY,
    class_id VARCHAR(50) NOT NULL,
    month VARCHAR(7) NOT NULL,
    entries_json JSON DEFAULT NULL,
    professor_id INT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (professor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TRANSAÇÕES FINANCEIRAS
-- =============================================
CREATE TABLE IF NOT EXISTS transactions (
    id VARCHAR(50) PRIMARY KEY,
    description VARCHAR(500) NOT NULL,
    type ENUM('RECEITA','DESPESA') NOT NULL,
    category VARCHAR(255) DEFAULT NULL,
    value DECIMAL(12,2) NOT NULL DEFAULT 0,
    payment_method VARCHAR(100) DEFAULT NULL,
    date DATE NOT NULL,
    due_date DATE DEFAULT NULL,
    is_recurring TINYINT(1) NOT NULL DEFAULT 0,
    recurrence_period ENUM('Mensal','Semanal','Anual') DEFAULT NULL,
    project_id VARCHAR(50) DEFAULT NULL,
    status ENUM('Pendente','Pago','Vencido') NOT NULL DEFAULT 'Pendente',
    attachment_url VARCHAR(500) DEFAULT NULL,
    observations TEXT DEFAULT NULL,
    created_by VARCHAR(255) DEFAULT NULL,
    created_by_id INT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- DOCUMENTOS
-- =============================================
CREATE TABLE IF NOT EXISTS documents (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    category VARCHAR(100) NOT NULL,
    issue_date DATE DEFAULT NULL,
    expiry_date DATE DEFAULT NULL,
    observations TEXT DEFAULT NULL,
    file_url VARCHAR(500) DEFAULT NULL,
    file_type VARCHAR(20) DEFAULT 'pdf',
    status VARCHAR(50) DEFAULT 'Ativo e Regular',
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    uploaded_by VARCHAR(255) DEFAULT NULL,
    uploaded_by_id INT DEFAULT NULL,
    updated_by VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- PORTFÓLIO
-- =============================================
CREATE TABLE IF NOT EXISTS portfolio_items (
    id VARCHAR(50) PRIMARY KEY,
    project_id VARCHAR(50) DEFAULT NULL,
    year VARCHAR(4) DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    beneficiaries_count VARCHAR(50) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    photos_json JSON DEFAULT NULL,
    videos_json JSON DEFAULT NULL,
    results_json JSON DEFAULT NULL,
    testimonials_json JSON DEFAULT NULL,
    partners_json JSON DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- QUADRO DE ATIVIDADES
-- =============================================
CREATE TABLE IF NOT EXISTS activity_board (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    teacher VARCHAR(255) DEFAULT NULL,
    day_of_week INT NOT NULL DEFAULT 1,
    start_time VARCHAR(10) DEFAULT NULL,
    end_time VARCHAR(10) DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- EVENTOS
-- =============================================
CREATE TABLE IF NOT EXISTS events (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    date DATE NOT NULL,
    time VARCHAR(10) DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    organizer VARCHAR(255) DEFAULT NULL,
    status ENUM('AGENDADO','REALIZADO','CANCELADO') NOT NULL DEFAULT 'AGENDADO',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- ORGANIZAÇÃO (singleton)
-- =============================================
CREATE TABLE IF NOT EXISTS organization (
    id INT NOT NULL DEFAULT 1 PRIMARY KEY,
    name VARCHAR(255) DEFAULT NULL,
    logo_url VARCHAR(500) DEFAULT NULL,
    cnpj VARCHAR(25) DEFAULT NULL,
    foundation_year VARCHAR(4) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    territory VARCHAR(500) DEFAULT NULL,
    audience TEXT DEFAULT NULL,
    beneficiaries_count VARCHAR(50) DEFAULT NULL,
    team_size VARCHAR(50) DEFAULT NULL,
    mission TEXT DEFAULT NULL,
    vision TEXT DEFAULT NULL,
    org_values TEXT DEFAULT NULL,
    history TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO organization (id, name, logo_url, cnpj, foundation_year, email, phone, address, territory, audience, beneficiaries_count, team_size, mission, vision, org_values, history) VALUES
(1, 'Projeto Arte Transformadora', NULL, '26.095.649/0001-27', '2016', 'contato@artetransformadora.org', '(21) 99999-9999', 'Complexo da Penha, Rio de Janeiro - RJ', 'Complexo da Penha e arredores', 'Crianças, adolescentes e jovens em situação de vulnerabilidade', '150', '12', 'Promover a transformação social através da arte e cultura.', 'Ser referência em impacto social e desenvolvimento humano na periferia.', 'Ética, Transparência, Respeito, Criatividade e Solidariedade.', 'O Projeto Arte Transformadora nasceu da necessidade de oferecer alternativas culturais e educativas para jovens do Complexo da Penha...');

-- =============================================
-- DIAGNÓSTICO (SWOT)
-- =============================================
CREATE TABLE IF NOT EXISTS diagnoses (
    id VARCHAR(50) PRIMARY KEY,
    project_id VARCHAR(50) NOT NULL,
    strengths TEXT DEFAULT NULL,
    weaknesses TEXT DEFAULT NULL,
    opportunities TEXT DEFAULT NULL,
    threats TEXT DEFAULT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- CAPTAÇÃO DE RECURSOS
-- =============================================
CREATE TABLE IF NOT EXISTS fundraising (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    funder VARCHAR(255) DEFAULT NULL,
    deadline DATE DEFAULT NULL,
    total_value DECIMAL(12,2) DEFAULT 0,
    requested_value DECIMAL(12,2) DEFAULT 0,
    status ENUM('Identificado','Preparando','Enviado','Aprovado','Rejeitado','Expirado') DEFAULT 'Identificado',
    link VARCHAR(500) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    observations TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- LOG DE AUDITORIA
-- =============================================
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entity_type VARCHAR(50) DEFAULT NULL,
    entity_id VARCHAR(50) DEFAULT NULL,
    user_id INT DEFAULT NULL,
    user_name VARCHAR(255) DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_action (action),
    INDEX idx_created (created_at),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Projeto de exemplo
INSERT INTO projects (id, name, general_objective, justification, specific_objectives, methodology, communication_plan, sustainability_plan, schedule_json, budget_json) VALUES
('proj-1', 'Arte na Comunidade', 'Promover a inclusão social através da arte no Complexo da Penha.', 'A carência de espaços culturais na região justifica a intervenção.', 'Capacitar 100 jovens em artes visuais\nRealizar 2 exposições anuais', 'Oficinas práticas semanais com artistas locais.', 'Redes sociais e carro de som local.', 'Parcerias com empresas privadas e editais públicos.', '[]', '[{"item":"Professores","value":12000},{"item":"Materiais","value":3000}]');
