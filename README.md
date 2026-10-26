# SIGAT Completo — Versão PHP/MySQL (ativa)

Sistema de gestão da **Art. Transformadora**: alunos, turmas, frequência, atividades,
beneficiários e auditoria. É a versão **em evolução ativa** do SIGAT — conectada ao
GitHub como `carlosefcandido/sigat_completo2` (branch `master`).

## Tecnologias

- **PHP 8.x** (PDO, `getConnection()` único em `config/database.php`)
- **MySQL** (banco `artetran_sigat_completo` — no servidor de produção em Hostinger
  usa usuário dedicado; localmente, root sem senha)
- Front-end: páginas PHP + templates em `templates/`, com APIs REST em `api/`

## Estrutura

```
api/                    # endpoints REST (attività, attendances, audit, beneficiaries…)
pages/                  # telas do sistema
templates/              # componentes de layout reutilizáveis
includes/               # header, footer, auth
config/database.php     # conexão (credenciais — troque localmente)
uploads/                # anexos/imagens dos registros (NÃO versionar conteúdo sensível)
database.sql            # estrutura inicial do banco
install.php             # instalação automatizada (gera install.lock)
docs/                   # documentação
PLANO_IMPLEMENTACAO.md  # plano de desenvolvimento
*.sql                   # migrações incrementais (update_*)
```

## Pré-requisitos

- PHP 8.x com `pdo_mysql`
- MySQL 8.x

## Instalação (máquina nova)

```bash
# 1. banco
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS artetran_sigat_completo CHARACTER SET utf8mb4;"

# 2. credenciais em config/database.php
#    padrão local: localhost / root / senha vazia

# 3. instalar via navegador (UMA vez)
#    http://localhost/sigat_completo/install.php
#    → cria tabelas + usuário inicial e gera install.lock (trava de segurança)
```

## Como rodar

**Via Apache (padrão deste servidor):** `http://localhost/sigat_completo`
(login na tela inicial).

**Servidor dev:**

```bash
php -S 0.0.0.0:8080
# acesse http://localhost:8080
```

## Sincronização com GitHub

```bash
cd /var/www
./sync-github.sh sigat_completo   # commit + push (branch master)
git pull                          # (dentro da pasta) para trazer alterações
```
