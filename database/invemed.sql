-- ============================================================
-- InveMed — Base de Dados
-- Projeto SIBDAS 2024/2025 | Aluno: 1241035
-- ============================================================

USE db1241035;

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
    PRIMARY KEY (id_localizacao),
    UNIQUE KEY uq_localizacao (edificio, servico)
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
    tipo           ENUM('Fabricante','Distribuidor','Assistência técnica','Consumíveis','Outro') DEFAULT NULL,
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
    UNIQUE KEY uq_documento (nome, id_equipamento),
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
    UNIQUE KEY uq_garantia_equip (id_equipamento),
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
('Edifício Cirúrgico', 'Piso 1', 'Bloco Operatório',  'BO-1')
ON DUPLICATE KEY UPDATE piso = VALUES(piso), sala = VALUES(sala);

-- Fornecedores iniciais (correspondem às opções dos formulários)
-- ON DUPLICATE KEY UPDATE: se o NIF já existe, atualiza os dados em vez de duplicar
INSERT INTO fornecedores (nome, nif, contacto, email, website, morada, pessoa_contacto, telefone_pessoa) VALUES
('Philips Healthcare',  '501234567', '210 123 456', 'saude@philips.pt',   'www.philips.pt/healthcare',  'Rua Filipe Folque, 2, 1050-113 Lisboa',               'Ana Rodrigues',  '912 111 222'),
('Dräger Portugal',     '502345678', '220 234 567', 'info@draeger.pt',     'www.draeger.com/pt_pt',      'Av. da Boavista, 1245, 4100-130 Porto',               'Carlos Mendes',  '923 222 333'),
('B. Braun Portugal',   '503456789', '219 345 678', 'geral@bbraun.pt',     'www.bbraun.pt',              'Estrada Consiglieri Pedroso, 84, 2734-503 Barcarena', 'Marta Costa',    '934 333 444'),
('Zoll Medical',        '504567890', '211 456 789', 'portugal@zoll.com',   'www.zoll.com',               'Av. Fontes Pereira de Melo, 6, 1069-001 Lisboa',      'João Ferreira',  '915 444 555'),
('GE Healthcare',       '505678901', '213 567 890', 'gehealthcare@ge.pt',  'www.gehealthcare.com/pt',    'Rua Rodrigo da Fonseca, 103, 1099-009 Lisboa',        'Sofia Alves',    '926 555 666'),
('Tuttnauer Europe',    '506789012', '214 678 901', 'info@tuttnauer.pt',   'www.tuttnauer.com',          'Rua da Prata, 80, 1100-415 Lisboa',                   'Rui Oliveira',   '937 666 777')
ON DUPLICATE KEY UPDATE
    nome            = VALUES(nome),
    contacto        = VALUES(contacto),
    email           = VALUES(email),
    website         = VALUES(website),
    morada          = VALUES(morada),
    pessoa_contacto = VALUES(pessoa_contacto),
    telefone_pessoa = VALUES(telefone_pessoa);
