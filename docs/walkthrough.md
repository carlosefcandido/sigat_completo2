# SIGAT PHP — Walkthrough

## O que foi construído

O sistema SIGAT foi reescrito do zero em **PHP + MySQL** com frontend **Bootstrap 5 + Tailwind CSS** (via CDN) e **JavaScript puro** — sem React, Vite ou Next.js.

## Estrutura do Projeto

```
Projeto_PHP/
├── config/database.php      — Conexão PDO MySQL
├── includes/auth.php         — Sessões, roles, autenticação
├── includes/functions.php    — Utilidades (ID, sanitize, audit)
├── api/ (19 arquivos)        — REST endpoints completos
├── templates/                — header, sidebar, footer
├── assets/css/custom.css     — Design system dark theme
├── pages/ (16 módulos)       — Todas as telas do sistema
├── login.php                 — Tela de login premium
├── index.php                 — Router com controle de acesso
├── install.php               — Script de instalação automática
└── database.sql              — Schema completo (18 tabelas)
```

## Como Instalar

1. **Inicie o WAMP** (Apache + MySQL rodando)
2. Acesse: `http://localhost/sigat%20completo/Projeto_PHP/install.php`
3. O script cria o banco `sigat_db`, tabelas e usuários automaticamente
4. Após sucesso, clique em **"Acessar SIGAT"**

## Credenciais

| Perfil | Email | Senha |
|--------|-------|-------|
| Admin | admin@sigat.com | SIGAT-Admin-2024 |
| Coordenação | coord@sigat.com | coord123 |

> [!NOTE]
> O admin será solicitado a trocar a senha no primeiro login.

## Módulos Implementados (16)

| Módulo | Funcionalidades |
|--------|----------------|
| **Dashboard** | Stats, documentos, finanças, eventos |
| **Beneficiários** | CRUD completo, PCD, CadÚnico, NIS |
| **Turmas** | Cards, vincular projeto/professor |
| **Plano de Aula** | Planos e relatórios por turma |
| **Projetos** | Cards, orçamento, objetivos |
| **Portfólio** | Galeria de resultados por projeto |
| **Atividades** | Quadro semanal colorido |
| **Eventos** | Filtros por status, CRUD |
| **Documentos** | Categorias, vencimento, status |
| **Organização** | Perfil singleton editável |
| **Diagnóstico** | SWOT por projeto (4 quadrantes) |
| **Captação** | Pipeline de fundraising |
| **Financeiro** | Receitas/despesas, saldo, categorias |
| **Usuários** | Criar, ativar/desativar |
| **Auditoria** | Log filtrável (admin only) |
| **Lixeira** | Restaurar itens excluídos |

- **ADMIN** — Acesso total ao sistema e configurações.
- **COORDENAÇÃO** — Gestão pedagógica, projetos e beneficiários.
- **PROFESSOR** — Filtro automático para ver apenas **suas turmas**. Relatórios, Planos de Aula, Agenda Semanal e **Chamada de Alunos**. Acesso a finanças e gestão de usuários é bloqueado.
- **FINANCEIRO** — Dashboard financeiro e gestão de transações/documentos.

## Novidades no Módulo de Turmas

- **Múltiplos Dias**: Suporte a turmas que ocorrem em vários dias da semana.
- **Agenda Semanal**: Visualização organizada por dia para facilitar a rotina do professor.
- **Registro de Chamada**: Interface dedicada dentro de cada turma para marcar presença/falta de alunos por data específica, com persistência inteligente (upsert).
- **Gestão de Alunos**: Inclusão de funcionalidade para matricular beneficiários em turmas específicas diretamente no modal.
- **Segurança Refinada**: Bloqueio de chamadas de API não autorizadas no Dashboard e telas de listagem para usuários com perfil Professor.

## Organização e Identidade Visual

- **Logo da Instituição**: Agora é possível fazer o upload da logomarca da organização no perfil. Esta logo é integrada automaticamente ao cabeçalho de todos os documentos oficiais do sistema.
- **Agenda da Diretoria**: O antigo "Quadro de Atividades" foi renomeado e reposicionado como a ferramenta central de agendamento estratégico da gestão.

## Módulo de Relatórios (NOVO)

- **Impressão Profissional**: Interface dedicada para geração de relatórios otimizados para papel (A4).
- **Tipos de Relatório**: 
  - **Beneficiários**: Listagem completa com dados de contato e acessibilidade.
  - **Financeiro**: Resumo mensal de receitas, despesas e saldo, com detalhamento de transações.
  - **Turmas**: Consolidação de horários, projetos vinculados e professores responsáveis.

## Melhorias no Financeiro

- **Vencimentos**: Exibição clara da **Data de Vencimento** na listagem de transações, com destaque visual para pagamentos atrasados.
- **Recorrência**: Suporte a despesas recorrentes (Mensal, Semanal, Anual). Transações recorrentes são identificadas com um ícone de sincronização na lista.
- **Modal Inteligente**: Campos adicionais no cadastro de transações para controle total de prazos e periodicidade.

## Design

- Dark theme premium com gradientes e glassmorphism
- Sidebar responsiva com collapse para mobile
- Toast notifications, badges de status, animações suaves
- Fonte Inter (Google Fonts), ícones Font Awesome 6
