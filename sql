


CREATE DATABASE IF NOT EXISTS tcc_peletronico CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tcc_peletronico;


CREATE TABLE IF NOT EXISTS funcionario (
    id_funcionario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    adm TINYINT(1) DEFAULT 0,
    ativo TINYINT(1) DEFAULT 1,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_cpf (cpf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS qrcode (
    id_qrcode INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(64) NOT NULL UNIQUE,
    data_geracao DATETIME NOT NULL,
    data_expiracao DATETIME NOT NULL,
    status ENUM('ativo', 'expirado', 'utilizado') DEFAULT 'ativo',
    ip_origem VARCHAR(45),
    INDEX idx_token (token),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS ponto (
    id_ponto INT AUTO_INCREMENT PRIMARY KEY,
    id_funcionario INT NOT NULL,
    id_qrcode INT,
    tipo_registro ENUM('entrada', 'intervalo', 'retorno', 'saida') NOT NULL,
    data_hora DATETIME NOT NULL,
    ip_registro VARCHAR(45),
    observacao TEXT,
    FOREIGN KEY (id_funcionario) REFERENCES funcionario(id_funcionario) ON DELETE CASCADE,
    FOREIGN KEY (id_qrcode) REFERENCES qrcode(id_qrcode) ON DELETE SET NULL,
    INDEX idx_funcionario_data (id_funcionario, data_hora),
    INDEX idx_data (data_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS feriados (
    id_feriado INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    data DATE NOT NULL UNIQUE,
    tipo ENUM('nacional', 'estadual', 'municipal') NOT NULL,
    INDEX idx_data (data)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de logs de auditoria
CREATE TABLE IF NOT EXISTS logs_auditoria (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_funcionario INT,
    acao VARCHAR(100) NOT NULL,
    descricao TEXT,
    ip_origem VARCHAR(45),
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_funcionario) REFERENCES funcionario(id_funcionario) ON DELETE SET NULL,
    INDEX idx_data (data_hora),
    INDEX idx_funcionario (id_funcionario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de tentativas de login
CREATE TABLE IF NOT EXISTS tentativas_login (
    id_tentativa INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_origem VARCHAR(45) NOT NULL,
    sucesso TINYINT(1) DEFAULT 0,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email_ip (email, ip_origem),
    INDEX idx_data (data_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir usuário administrador padrão
-- Senha: Admin@123
INSERT INTO funcionario (nome, telefone, cpf, email, senha, adm) 
VALUES (
    'Administrador', 
    '(00) 00000-0000', 
    '00000000000', 
    'admin@sistema.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    1
) ON DUPLICATE KEY UPDATE id_funcionario=id_funcionario;

--limpar tokens expirados
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS limpar_tokens_expirados()
BEGIN
    UPDATE qrcode 
    SET status = 'expirado' 
    WHERE status = 'ativo' 
    AND data_expiracao < NOW();
    
    DELETE FROM tentativas_login 
    WHERE data_hora < DATE_SUB(NOW(), INTERVAL 24 HOUR);
END //
DELIMITER ;

-- View para relatório de frequência
CREATE OR REPLACE VIEW vw_frequencia_mensal AS
SELECT 
    f.id_funcionario,
    f.nome,
    DATE_FORMAT(p.data_hora, '%Y-%m') as mes_ano,
    COUNT(DISTINCT DATE(p.data_hora)) as dias_trabalhados,
    COUNT(CASE WHEN p.tipo_registro = 'entrada' THEN 1 END) as total_entradas,
    COUNT(CASE WHEN p.tipo_registro = 'saida' THEN 1 END) as total_saidas
FROM funcionario f
LEFT JOIN ponto p ON f.id_funcionario = p.id_funcionario
GROUP BY f.id_funcionario, f.nome, mes_ano;


-- DADOS DE TESTE

-- Funcionário de teste
-- Senha: Teste@123
INSERT INTO funcionario (nome, telefone, cpf, email, senha) 
VALUES (
    'João da Silva', 
    '(17) 98765-4321', 
    '12345678901', 
    'joao@teste.com', 
    '$2y$10$YmVkZjM4ZTNiZjE5ZjE5Z.VxYqvKxHzKhRqZGZXZGZXZGZXZGZXZGZ'
);

-- Registro de ponto de exemplo
INSERT INTO ponto (id_funcionario, tipo_registro, data_hora) VALUES
(2, 'entrada', '2025-01-15 08:00:00'),
(2, 'intervalo', '2025-01-15 12:00:00'),
(2, 'retorno', '2025-01-15 13:00:00'),
(2, 'saida', '2025-01-15 17:00:00');