/**
* SIGAT - Script de Instalação
*/

// Trava de segurança
if (file_exists(__DIR__ . '/install.lock')) {
die("<h1>🛡️ SIGAT - Instalação Bloqueada</h1>
<p>O sistema já foi instalado. Para reinstalar, remova o arquivo <code>install.lock</code> na pasta raiz.</p>");
}

require_once __DIR__ . '/config/database.php';
$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASS;

echo "
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <title>SIGAT Instalação</title>";
    echo "<style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            padding: 40px;
            max-width: 700px;
            margin: 0 auto;
        }

        ";
 echo ".ok{color:#4ade80;}.err{color:#f87171;}.info{color:#60a5fa;}.box{background:#1e293b;border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:24px;margin:16px 0;}";
        echo "h1{font-size:28px;margin-bottom:8px;}h2{font-size:18px;color:#94a3b8;font-weight:400;}";
        echo ".btn{display:inline-block;background:linear-gradient(135deg,#14b8a6,#0d9488);color:white;padding:12px 28px;border-radius:12px;text-decoration:none;font-weight:600;margin-top:20px;}
    </style>
</head>

<body>";
    echo "<h1>🛡️ SIGAT - Instalação</h1>
    <h2>Sistema de Gerenciamento de ONG</h2>";

    try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<div class='box'>
        <p class='ok'>✅ Conexão com MySQL estabelecida</p>";

        // Drop and recreate DB for clean install
        $pdo->exec("DROP DATABASE IF EXISTS sigat_db");
        $pdo->exec("CREATE DATABASE sigat_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE sigat_db");
        echo "<p class='ok'>✅ Banco de dados 'sigat_db' criado</p>";

        // ===== CREATE ALL TABLES =====
        $pdo->exec("CREATE TABLE users (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela users</p>";

        $pdo->exec("CREATE TABLE beneficiaries (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela beneficiaries</p>";

        $pdo->exec("CREATE TABLE projects (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela projects</p>";

        $pdo->exec("CREATE TABLE classes (
        id VARCHAR(50) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        project_id VARCHAR(50) DEFAULT NULL,
        teacher_id INT DEFAULT NULL,
        schedule VARCHAR(255) DEFAULT NULL,
        days_of_week_json JSON DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
        FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela classes</p>";

        $pdo->exec("CREATE TABLE class_beneficiaries (
        class_id VARCHAR(50) NOT NULL,
        beneficiary_id VARCHAR(20) NOT NULL,
        PRIMARY KEY (class_id, beneficiary_id),
        FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
        FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela class_beneficiaries</p>";

        $pdo->exec("CREATE TABLE attendances (
        id VARCHAR(50) PRIMARY KEY,
        class_id VARCHAR(50) NOT NULL,
        date DATE NOT NULL,
        records_json JSON NOT NULL,
        professor_id INT DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
        FOREIGN KEY (professor_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela attendances</p>";

        $pdo->exec("CREATE TABLE lesson_plans (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela lesson_plans</p>";

        $pdo->exec("CREATE TABLE lesson_reports (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela lesson_reports</p>";

        $pdo->exec("CREATE TABLE transactions (
        id VARCHAR(50) PRIMARY KEY,
        description VARCHAR(500) NOT NULL,
        type ENUM('RECEITA','DESPESA') NOT NULL,
        category VARCHAR(255) DEFAULT NULL,
        value DECIMAL(12,2) NOT NULL DEFAULT 0,
        payment_method VARCHAR(100) DEFAULT NULL,
        date DATE NOT NULL,
        due_date DATE DEFAULT NULL,
        project_id VARCHAR(50) DEFAULT NULL,
        status ENUM('Pendente','Pago','Vencido') NOT NULL DEFAULT 'Pendente',
        attachment_url VARCHAR(500) DEFAULT NULL,
        observations TEXT DEFAULT NULL,
        created_by VARCHAR(255) DEFAULT NULL,
        created_by_id INT DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela transactions</p>";

        $pdo->exec("CREATE TABLE documents (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela documents</p>";

        $pdo->exec("CREATE TABLE portfolio_items (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela portfolio_items</p>";

        $pdo->exec("CREATE TABLE activity_board (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela activity_board</p>";

        $pdo->exec("CREATE TABLE events (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela events</p>";

        $pdo->exec("CREATE TABLE organization (
        id INT NOT NULL DEFAULT 1 PRIMARY KEY,
        name VARCHAR(255) DEFAULT NULL,
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela organization</p>";

        $pdo->exec("CREATE TABLE diagnoses (
        id VARCHAR(50) PRIMARY KEY,
        project_id VARCHAR(50) NOT NULL,
        strengths TEXT DEFAULT NULL,
        weaknesses TEXT DEFAULT NULL,
        opportunities TEXT DEFAULT NULL,
        threats TEXT DEFAULT NULL,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela diagnoses</p>";

        $pdo->exec("CREATE TABLE fundraising (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela fundraising</p>";

        $pdo->exec("CREATE TABLE audit_logs (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='ok'>✅ Tabela audit_logs</p>";
        echo "<p class='ok'><strong>✅ 18 tabelas criadas com sucesso!</strong></p>";

        // ===== SEED DATA =====
        $adminHash = password_hash('SIGAT-Admin-2024', PASSWORD_DEFAULT);
        $coordHash = password_hash('coord123', PASSWORD_DEFAULT);

        $pdo->prepare("INSERT INTO users (nome, email, senha_hash, perfil, ativo, must_change_password) VALUES
        (?,?,?,?,1,1)")
        ->execute(['Administrador SIGAT', 'admin@sigat.com', $adminHash, 'ADMIN']);
        echo "<p class='ok'>✅ Usuário admin criado</p>";

        $pdo->prepare("INSERT INTO users (nome, email, senha_hash, perfil, ativo, must_change_password) VALUES
        (?,?,?,?,1,0)")
        ->execute(['Carlos Coordenação', 'coord@sigat.com', $coordHash, 'COORDENAÇÃO']);
        echo "<p class='ok'>✅ Usuário coordenação criado</p>";

        $pdo->exec("INSERT INTO organization (id, name, cnpj, foundation_year, email, phone, address, territory,
        audience, beneficiaries_count, team_size, mission, vision, org_values, history) VALUES (1, 'Projeto Arte
        Transformadora', '26.095.649/0001-27', '2016', 'contato@artetransformadora.org', '(21) 99999-9999', 'Complexo da
        Penha, Rio de Janeiro - RJ', 'Complexo da Penha e arredores', 'Crianças, adolescentes e jovens em situação de
        vulnerabilidade', '150', '12', 'Promover a transformação social através da arte e cultura.', 'Ser referência em
        impacto social e desenvolvimento humano na periferia.', 'Ética, Transparência, Respeito, Criatividade e
        Solidariedade.', 'O Projeto Arte Transformadora nasceu da necessidade de oferecer alternativas culturais...')");
        echo "<p class='ok'>✅ Dados da organização inseridos</p>";

        $pdo->exec("INSERT INTO projects (id, name, general_objective, justification, specific_objectives, methodology,
        communication_plan, sustainability_plan, schedule_json, budget_json) VALUES ('proj-1', 'Arte na Comunidade',
        'Promover a inclusão social através da arte.', 'A carência de espaços culturais na região justifica a
        intervenção.', 'Capacitar 100 jovens em artes visuais', 'Oficinas práticas semanais com artistas locais.',
        'Redes sociais e carro de som local.', 'Parcerias com empresas privadas e editais públicos.', '[]',
        '[{\"item\":\"Professores\",\"value\":12000},{\"item\":\"Materiais\",\"value\":3000}]')");
        echo "<p class='ok'>✅ Projeto de exemplo criado</p>";

        echo "
    </div>
    <div class='box'>";
        echo "<p class='ok' style='font-size:18px;font-weight:bold;'>🎉 Instalação concluída!</p>";

        // Create lock file
        file_put_contents(__DIR__ . '/install.lock', date('Y-m-d H:i:s'));
        echo "<p class='info'>🛡️ Travas de segurança ativadas (install.lock criado).</p>";

        echo "<p><strong>Credenciais:</strong></p>";
        echo "<p class='info'>📧 Admin: admin@sigat.com / SIGAT-Admin-2024</p>";
        echo "<p class='info'>📧 Coord: coord@sigat.com / coord123</p>";
        echo "<a href='login.php' class='btn'>Acessar SIGAT →</a>";
        echo "</div>";

    } catch (PDOException $e) {
    echo "<div class='box'>
        <p class='err'>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p class='info'>Verifique se o WAMP está rodando e o MySQL está ativo.</p>
    </div>";
    }
    echo "
</body>

</html>";