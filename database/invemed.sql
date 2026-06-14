-- ============================================================
-- InveMed — Base de Dados
-- Projeto SIBDAS 2024/2025 | Aluno: 1241035
-- ============================================================

CREATE DATABASE IF NOT EXISTS invemed
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE invemed;

-- ------------------------------------------------------------
-- Tabela: utilizadores
-- Guarda os utilizadores com acesso à área privada
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS utilizadores (
    id_utilizador INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    email         VARCHAR(100)      NOT NULL,
    password_hash VARCHAR(255)      NOT NULL,
    nome          VARCHAR(100)      NOT NULL,
    criado_em     TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_utilizador),
    UNIQUE KEY uq_utilizador_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: localizacoes
-- Localização física dos equipamentos no hospital
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS localizacoes (
    id_localizacao INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    edificio       VARCHAR(100)  NOT NULL,
    piso           VARCHAR(50)   DEFAULT NULL,
    servico        VARCHAR(100)  NOT NULL,
    sala           VARCHAR(100)  DEFAULT NULL,
    PRIMARY KEY (id_localizacao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: fornecedores
-- Empresas fornecedoras de equipamentos e serviços
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS fornecedores (
    id_fornecedor   INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    nome            VARCHAR(150)  NOT NULL,
    nif             VARCHAR(20)   NOT NULL,
    contacto        VARCHAR(20)   DEFAULT NULL,
    email           VARCHAR(100)  DEFAULT NULL,
    website         VARCHAR(255)  DEFAULT NULL,
    morada          VARCHAR(255)  DEFAULT NULL,
    tipo            ENUM('Fabricante','Distribuidor','Assistência técnica','Consumíveis','Outro') DEFAULT NULL,
    pessoa_contacto VARCHAR(100)  DEFAULT NULL,
    telefone_pessoa VARCHAR(20)   DEFAULT NULL,
    observacoes     TEXT          DEFAULT NULL,
    PRIMARY KEY (id_fornecedor),
    UNIQUE KEY uq_fornecedor_nif (nif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: equipamentos
-- Inventário de equipamentos médicos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS equipamentos (
    id_equipamento  INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    codigo_interno  VARCHAR(50)    NOT NULL,
    designacao      VARCHAR(150)   NOT NULL,
    categoria       ENUM('Monitorização','Suporte de vida','Terapia','Diagnóstico','Laboratório','Esterilização','Reabilitação') DEFAULT NULL,
    marca           VARCHAR(100)   DEFAULT NULL,
    modelo          VARCHAR(100)   DEFAULT NULL,
    numero_serie    VARCHAR(100)   DEFAULT NULL,
    fabricante      VARCHAR(100)   DEFAULT NULL,
    data_aquisicao  DATE           DEFAULT NULL,
    ano_fabrico     YEAR           DEFAULT NULL,
    custo_aquisicao DECIMAL(10,2)  DEFAULT NULL,
    tipo_entrada    ENUM('Compra','Doação','Aluguer','Empréstimo') DEFAULT NULL,
    estado          ENUM('Ativo','Em manutenção','Inativo','Em calibração','Em quarentena','Abatido') NOT NULL DEFAULT 'Ativo',
    criticidade     ENUM('Baixa','Média','Alta','Suporte de vida') DEFAULT NULL,
    id_localizacao  INT UNSIGNED   DEFAULT NULL,
    observacoes     TEXT           DEFAULT NULL,
    PRIMARY KEY (id_equipamento),
    UNIQUE KEY uq_equipamento_codigo (codigo_interno),
    CONSTRAINT fk_equipamento_localizacao
        FOREIGN KEY (id_localizacao)
        REFERENCES localizacoes (id_localizacao)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: equipamento_fornecedor  (relação N:M)
-- Um equipamento pode ter vários fornecedores e vice-versa
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS equipamento_fornecedor (
    id_equipamento INT UNSIGNED NOT NULL,
    id_fornecedor  INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_equipamento, id_fornecedor),
    CONSTRAINT fk_ef_equipamento
        FOREIGN KEY (id_equipamento)
        REFERENCES equipamentos (id_equipamento)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_ef_fornecedor
        FOREIGN KEY (id_fornecedor)
        REFERENCES fornecedores (id_fornecedor)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: documentacao
-- Documentos associados a equipamentos (e opcionalmente a fornecedores)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS documentacao (
    id_documento     INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    tipo             ENUM('Manual de utilizador','Manual de serviço','Certificado de calibração','Contrato de manutenção','Fatura/Guia de aquisição','Declaração de conformidade','Relatório técnico') NOT NULL,
    nome             VARCHAR(200)  NOT NULL,
    data             DATE          NOT NULL,
    validade         DATE          DEFAULT NULL,
    caminho_ficheiro VARCHAR(500)  DEFAULT NULL,
    id_equipamento   INT UNSIGNED  NOT NULL,
    id_fornecedor    INT UNSIGNED  DEFAULT NULL,
    PRIMARY KEY (id_documento),
    CONSTRAINT fk_doc_equipamento
        FOREIGN KEY (id_equipamento)
        REFERENCES equipamentos (id_equipamento)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_doc_fornecedor
        FOREIGN KEY (id_fornecedor)
        REFERENCES fornecedores (id_fornecedor)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: garantias_contratos
-- Garantias e contratos de manutenção dos equipamentos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS garantias_contratos (
    id_garantia          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    id_equipamento       INT UNSIGNED  NOT NULL,
    entidade_responsavel VARCHAR(150)  DEFAULT NULL,
    data_inicio_garantia DATE          DEFAULT NULL,
    data_fim_garantia    DATE          NOT NULL,
    tem_contrato         ENUM('Sim','Não') DEFAULT NULL,
    tipo_contrato        ENUM('Preventiva','Corretiva','Completa','Outro') DEFAULT NULL,
    periodicidade        ENUM('Mensal','Trimestral','Semestral','Anual') DEFAULT NULL,
    observacoes          TEXT          DEFAULT NULL,
    PRIMARY KEY (id_garantia),
    CONSTRAINT fk_garantia_equipamento
        FOREIGN KEY (id_equipamento)
        REFERENCES equipamentos (id_equipamento)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------
-- Tabela: conteudos_publicos
-- Conteúdos dinâmicos da área pública geridos pelo backoffice
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS conteudos_publicos (
    id_conteudo   INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    chave         VARCHAR(100)  NOT NULL,
    titulo        VARCHAR(255)  DEFAULT NULL,
    conteudo      TEXT          DEFAULT NULL,
    imagem_path   VARCHAR(500)  DEFAULT NULL,
    atualizado_em TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id_utilizador INT UNSIGNED  DEFAULT NULL,
    PRIMARY KEY (id_conteudo),
    UNIQUE KEY uq_conteudo_chave (chave),
    CONSTRAINT fk_conteudo_utilizador
        FOREIGN KEY (id_utilizador)
        REFERENCES utilizadores (id_utilizador)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: logs
-- Registo de eventos do sistema (autenticação, CRUD, erros)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS logs (
    id_log        INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    tipo          ENUM('login_sucesso','login_falhado','logout','inserir','editar','eliminar','erro') NOT NULL,
    descricao     VARCHAR(500)  DEFAULT NULL,
    id_utilizador INT UNSIGNED  DEFAULT NULL,
    ip_address    VARCHAR(45)   DEFAULT NULL,
    criado_em     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_log),
    CONSTRAINT fk_log_utilizador
        FOREIGN KEY (id_utilizador)
        REFERENCES utilizadores (id_utilizador)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------
-- Tabela: slides_carousel
-- Slides do carrossel da área pública (ordem, imagem)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS slides_carousel (
    id_slide    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    ordem       TINYINT       NOT NULL,
    imagem_path VARCHAR(500)  NOT NULL,
    alt_text    VARCHAR(200)  DEFAULT NULL,
    PRIMARY KEY (id_slide),
    UNIQUE KEY uq_slide_ordem (ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: mensagens_contacto
-- Mensagens submetidas pelo formulário da área pública
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS mensagens_contacto (
    id_mensagem INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    nome        VARCHAR(150)  NOT NULL,
    email       VARCHAR(100)  NOT NULL,
    mensagem    TEXT          NOT NULL,
    lida        TINYINT(1)    NOT NULL DEFAULT 0,
    enviado_em  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_mensagem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- DADOS DE EXEMPLO (dados iniciais / seed)
-- ============================================================

-- Localizações iniciais (correspondem às opções dos formulários)
INSERT INTO localizacoes (edificio, piso, servico, sala) VALUES
('Edifício Principal', 'Piso 1', 'UCI',             'Sala UCI-A'),
('Edifício Principal', 'Piso 2', 'Medicina',         'Sala 201'),
('Edifício Principal', 'R/C',    'Urgência',          'Triagem'),
('Edifício Principal', 'Piso 3', 'Cardiologia',       'Sala 301'),
('Edifício Cirúrgico', 'Piso 1', 'Bloco Operatório',  'BO-1');

-- Fornecedores iniciais (correspondem às opções dos formulários)
INSERT INTO fornecedores (nome, nif, tipo) VALUES
('Philips Healthcare',   '501234567', 'Fabricante'),
('Dräger Portugal',      '502345678', 'Fabricante'),
('B. Braun Portugal',    '503456789', 'Distribuidor'),
('Zoll Medical',         '504567890', 'Fabricante'),
('GE Healthcare',        '505678901', 'Fabricante'),
('Tuttnauer Europe',     '506789012', 'Fabricante');

-- Slides iniciais do carrossel
INSERT INTO slides_carousel (ordem, imagem_path, alt_text) VALUES
(1, 'assets/img/Slide 1.png', 'Primeiro slide InveMed'),
(2, 'assets/img/Slide 2.png', 'Segundo slide InveMed'),
(3, 'assets/img/Slide 3.png', 'Terceiro slide InveMed');

-- Conteúdos iniciais da área pública
INSERT INTO conteudos_publicos (chave, titulo, conteudo) VALUES
('hero_titulo',        'InveMed — Gestão de Inventário Hospitalar', 'Solução web para gestão de equipamentos médicos'),
('hero_subtitulo',     'Organização, rastreabilidade e controlo total', NULL),
('sobre_titulo',       'Sobre a InveMed', 'A InveMed é uma empresa especializada em sistemas de informação para a área da saúde.'),
('servicos_texto',     'Gestão de inventário, documentação técnica e controlo de fornecedores.', NULL),
('contacto_email',     'geral@invemed.pt', NULL),
('contacto_telefone',  '222 000 111', NULL),
('contacto_morada',    'Rua da Saúde, 100, 4000-001 Porto', NULL),
('contacto_horario',   'Segunda a Sexta: 09h00 – 18h00', NULL);

-- ============================================================
-- UTILIZADOR ADMINISTRADOR
-- Para gerar o hash correto, correr em PHP:
--   echo password_hash('invemed123', PASSWORD_DEFAULT);
-- e substituir o valor abaixo antes de importar o ficheiro.
-- ============================================================
-- INSERT INTO utilizadores (email, password_hash, nome)
-- VALUES ('admin@invemed.pt', 'SUBSTITUIR_PELO_HASH', 'Administrador');
