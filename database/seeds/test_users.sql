-- Inserir usuários de teste
INSERT IGNORE INTO users (name, email, password, perfil_acesso, status, created_at, updated_at) VALUES
('Douglas costa', 'douglas.costa@example.com', '$2y$12$N9qo8uLOickgx2ZF.atbROQ3xHHx7I7.V7U.7WOYJvtpPwAf.1WM2', 1, 1, NOW(), NOW());

INSERT IGNORE INTO users (name, email, password, perfil_acesso, status, created_at, updated_at) VALUES
('Usuário Teste', 'test@example.com', '$2y$12$N9qo8uLOickgx2ZF.atbROQ3xHHx7I7.V7U.7WOYJvtpPwAf.1WM2', 2, 1, NOW(), NOW());

INSERT IGNORE INTO users (name, email, password, perfil_acesso, status, created_at, updated_at) VALUES
('Usuário Inativo', 'inativo@example.com', '$2y$12$N9qo8uLOickgx2ZF.atbROQ3xHHx7I7.V7U.7WOYJvtpPwAf.1WM2', 2, 0, NOW(), NOW());

INSERT IGNORE INTO users (name, email, password, perfil_acesso, status, created_at, updated_at) VALUES
('Douglas Silva', 'douglas2@example.com', '$2y$12$N9qo8uLOickgx2ZF.atbROQ3xHHx7I7.V7U.7WOYJvtpPwAf.1WM2', 2, 1, NOW(), NOW());

INSERT IGNORE INTO users (name, email, password, perfil_acesso, status, created_at, updated_at) VALUES
('João Costa', 'costa@example.com', '$2y$12$N9qo8uLOickgx2ZF.atbROQ3xHHx7I7.V7U.7WOYJvtpPwAf.1WM2', 2, 1, NOW(), NOW());
