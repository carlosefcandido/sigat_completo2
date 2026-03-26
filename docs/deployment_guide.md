# Guia de Implantação (Deployment Guide) - SIGAT PHP

Este documento descreve os passos necessários para tirar o SIGAT do ambiente local (WAMP/XAMPP) e colocá-lo em um servidor de hospedagem real.

## 1. Requisitos do Servidor
*   **PHP**: Versão 7.4 ou superior (Recomendado 8.1+).
*   **Banco de Dados**: MySQL 5.7+ ou MariaDB.
*   **Extensões PHP Necessárias**: `pdo_mysql`, `json`, `mbstring`, `fileinfo`.
*   **Servidor Web**: Apache (com suporte a `.htaccess`) ou Nginx.

---

## 2. Preparação dos Arquivos
Antes de subir, ajuste as configurações de conexão:

1.  Abra o arquivo `config/database.php`.
2.  Altere as constantes para os dados fornecidos pela sua hospedagem (Host, Usuário, Senha e Nome do Banco).
    ```php
    define('DB_HOST', 'seu_host_da_hospedagem');
    define('DB_NAME', 'seu_nome_de_banco');
    define('DB_USER', 'seu_usuario');
    define('DB_PASS', 'sua_senha');
    ```

---

## 3. Subindo os Arquivos
1.  Compacte a pasta `Projeto_PHP`.
2.  Utilize o Gerenciador de Arquivos da sua hospedagem ou um cliente FTP (como FileZilla) para subir o arquivo para a pasta `public_html` (ou equivalente).
3.  Descompacte os arquivos.

---

## 4. Instalação e Banco de Dados
Existem duas formas de configurar o banco de dados no servidor:

### Opção A: Usando o Script de Instalação (Recomendado)
1.  Acesse `https://seudominio.com/install.php`.
2.  O sistema verificará a conexão e criará todas as 18 tabelas automaticamente.
3.  Ao final, o script criará um arquivo chamado `install.lock` que bloqueia novas instalações por segurança.

### Opção B: Importação Manual
1.  Acesse o **phpMyAdmin** da sua hospedagem.
2.  Selecione o seu banco de dados.
3.  Clique em "Importar" e selecione o arquivo `database.sql` que está na raiz do projeto.

---

## 5. Permissões de Pasta
Para que o upload de fotos e logos funcione, certifique-se de que a pasta de uploads tenha permissão de escrita (CHMOD 755 ou 775):
*   `assets/uploads/`
*   `uploads/images/` (esta pasta será criada automaticamente pela API se tiver permissão).

---

## 6. Segurança Pós-Instalação
1.  **Remova o arquivo `install.php`** do seu servidor após a conclusão bem-sucedida.
2.  Certifique-se de que o arquivo `install.lock` está presente na raiz.
3.  Verifique se o seu `.htaccess` está protegendo o acesso direto às pastas sensíveis (config, includes).

---

## Suporte
Se encontrar erros de "500 Internal Server Error", verifique os logs de erro do PHP no seu painel de controle (cPanel/Plesk). O erro mais comum é a falta de alguma extensão ou credenciais de banco incorretas no `config/database.php`.