INSERT INTO equipamentos (codigo_interno, designacao, categoria, marca, modelo, numero_serie, fabricante, data_aquisicao, ano_fabrico, custo_aquisicao, tipo_entrada, estado, criticidade, id_localizacao, observacoes) VALUES
('01.001.00', 'Monitor Multiparamétrico',       'Monitorização',  'Philips',       'IntelliVue MP5',    'SN-PH-001', 'Philips Healthcare', '2021-03-15', 2020, 8500.00,  'Compra', 'Ativo',         'Alta',           1, NULL),
('02.001.00', 'Ventilador Invasivo',             'Suporte de vida','Dräger',        'Savina 300',        'SN-DR-001', 'Dräger',             '2020-06-01', 2019, 22000.00, 'Compra', 'Ativo',         'Suporte de vida',1, NULL),
('02.002.00', 'Desfibrilhador',                  'Suporte de vida','Zoll',          'R Series',          'SN-ZL-001', 'Zoll Medical',       '2022-01-10', 2021, 15000.00, 'Compra', 'Ativo',         'Suporte de vida',3, NULL),
('03.001.00', 'Bomba Infusora',                  'Terapia',        'B. Braun',      'Infusomat Space',   'SN-BB-001', 'B. Braun',           '2021-09-20', 2021, 3200.00,  'Compra', 'Ativo',         'Alta',           2, NULL),
('04.001.00', 'Eletrocardiógrafo',               'Diagnóstico',    'GE Healthcare', 'MAC 5500 HD',       'SN-GE-001', 'GE Healthcare',      '2019-11-05', 2019, 12000.00, 'Compra', 'Ativo',         'Média',          4, NULL),
('06.001.00', 'Autoclave',                       'Esterilização',  'Tuttnauer',     '2840 MK',           'SN-TT-001', 'Tuttnauer',          '2018-04-12', 2018, 9800.00,  'Compra', 'Em manutenção', 'Baixa',          5, 'Manutenção preventiva agendada'),
('03.002.00', 'Seringa Infusora',                'Terapia',        'B. Braun',      'Perfusor Space',    'SN-BB-002', 'B. Braun',           '2022-07-03', 2022, 2100.00,  'Compra', 'Ativo',         'Alta',           1, NULL),
('01.002.00', 'Monitor de Pressão Não Invasiva', 'Monitorização',  'Philips',       'SureSigns VM4',     'SN-PH-002', 'Philips Healthcare', '2020-02-28', 2020, 4500.00,  'Compra', 'Ativo',         'Média',          2, NULL)
ON DUPLICATE KEY UPDATE
    designacao      = VALUES(designacao),
    categoria       = VALUES(categoria),
    marca           = VALUES(marca),
    modelo          = VALUES(modelo),
    estado          = VALUES(estado),
    criticidade     = VALUES(criticidade),
    id_localizacao  = VALUES(id_localizacao);

-- Relações equipamento ↔ fornecedor (tipo = papel do fornecedor nesta relação)
INSERT INTO equipamento_fornecedor (id_equipamento, id_fornecedor, tipo) VALUES
(1, 1, 'Fabricante'),
(2, 2, 'Fabricante'),
(3, 4, 'Fabricante'),
(4, 3, 'Fabricante'),
(5, 5, 'Fabricante'),
(6, 6, 'Fabricante'),
(7, 3, 'Fabricante'),
(8, 1, 'Fabricante')
ON DUPLICATE KEY UPDATE tipo = VALUES(tipo);
-- Slides iniciais do carrossel
INSERT INTO slides_carousel (ordem, imagem_path, alt_text) VALUES
(1, 'assets/img/Slide 1.png', 'Primeiro slide InveMed'),
(2, 'assets/img/Slide 2.png', 'Segundo slide InveMed'),
(3, 'assets/img/Slide 3.png', 'Terceiro slide InveMed')
ON DUPLICATE KEY UPDATE imagem_path = VALUES(imagem_path), alt_text = VALUES(alt_text);

-- Conteúdos iniciais da área pública
INSERT INTO conteudos_publicos (chave, titulo, conteudo) VALUES
('hero_titulo',        'InveMed — Gestão de Inventário Hospitalar', 'Solução web para gestão de equipamentos médicos'),
('hero_subtitulo',     'Organização, rastreabilidade e controlo total', NULL),
('sobre_titulo',       'Sobre a InveMed', 'A InveMed é uma empresa especializada em sistemas de informação para a área da saúde.'),
('servicos_texto',     'Gestão de inventário, documentação técnica e controlo de fornecedores.', NULL),
('contacto_email',     'geral@invemed.pt', NULL),
('contacto_telefone',  '222 000 111', NULL),
('contacto_morada',    'Rua da Saúde, 100, 4000-001 Porto', NULL),
('contacto_horario',   'Segunda a Sexta: 09h00 – 18h00', NULL)
ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), conteudo = VALUES(conteudo);

