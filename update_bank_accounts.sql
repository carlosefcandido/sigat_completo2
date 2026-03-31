-- ==========================================================
-- SCRIPT DE ATUALIZAÇÃO DO BANCO DE DADOS
-- Data: Março de 2026
-- Módulo: Financeiro (Contas Bancárias + Projeto em Transações)
-- ==========================================================

-- 1. Criar tabela de Contas Bancárias (mesma collation do BD existente)
CREATE TABLE IF NOT EXISTS `bank_accounts` (
    `id` VARCHAR(50) PRIMARY KEY,
    `bank_name` VARCHAR(255) NOT NULL COMMENT 'Nome do banco (ex: Banco do Brasil)',
    `agency` VARCHAR(20) DEFAULT NULL COMMENT 'Número da agência',
    `account_number` VARCHAR(30) NOT NULL COMMENT 'Número da conta',
    `account_type` ENUM('Corrente','Poupança','Pagamento') NOT NULL DEFAULT 'Corrente',
    `holder_name` VARCHAR(255) DEFAULT NULL COMMENT 'Nome do titular',
    `holder_document` VARCHAR(25) DEFAULT NULL COMMENT 'CPF/CNPJ do titular',
    `pix_key` VARCHAR(255) DEFAULT NULL COMMENT 'Chave PIX (se houver)',
    `project_id` VARCHAR(50) DEFAULT NULL COMMENT 'Projeto vinculado',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `observations` TEXT DEFAULT NULL,
    `created_by` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Adicionar coluna bank_account_id na tabela transactions (vínculo opcional)
ALTER TABLE `transactions`
ADD COLUMN `bank_account_id` VARCHAR(50) DEFAULT NULL AFTER `project_id`;

ALTER TABLE `transactions`
ADD CONSTRAINT `fk_transactions_bank_account`
FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts`(`id`) ON DELETE SET NULL;

-- ==========================================================
-- FIM DA ATUALIZAÇÃO
-- ==========================================================
