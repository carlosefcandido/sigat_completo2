-- ==========================================================
-- SCRIPT DE ATUALIZAÇÃO DO BANCO DE DADOS (PRODUÇÃO)
-- Data: Março de 2026
-- Módulo: Beneficiários (Uploads e Histórico de Benefícios)
-- ==========================================================

-- 1. Adicionar novas colunas na tabela 'beneficiaries' para termos de imagem e saída
ALTER TABLE `beneficiaries`
ADD COLUMN `image_term_url` VARCHAR(500) DEFAULT NULL AFTER `photo_url`,
ADD COLUMN `exit_term_url` VARCHAR(500) DEFAULT NULL AFTER `image_term_url`;

-- (Nota: O comando AFTER acima visa organização, mas os campos podem ser adicionados no final)

-- 2. Criar a nova tabela de 'beneficiary_benefits' para registrar histórico
CREATE TABLE IF NOT EXISTS `beneficiary_benefits` (
    `id` VARCHAR(50) PRIMARY KEY,
    `beneficiary_id` VARCHAR(20) NOT NULL,
    `benefit_name` VARCHAR(255) NOT NULL,
    `date_received` DATE NOT NULL,
    `observations` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- FIM DA ATUALIZAÇÃO
-- ==========================================================