INSERT INTO documentacao (tipo, nome, data, validade, caminho_ficheiro, id_equipamento, id_fornecedor) VALUES
('Manual de utilizador',       'Manual de Utilizador — IntelliVue MP5',       '2021-03-15', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_utilizador_intellivue_mp5.html',       1, 1),
('Manual de serviço',          'Manual de Serviço — Savina 300',              '2020-06-01', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_servico_savina300.html',              2, 2),
('Manual de utilizador',       'Manual de Utilizador — Infusomat Space',      '2021-09-20', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_utilizador_infusomat_space.html',     4, 3),
('Fatura/Guia de aquisição',   'Fatura de Aquisição — Ventilador Savina 300', '2020-06-01', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/fatura_ventilador_savina300.html',           2, 2),
('Fatura/Guia de aquisição',   'Fatura de Aquisição — Bomba Infusora Space',  '2021-09-20', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/fatura_bomba_infusora_space.html',           4, 3),
('Declaração de conformidade', 'Declaração de Conformidade — Desfibrilhador', '2022-01-10', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/declaracao_conformidade_desfibrilhador.html', 3, 4),
('Relatório técnico',          'Relatório Técnico — Autoclave 2840 MK',       '2025-11-15', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/relatorio_tecnico_autoclave.html',            6, 6),
('Certificado de calibração',  'Certificado de Calibração — IntelliVue MP5',  '2025-06-20', '2026-09-20', '/sibdas/1241035/invemed/uploads/documentacao/certificado_calibracao_intellivue_mp5.html',  1, 1),
('Certificado de calibração',  'Certificado de Calibração — MAC 5500 HD',     '2026-01-10', '2027-01-10', '/sibdas/1241035/invemed/uploads/documentacao/certificado_calibracao_mac5500hd.html',      5, 5),
('Contrato de manutenção',     'Contrato de Manutenção — Autoclave 2840 MK',  '2024-04-01', '2026-12-31', '/sibdas/1241035/invemed/uploads/documentacao/contrato_manutencao_autoclave.html',         6, 6),
('Certificado de calibração',  'Certificado de Calibração — Desfibrilhador',  '2024-06-15', '2025-06-15', '/sibdas/1241035/invemed/uploads/documentacao/certificado_calibracao_desfibrilhador.html', 3, 4),
('Contrato de manutenção',     'Contrato de Manutenção — MAC 5500 HD',        '2023-01-05', '2025-01-05', '/sibdas/1241035/invemed/uploads/documentacao/contrato_manutencao_mac5500hd.html',         5, 5)
ON DUPLICATE KEY UPDATE
    tipo             = VALUES(tipo),
    data             = VALUES(data),
    validade         = VALUES(validade),
    caminho_ficheiro = VALUES(caminho_ficheiro),
    id_fornecedor    = VALUES(id_fornecedor);

INSERT INTO garantias_contratos (id_equipamento, entidade_responsavel, data_inicio_garantia, data_fim_garantia, tem_contrato, tipo_contrato, periodicidade, observacoes) VALUES
(1, 'Philips Healthcare',  '2021-03-15', '2024-03-15', 'Sim', 'Preventiva', 'Anual',     'Garantia expirada. Contrato de manutenção ativo separadamente.'),
(2, 'Dräger Portugal',     '2020-06-01', '2023-06-01', 'Sim', 'Completa',   'Anual',     NULL),
(3, 'Zoll Medical',        '2022-01-10', '2026-08-10', 'Não', NULL,          NULL,        'Sem contrato de manutenção ativo.'),
(4, 'B. Braun Portugal',   '2021-09-20', '2025-09-20', 'Não', NULL,          NULL,        NULL),
(5, 'GE Healthcare',       '2019-11-05', '2026-07-15', 'Sim', 'Preventiva', 'Anual',     NULL),
(6, 'Tuttnauer Europe',    '2024-04-01', '2027-04-01', 'Sim', 'Completa',   'Anual',     'Contrato inclui visita anual preventiva e resposta corretiva em 48h úteis.'),
(7, 'B. Braun Portugal',   '2022-07-03', '2025-07-03', 'Não', NULL,          NULL,        NULL),
(8, 'Philips Healthcare',  '2020-02-28', '2027-02-28', 'Sim', 'Preventiva', 'Semestral', NULL)
ON DUPLICATE KEY UPDATE
    entidade_responsavel  = VALUES(entidade_responsavel),
    data_inicio_garantia  = VALUES(data_inicio_garantia),
    data_fim_garantia     = VALUES(data_fim_garantia),
    tem_contrato          = VALUES(tem_contrato),
    tipo_contrato         = VALUES(tipo_contrato),
    periodicidade         = VALUES(periodicidade),
    observacoes           = VALUES(observacoes);

-- ============================================================
-- UTILIZADOR ADMINISTRADOR
-- Para gerar o hash correto, correr em PHP:
--   echo password_hash('invemed123', PASSWORD_DEFAULT);
-- e substituir o valor abaixo antes de importar o ficheiro.
-- ============================================================
-- INSERT INTO utilizadores (email, password_hash, nome)
-- VALUES ('admin@invemed.pt', 'SUBSTITUIR_PELO_HASH', 'Administrador');