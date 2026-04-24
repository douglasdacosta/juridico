-- Script SQL para adicionar usuários de teste ao banco juridico

-- Certifique-se de executar este arquivo no banco de dados 'juridico'
-- Você pode usar: mysql -u root -proot juridico < database/seeds/insert_test_users.sql

-- Inserir usuário "Douglas costa" (o que você está tentando buscar)
INSERT INTO users (name, email, password, perfil_acesso, status, created_at, updated_at)
VALUES ('Douglas costa', 'douglas.costa@example.com', '$2y$12$N9qo8uLOickgx2ZF.atbROQ3xHHx7I7.V7U.7WOYJvtpPwAf.1WM2', 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- Inserir outros usuários de teste
INSERT INTO users (name, email, password, perfil_acesso, status, created_at, updated_at)
VALUES ('Usuário Teste', 'test@example.com', '$2y$12$N9qo8uLOickgx2ZF.atbROQ3xHHx7I7.V7U.7WOYJvtpPwAf.1WM2', 2, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

INSERT INTO users (name, email, password, perfil_acesso, status, created_at, updated_at)
VALUES ('Usuário Inativo', 'inativo@example.com', '$2y$12$N9qo8uLOickgx2ZF.atbROQ3xHHx7I7.V7U.7WOYJvtpPwAf.1WM2', 2, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

INSERT INTO users (name, email, password, perfil_acesso, status, created_at, updated_at)
VALUES ('Douglas Silva', 'douglas2@example.com', '$2y$12$N9qo8uLOickgx2ZF.atbROQ3xHHx7I7.V7U.7WOYJvtpPwAf.1WM2', 2, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

INSERT INTO users (name, email, password, perfil_acesso, status, created_at, updated_at)
VALUES ('João Costa', 'costa@example.com', '$2y$12$N9qo8uLOickgx2ZF.atbROQ3xHHx7I7.V7U.7WOYJvtpPwAf.1WM2', 2, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- Confirmação
SELECT 'Usuários adicionados com sucesso!' as Mensagem;
SELECT COUNT(*) as Total_Usuarios FROM users;
