
-- ============================================================
-- DADOS DE EXEMPLO (dados iniciais / seed)
-- ============================================================

-- Localizações
INSERT INTO localizacoes (edificio, piso, servico, sala) VALUES
('Edifício Principal', 'Piso 1',  'UCI',               'Sala UCI-A'),
('Edifício Principal', 'Piso 2',  'Medicina',          'Sala 201'),
('Edifício Principal', 'R/C',     'Urgência',          'Triagem'),
('Edifício Principal', 'Piso 3',  'Cardiologia',       'Sala 301'),
('Edifício Cirúrgico',  'Piso 1', 'Bloco Operatório',  'BO-1'),
('Edifício Principal', 'R/C',     'Laboratório',       'Sala Lab-1'),
('Edifício Principal', 'Piso 4',  'Pediatria',         'Sala 401'),
('Edifício Principal', 'Piso -1', 'Imagiologia',       'Sala Raio-X'),
('Edifício Cirúrgico',  'Piso 2', 'Fisioterapia',      'Sala FT-1'),
('Edifício Principal', 'Piso 5',  'Neonatologia',      'Sala Neo-1'),
('Edifício Principal', 'Piso 2',  'Hemodiálise',       'Sala HD-1'),
('Edifício Principal', 'Piso 3',  'Gastroenterologia', 'Sala Endoscopia'),
('Edifício Principal', 'Piso 4',  'Obstetrícia',       'Sala Partos'),
('Edifício Principal', 'Piso 2',  'Pneumologia',       'Consulta 5'),
('Edifício Cirúrgico',  'R/C',    'CME',               'Sala Esterilização')
ON DUPLICATE KEY UPDATE piso = VALUES(piso), sala = VALUES(sala);

-- Fornecedores
-- ON DUPLICATE KEY UPDATE: se o NIF já existe, atualiza os dados em vez de duplicar
INSERT INTO fornecedores (nome, nif, contacto, email, website, morada, pessoa_contacto, telefone_pessoa) VALUES
('Philips Healthcare',              '501234567', '210 123 456', 'saude@philips.pt',        'www.philips.pt/healthcare',         'Rua Filipe Folque, 2, 1050-113 Lisboa',                'Ana Rodrigues',  '912 111 222'),
('Dräger Portugal',                 '502345678', '220 234 567', 'info@draeger.pt',         'www.draeger.com/pt_pt',             'Av. da Boavista, 1245, 4100-130 Porto',                'Carlos Mendes',  '923 222 333'),
('B. Braun Portugal',                '503456789', '219 345 678', 'geral@bbraun.pt',         'www.bbraun.pt',                     'Estrada Consiglieri Pedroso, 84, 2734-503 Barcarena',  'Marta Costa',    '934 333 444'),
('Zoll Medical',                    '504567890', '211 456 789', 'portugal@zoll.com',       'www.zoll.com',                      'Av. Fontes Pereira de Melo, 6, 1069-001 Lisboa',       'João Ferreira',  '915 444 555'),
('GE Healthcare',                   '505678901', '213 567 890', 'gehealthcare@ge.pt',      'www.gehealthcare.com/pt',           'Rua Rodrigo da Fonseca, 103, 1099-009 Lisboa',         'Sofia Alves',    '926 555 666'),
('Tuttnauer Europe',                '506789012', '214 678 901', 'info@tuttnauer.pt',       'www.tuttnauer.com',                 'Rua da Prata, 80, 1100-415 Lisboa',                    'Rui Oliveira',   '937 666 777'),
('Mindray Medical Portugal',        '507890123', '218 111 222', 'portugal@mindray.com',    'www.mindray.com',                   'Av. da Liberdade, 110, 1250-146 Lisboa',               'Pedro Santos',   '918 111 222'),
('Siemens Healthineers',            '508901234', '219 222 333', 'healthineers@siemens.pt', 'www.siemens-healthineers.com/pt',   'Rua Irmãos Siemens, 1, 2720-093 Amadora',              'Beatriz Lima',   '929 222 333'),
('Getinge Group',                   '509012345', '220 333 444', 'info@getinge.com',        'www.getinge.com',                   'Estrada Nacional 117, 2720-092 Amadora',               'Tiago Rocha',    '930 333 444'),
('Roche Diagnostics Portugal',      '510123456', '217 444 555', 'diagnostics@roche.pt',    'www.roche.pt',                      'Estrada Nacional 249-1, 2720-413 Amadora',             'Inês Carvalho',  '931 444 555'),
('Fresenius Medical Care Portugal', '511234567', '212 111 333', 'fmc@fresenius.pt',        'www.freseniusmedicalcare.pt',       'Rua Alfredo da Silva, 16, 1300-040 Lisboa',            'Marco Pinto',    '912 333 444'),
('Olympus Portugal',                '512345678', '213 222 444', 'info@olympus.pt',         'www.olympus.pt',                    'Rua de Tomar, 22, 2685-338 Sacavém',                   'Sara Nogueira',  '913 444 555'),
('Medtronic Portugal',              '513456789', '214 333 555', 'portugal@medtronic.com',  'www.medtronic.pt',                  'Av. José Malhoa, 16, 1070-159 Lisboa',                 'Rui Almeida',    '914 555 666'),
('Baxter Portugal',                 '514567890', '215 444 666', 'info@baxter.pt',          'www.baxter.pt',                     'Rua General Firmino Miguel, 5, 1600-100 Lisboa',       'Cláudia Reis',   '915 666 777')
ON DUPLICATE KEY UPDATE
    nome            = VALUES(nome),
    contacto        = VALUES(contacto),
    email           = VALUES(email),
    website         = VALUES(website),
    morada          = VALUES(morada),
    pessoa_contacto = VALUES(pessoa_contacto),
    telefone_pessoa = VALUES(telefone_pessoa);

-- Equipamentos — estados, categorias e criticidades deliberadamente variados,
-- para cobrir todos os valores possíveis usados na aplicação.
INSERT INTO equipamentos (codigo_interno, designacao, categoria, marca, modelo, numero_serie, fabricante, data_aquisicao, ano_fabrico, custo_aquisicao, tipo_entrada, estado, criticidade, id_localizacao, observacoes) VALUES
('01.001.00', 'Monitor Multiparamétrico',            'Monitorização',   'Philips',     'IntelliVue MP5',       'SN-PH-001', 'Philips Healthcare',   '2021-03-15', 2020, 8500.00,  'Compra', 'Ativo',          'Alta',             (SELECT id_localizacao FROM localizacoes WHERE servico = 'UCI'),               NULL),
('02.001.00', 'Ventilador Invasivo',                  'Suporte de vida', 'Dräger',      'Savina 300',           'SN-DR-001', 'Dräger',               '2020-06-01', 2019, 22000.00, 'Compra', 'Ativo',          'Suporte de vida', (SELECT id_localizacao FROM localizacoes WHERE servico = 'UCI'),               NULL),
('02.002.00', 'Desfibrilhador',                       'Suporte de vida', 'Zoll',        'R Series',             'SN-ZL-001', 'Zoll Medical',         '2022-01-10', 2021, 15000.00, 'Compra', 'Ativo',          'Suporte de vida', (SELECT id_localizacao FROM localizacoes WHERE servico = 'Urgência'),          NULL),
('03.001.00', 'Bomba Infusora',                       'Terapia',         'B. Braun',    'Infusomat Space',      'SN-BB-001', 'B. Braun',             '2021-09-20', 2021, 3200.00,  'Compra', 'Ativo',          'Alta',             (SELECT id_localizacao FROM localizacoes WHERE servico = 'Medicina'),          NULL),
('04.001.00', 'Eletrocardiógrafo',                    'Diagnóstico',     'GE Healthcare','MAC 5500 HD',         'SN-GE-001', 'GE Healthcare',        '2019-11-05', 2019, 12000.00, 'Compra', 'Ativo',          'Média',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Cardiologia'),       NULL),
('06.001.00', 'Autoclave',                            'Esterilização',   'Tuttnauer',   '2840 MK',              'SN-TT-001', 'Tuttnauer',            '2018-04-12', 2018, 9800.00,  'Compra', 'Em manutenção',  'Baixa',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Bloco Operatório'),  'Manutenção preventiva agendada'),
('03.002.00', 'Seringa Infusora',                     'Terapia',         'B. Braun',    'Perfusor Space',       'SN-BB-002', 'B. Braun',             '2022-07-03', 2022, 2100.00,  'Compra', 'Ativo',          'Alta',             (SELECT id_localizacao FROM localizacoes WHERE servico = 'UCI'),               NULL),
('01.002.00', 'Monitor de Pressão Não Invasiva',      'Monitorização',   'Philips',     'SureSigns VM4',        'SN-PH-002', 'Philips Healthcare',   '2020-02-28', 2020, 4500.00,  'Compra', 'Ativo',          'Média',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Medicina'),          NULL),
('05.001.00', 'Centrífuga de Laboratório',            'Laboratório',     'Hettich',     'Universal 320',        'SN-HT-001', 'Hettich',              '2021-05-10', 2021, 6500.00,  'Compra', 'Ativo',          'Baixa',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Laboratório'),       NULL),
('05.002.00', 'Analisador Bioquímico',                'Laboratório',     'Roche',       'Cobas c311',           'SN-RC-001', 'Roche Diagnostics',    '2022-02-14', 2022, 28000.00, 'Compra', 'Ativo',          'Média',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Laboratório'),       NULL),
('04.002.00', 'Ecógrafo',                             'Diagnóstico',     'Siemens',     'Acuson P500',          'SN-SM-001', 'Siemens Healthineers', '2020-09-01', 2020, 19500.00, 'Compra', 'Ativo',          'Alta',             (SELECT id_localizacao FROM localizacoes WHERE servico = 'Imagiologia'),       NULL),
('07.001.00', 'Equipamento de Eletroterapia',         'Reabilitação',    'Chattanooga', 'Intelect Advanced',    'SN-CH-001', 'Chattanooga',          '2019-08-20', 2019, 5200.00,  'Compra', 'Ativo',          'Baixa',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Fisioterapia'),      NULL),
('01.003.00', 'Oxímetro de Pulso',                    'Monitorização',   'Mindray',     'PM-60',                'SN-MD-001', 'Mindray',              '2023-01-05', 2023, 1200.00,  'Compra', 'Ativo',          'Média',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'UCI'),               NULL),
('03.003.00', 'Unidade Eletrocirúrgica',              'Terapia',         'Covidien',    'Force FX-8C',          'SN-CV-001', 'Covidien',             '2021-11-30', 2021, 14500.00, 'Compra', 'Ativo',          'Alta',             (SELECT id_localizacao FROM localizacoes WHERE servico = 'Bloco Operatório'),  NULL),
('02.003.00', 'Ventilador Pulmonar Neonatal',         'Suporte de vida', 'Dräger',      'Babylog VN500',        'SN-DR-002', 'Dräger',               '2022-04-12', 2022, 26500.00, 'Compra', 'Ativo',          'Suporte de vida', (SELECT id_localizacao FROM localizacoes WHERE servico = 'Neonatologia'),      NULL),
('03.004.00', 'Bomba de Hemodiálise',                 'Terapia',         'Fresenius',   '4008S',                'SN-FR-001', 'Fresenius',            '2021-07-08', 2021, 17800.00, 'Compra', 'Em quarentena',  'Alta',             (SELECT id_localizacao FROM localizacoes WHERE servico = 'Hemodiálise'),       NULL),
('04.003.00', 'Endoscópio Flexível',                  'Diagnóstico',     'Olympus',     'CV-190',               'SN-OL-001', 'Olympus',              '2020-12-01', 2020, 32000.00, 'Compra', 'Em calibração',  'Média',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Gastroenterologia'), NULL),
('01.004.00', 'Monitor Fetal CTG',                    'Monitorização',   'GE',          'Corometrics 250cx',    'SN-GE-002', 'GE Healthcare',        '2023-03-22', 2023, 7800.00,  'Compra', 'Ativo',          'Alta',             (SELECT id_localizacao FROM localizacoes WHERE servico = 'Obstetrícia'),       NULL),
('03.005.00', 'Bomba de Insulina',                    'Terapia',         'Medtronic',   'MiniMed 780G',         'SN-MT-001', 'Medtronic',            '2023-06-15', 2023, 3400.00,  'Compra', 'Inativo',        'Média',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Medicina'),          NULL),
('04.004.00', 'Espirómetro',                          'Diagnóstico',     'Vitalograph', 'Pneumotrac',           'SN-VT-001', 'Vitalograph',          '2019-10-10', 2019, 2100.00,  'Compra', 'Ativo',          'Baixa',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Pneumologia'),       NULL),
('06.002.00', 'Autoclave de Bancada',                 'Esterilização',   'Tuttnauer',   '3870ELVC',             'SN-TT-002', 'Tuttnauer',            '2022-08-19', 2022, 8900.00,  'Compra', 'Em manutenção',  'Baixa',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'CME'),               NULL),
('07.002.00', 'Equipamento de Ultrassom Terapêutico', 'Reabilitação',    'Chattanooga', 'Intelect Transport',   'SN-CH-002', 'Chattanooga',          '2020-05-05', 2020, 3900.00,  'Compra', 'Abatido',        'Baixa',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Fisioterapia'),      NULL),
('01.005.00', 'Monitor de Sinais Vitais Pediátrico',  'Monitorização',   'Philips',     'IntelliVue MX450',     'SN-PH-002', 'Philips',              '2022-09-01', 2022, 6200.00,  'Compra', 'Ativo',          'Alta',             (SELECT id_localizacao FROM localizacoes WHERE servico = 'Pediatria'),         NULL),
('03.006.00', 'Bomba de Infusão Pediátrica',          'Terapia',         'B. Braun',    'Infusomat fmS',        'SN-BB-003', 'B. Braun',             '2022-09-01', 2022, 2800.00,  'Compra', 'Ativo',          'Alta',             (SELECT id_localizacao FROM localizacoes WHERE servico = 'Pediatria'),         NULL),
('02.004.00', 'Desfibrilhador Portátil',              'Suporte de vida', 'Zoll',        'AED Plus',             'SN-ZL-002', 'Zoll',                 '2023-02-10', 2023, 4200.00,  'Compra', 'Ativo',          'Suporte de vida', (SELECT id_localizacao FROM localizacoes WHERE servico = 'Urgência'),          NULL),
('04.005.00', 'Eletrocardiógrafo de Esforço',         'Diagnóstico',     'GE',          'CASE',                 'SN-GE-003', 'GE Healthcare',        '2021-04-05', 2021, 15500.00, 'Compra', 'Ativo',          'Média',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Cardiologia'),       NULL),
('04.006.00', 'Aparelho de Raio-X Portátil',          'Diagnóstico',     'GE',          'Optima XR240amx',      'SN-GE-004', 'GE Healthcare',        '2020-03-18', 2020, 45000.00, 'Compra', 'Ativo',          'Alta',             (SELECT id_localizacao FROM localizacoes WHERE servico = 'Imagiologia'),       NULL),
('02.005.00', 'Incubadora Neonatal',                  'Suporte de vida', 'Dräger',      'Caleo',                'SN-DR-003', 'Dräger',               '2021-10-25', 2021, 21000.00, 'Compra', 'Ativo',          'Suporte de vida', (SELECT id_localizacao FROM localizacoes WHERE servico = 'Neonatologia'),      NULL),
('04.007.00', 'Ecógrafo Obstétrico',                  'Diagnóstico',     'GE',          'Voluson E10',          'SN-GE-005', 'GE Healthcare',        '2022-11-30', 2022, 38000.00, 'Compra', 'Ativo',          'Alta',             (SELECT id_localizacao FROM localizacoes WHERE servico = 'Obstetrícia'),       NULL),
('03.007.00', 'Concentrador de Oxigénio',              'Terapia',         'Philips',     'EverFlo',              'SN-PH-003', 'Philips',              '2020-07-14', 2020, 1500.00,  'Compra', 'Ativo',          'Média',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Pneumologia'),       NULL),
('03.008.00', 'Bomba de Seringa',                     'Terapia',         'B. Braun',    'Perfusor Compact Plus','SN-BB-004', 'B. Braun',             '2022-01-20', 2022, 1900.00,  'Compra', 'Ativo',          'Alta',             (SELECT id_localizacao FROM localizacoes WHERE servico = 'Hemodiálise'),       NULL),
('04.008.00', 'Torre de Vídeo Endoscopia',            'Diagnóstico',     'Olympus',     'VISERA Elite II',      'SN-OL-002', 'Olympus',              '2021-06-09', 2021, 41000.00, 'Compra', 'Ativo',          'Média',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Gastroenterologia'), NULL),
('07.003.00', 'Equipamento de Tração Cervical',       'Reabilitação',    'Chattanooga', 'Triton DTS',           'SN-CH-003', 'Chattanooga',          '2019-02-14', 2019, 4800.00,  'Compra', 'Ativo',          'Baixa',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'Fisioterapia'),      NULL),
('06.003.00', 'Lavadora Desinfetadora',                'Esterilização',  'Tuttnauer',   'Synergy Plus',         'SN-TT-003', 'Tuttnauer',            '2023-05-22', 2023, 12500.00, 'Compra', 'Ativo',          'Baixa',            (SELECT id_localizacao FROM localizacoes WHERE servico = 'CME'),               NULL)
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
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '501234567'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '502345678'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '504567890'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '503456789'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '505678901'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '06.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '506789012'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '503456789'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '501234567'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '05.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '510123456'), 'Distribuidor'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '05.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '510123456'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '05.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '510123456'), 'Assistência técnica'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '508901234'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.003.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '507890123'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.003.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '509012345'), 'Distribuidor'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.003.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '502345678'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.004.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '511234567'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.003.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '512345678'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.004.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '505678901'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.005.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '513456789'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '06.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '506789012'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.005.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '501234567'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.004.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '504567890'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.005.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '505678901'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.006.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '505678901'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.005.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '502345678'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.007.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '505678901'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.007.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '501234567'), 'Fabricante'),
((SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '06.003.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '506789012'), 'Fabricante')
ON DUPLICATE KEY UPDATE tipo = VALUES(tipo);

-- Acessórios — exemplo do próprio enunciado (secção 6): monitor multiparamétrico com
-- componentes periféricos, e mais alguns para ilustrar outros equipamentos.
-- acessorios não tem chave UNIQUE, por isso usa-se WHERE NOT EXISTS por linha.
INSERT INTO acessorios (id_equipamento, codigo, nome, id_fornecedor)
SELECT src.id_equipamento, src.codigo, src.nome, src.id_fornecedor
FROM (
    SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.001.00') AS id_equipamento, '01.001.01' AS codigo, 'Sensor de oximetria (SpO2)' AS nome, (SELECT id_fornecedor FROM fornecedores WHERE nif = '501234567') AS id_fornecedor
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.001.00'), '01.001.02', 'Cabo ECG', (SELECT id_fornecedor FROM fornecedores WHERE nif = '501234567')
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.001.00'), '01.001.03', 'Manguito de pressão arterial não invasiva (NIBP)', (SELECT id_fornecedor FROM fornecedores WHERE nif = '501234567')
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.001.00'), '01.001.04', 'Sensor de temperatura', (SELECT id_fornecedor FROM fornecedores WHERE nif = '501234567')
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.001.00'), '01.001.05', 'Bateria', NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.001.00'), '02.001.01', 'Circuito respiratório', (SELECT id_fornecedor FROM fornecedores WHERE nif = '502345678')
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.001.00'), '02.001.02', 'Filtro antibacteriano', NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.002.00'), '02.002.01', 'Pás de desfibrilhação', (SELECT id_fornecedor FROM fornecedores WHERE nif = '504567890')
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.002.00'), '02.002.02', 'Cabo ECG', (SELECT id_fornecedor FROM fornecedores WHERE nif = '504567890')
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.002.00'), '02.002.03', 'Bateria', NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.002.00'), '02.002.04', 'Impressora térmica', (SELECT id_fornecedor FROM fornecedores WHERE nif = '504567890')
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.001.00'), '03.001.01', 'Set de infusão', (SELECT id_fornecedor FROM fornecedores WHERE nif = '503456789')
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.001.00'), '03.001.02', 'Bateria', NULL
) src
WHERE NOT EXISTS (
    SELECT 1 FROM acessorios a WHERE a.id_equipamento = src.id_equipamento AND a.codigo = src.codigo
);

-- Documentação
INSERT INTO documentacao (tipo, nome, data, validade, caminho_ficheiro, id_equipamento, id_fornecedor) VALUES
('Manual de utilizador',        'Manual de Utilizador — IntelliVue MP5',          '2021-03-15', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_utilizador_intellivue_mp5.html',       (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '501234567')),
('Manual de serviço',           'Manual de Serviço — Savina 300',                 '2020-06-01', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_servico_savina300.html',              (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '502345678')),
('Manual de utilizador',        'Manual de Utilizador — Infusomat Space',         '2021-09-20', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_utilizador_infusomat_space.html',     (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '503456789')),
('Fatura/Guia de aquisição',    'Fatura de Aquisição — Ventilador Savina 300',    '2020-06-01', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/fatura_ventilador_savina300.html',           (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '502345678')),
('Fatura/Guia de aquisição',    'Fatura de Aquisição — Bomba Infusora Space',     '2021-09-20', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/fatura_bomba_infusora_space.html',           (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '503456789')),
('Declaração de conformidade',  'Declaração de Conformidade — Desfibrilhador',    '2022-01-10', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/declaracao_conformidade_desfibrilhador.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '504567890')),
('Relatório técnico',           'Relatório Técnico — Autoclave 2840 MK',           '2025-11-15', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/relatorio_tecnico_autoclave.html',            (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '06.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '506789012')),
('Certificado de calibração',   'Certificado de Calibração — IntelliVue MP5',     '2025-06-20', '2026-09-20', '/sibdas/1241035/invemed/uploads/documentacao/certificado_calibracao_intellivue_mp5.html',  (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '501234567')),
('Certificado de calibração',   'Certificado de Calibração — MAC 5500 HD',        '2026-01-10', '2027-01-10', '/sibdas/1241035/invemed/uploads/documentacao/certificado_calibracao_mac5500hd.html',      (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '505678901')),
('Contrato de manutenção',      'Contrato de Manutenção — Autoclave 2840 MK',     '2024-04-01', '2026-12-31', '/sibdas/1241035/invemed/uploads/documentacao/contrato_manutencao_autoclave.html',         (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '06.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '506789012')),
('Certificado de calibração',   'Certificado de Calibração — Desfibrilhador',     '2024-06-15', '2025-06-15', '/sibdas/1241035/invemed/uploads/documentacao/certificado_calibracao_desfibrilhador.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '504567890')),
('Contrato de manutenção',      'Contrato de Manutenção — MAC 5500 HD',           '2023-01-05', '2025-01-05', '/sibdas/1241035/invemed/uploads/documentacao/contrato_manutencao_mac5500hd.html',         (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.001.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '505678901')),
('Manual de utilizador',        'Manual de Utilizador — Cobas c311',              '2022-02-14', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_de_utilizador_analisador_bioqu_imico_146.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '05.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '510123456')),
('Certificado de calibração',   'Certificado de Calibração — Cobas c311',         '2025-09-01', '2026-09-01', '/sibdas/1241035/invemed/uploads/documentacao/certificado_de_calibrac_ao_analisador_bioqu_imico_147.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '05.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '510123456')),
('Manual de utilizador',        'Manual de Utilizador — Acuson P500',             '2020-09-01', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_de_utilizador_ec_ografo_148.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '508901234')),
('Contrato de manutenção',      'Contrato de Manutenção — Acuson P500',           '2024-09-01', '2026-09-01', '/sibdas/1241035/invemed/uploads/documentacao/contrato_de_manutenc_ao_ec_ografo_149.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '508901234')),
('Fatura/Guia de aquisição',    'Fatura de Aquisição — Oxímetro PM-60',            '2023-01-05', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/fatura_guia_de_aquisic_ao_ox_imetro_de_pulso_150.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.003.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '507890123')),
('Manual de utilizador',        'Manual de Utilizador — Unidade Eletroterapia',   '2019-08-20', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_de_utilizador_equipamento_de_eletroterapia_151.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '07.001.00'), NULL),
('Manual de utilizador',        'Manual de Utilizador — Babylog VN500',           '2022-04-12', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_de_utilizador_ventilador_pulmonar_neonatal_176.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.003.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '502345678')),
('Manual de serviço',           'Manual de Serviço — 4008S',                      '2021-07-08', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_de_servico_bomba_de_hemodi_alise_177.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.004.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '511234567')),
('Contrato de manutenção',      'Contrato de Manutenção — 4008S',                 '2024-07-08', '2026-07-08', '/sibdas/1241035/invemed/uploads/documentacao/contrato_de_manutenc_ao_bomba_de_hemodi_alise_178.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.004.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '511234567')),
('Certificado de calibração',   'Certificado de Calibração — CV-190',             '2025-12-01', '2026-12-01', '/sibdas/1241035/invemed/uploads/documentacao/certificado_de_calibrac_ao_endosc_opio_flex_ivel_179.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.003.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '512345678')),
('Manual de utilizador',        'Manual de Utilizador — Corometrics 250cx',       '2023-03-22', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_de_utilizador_monitor_fetal_ctg_180.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.004.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '505678901')),
('Fatura/Guia de aquisição',    'Fatura de Aquisição — MiniMed 780G',             '2023-06-15', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/fatura_guia_de_aquisic_ao_bomba_de_insulina_181.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.005.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '513456789')),
('Certificado de calibração',   'Certificado de Calibração — Autoclave 3870ELVC', '2025-08-19', '2026-08-19', '/sibdas/1241035/invemed/uploads/documentacao/certificado_de_calibrac_ao_autoclave_de_bancada_182.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '06.002.00'), (SELECT id_fornecedor FROM fornecedores WHERE nif = '506789012')),
('Manual de utilizador',        'Manual de Utilizador — Intelect Transport',      '2020-05-05', NULL,         '/sibdas/1241035/invemed/uploads/documentacao/manual_de_utilizador_equipamento_de_ultrassom_terap_eutico_183.html', (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '07.002.00'), NULL)
ON DUPLICATE KEY UPDATE
    tipo             = VALUES(tipo),
    data             = VALUES(data),
    validade         = VALUES(validade),
    caminho_ficheiro = VALUES(caminho_ficheiro),
    id_fornecedor    = VALUES(id_fornecedor);

-- Garantias e contratos
-- garantias_contratos não tem nenhuma chave UNIQUE (várias garantias/renovações são
-- permitidas para o mesmo equipamento) — usa-se WHERE NOT EXISTS (correlacionado por
-- id_equipamento + data_inicio_garantia) para evitar duplicar se este ficheiro for
-- importado mais do que uma vez.
INSERT INTO garantias_contratos (id_equipamento, entidade_responsavel, data_inicio_garantia, data_fim_garantia, tem_contrato, tipo_contrato, periodicidade, observacoes)
SELECT src.id_equipamento, src.entidade_responsavel, src.data_inicio_garantia, src.data_fim_garantia, src.tem_contrato, src.tipo_contrato, src.periodicidade, src.observacoes
FROM (
    SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.001.00') AS id_equipamento, 'Philips Healthcare' AS entidade_responsavel, '2021-03-15' AS data_inicio_garantia, '2024-03-15' AS data_fim_garantia, 'Sim' AS tem_contrato, 'Preventiva' AS tipo_contrato, 'Anual' AS periodicidade, 'Garantia expirada. Contrato de manutenção ativo separadamente.' AS observacoes
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.001.00'), 'Dräger Portugal', '2020-06-01', '2023-06-01', 'Sim', 'Completa', 'Anual', NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.002.00'), 'Zoll Medical', '2022-01-10', '2026-08-10', 'Não', NULL, NULL, 'Sem contrato de manutenção ativo.'
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.001.00'), 'B. Braun Portugal', '2021-09-20', '2025-09-20', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.001.00'), 'GE Healthcare', '2019-11-05', '2026-07-15', 'Sim', 'Preventiva', 'Anual', NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '06.001.00'), 'Tuttnauer Europe', '2024-04-01', '2027-04-01', 'Sim', 'Completa', 'Anual', 'Contrato inclui visita anual preventiva e resposta corretiva em 48h úteis.'
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.002.00'), 'B. Braun Portugal', '2022-07-03', '2025-07-03', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.002.00'), 'Philips Healthcare', '2020-02-28', '2027-02-28', 'Sim', 'Preventiva', 'Semestral', NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '05.001.00'), 'Roche Diagnostics Portugal', '2021-05-10', '2024-05-10', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '05.002.00'), 'Roche Diagnostics Portugal', '2022-02-14', '2026-02-14', 'Sim', 'Completa', 'Trimestral', 'Contrato inclui reagentes e assistência técnica.'
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.002.00'), 'Siemens Healthineers', '2020-09-01', '2026-09-01', 'Sim', 'Preventiva', 'Anual', NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '07.001.00'), 'Chattanooga', '2019-08-20', '2022-08-20', 'Não', NULL, NULL, 'Garantia expirada, sem contrato de manutenção ativo.'
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.003.00'), 'Mindray Medical Portugal', '2023-01-05', '2027-01-05', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.003.00'), 'Getinge Group', '2021-11-30', '2026-11-30', 'Sim', 'Completa', 'Semestral', NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.003.00'), 'Dräger Portugal', '2022-04-12', '2026-12-12', 'Sim', 'Preventiva', 'Anual', NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.004.00'), 'Fresenius Medical Care Portugal', '2021-07-08', '2025-07-08', 'Sim', 'Completa', 'Mensal', 'Contrato crítico — equipamento de suporte renal.'
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.003.00'), 'Olympus Portugal', '2020-12-01', '2024-12-01', 'Não', NULL, NULL, 'Garantia expirada.'
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.004.00'), 'GE Healthcare', '2023-07-15', '2026-07-15', 'Sim', 'Corretiva', 'Anual', 'Garantia a expirar nos próximos 30 dias.'
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.005.00'), 'Medtronic Portugal', '2023-06-15', '2026-06-15', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.004.00'), 'Vitalograph', '2019-10-10', '2022-10-10', 'Não', NULL, NULL, 'Garantia expirada, sem contrato de manutenção.'
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '06.002.00'), 'Tuttnauer Europe', '2022-08-19', '2026-08-19', 'Sim', 'Outro', 'Semestral', NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '07.002.00'), 'Chattanooga', '2020-05-05', '2023-05-05', 'Não', NULL, NULL, 'Garantia expirada.'
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '01.005.00'), 'Fornecedor / Assistência técnica', '2022-09-01', '2025-09-01', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.006.00'), 'Fornecedor / Assistência técnica', '2022-09-01', '2025-09-01', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.004.00'), 'Fornecedor / Assistência técnica', '2023-02-10', '2026-02-10', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.005.00'), 'Fornecedor / Assistência técnica', '2021-04-05', '2024-04-05', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.006.00'), 'Fornecedor / Assistência técnica', '2020-03-18', '2023-03-18', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '02.005.00'), 'Fornecedor / Assistência técnica', '2021-10-25', '2024-10-25', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.007.00'), 'Fornecedor / Assistência técnica', '2022-11-30', '2025-11-30', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.007.00'), 'Fornecedor / Assistência técnica', '2020-07-14', '2023-07-14', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '03.008.00'), 'Fornecedor / Assistência técnica', '2022-01-20', '2025-01-20', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '04.008.00'), 'Fornecedor / Assistência técnica', '2021-06-09', '2024-06-09', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '07.003.00'), 'Fornecedor / Assistência técnica', '2019-02-14', '2022-02-14', 'Não', NULL, NULL, NULL
    UNION ALL SELECT (SELECT id_equipamento FROM equipamentos WHERE codigo_interno = '06.003.00'), 'Fornecedor / Assistência técnica', '2023-05-22', '2026-05-22', 'Não', NULL, NULL, NULL
) src
WHERE NOT EXISTS (
    SELECT 1 FROM garantias_contratos g WHERE g.id_equipamento = src.id_equipamento AND g.data_inicio_garantia = src.data_inicio_garantia
);

-- Slides iniciais do carrossel
INSERT INTO slides_carousel (ordem, imagem_path, alt_text) VALUES
(1, '/sibdas/1241035/invemed/assets/img/Slide 1.png', 'Primeiro slide InveMed'),
(2, '/sibdas/1241035/invemed/assets/img/Slide 2.png', 'Segundo slide InveMed'),
(3, '/sibdas/1241035/invemed/assets/img/Slide 3.png', 'Terceiro slide InveMed')
ON DUPLICATE KEY UPDATE imagem_path = VALUES(imagem_path), alt_text = VALUES(alt_text);

-- Conteúdos iniciais da área pública
INSERT INTO conteudos_publicos (chave, conteudo) VALUES
('titulo_site',          'InveMed'),
('nav_inicio',           'Início'),
('nav_quemsomos',        'Sobre Nós'),
('nav_servicos',         'Serviços'),
('nav_contacto',         'Contacto'),
('link_area_restrita',   'Área Restrita'),
('inicio_titulo',        'Bem-Vindo à InveMed'),
('inicio_texto',         'A solução de eleição para organizar os seus equipamentos médicos.'),
('sobre_titulo',         'SOBRE NÓS'),
('sobre_card1_titulo',   'Anos de experiência em organização médica'),
('sobre_card1_texto',    'Na InveMed, acreditamos que a eficiência na saúde começa nos bastidores. Nascemos da necessidade de transformar a gestão de inventário num processo simples, inteligente e livre de falhas, permitindo que as equipas médicas se foquem no que realmente importa: salvar vidas.'),
('sobre_card2_titulo',   'Os pilares da Empresa'),
('sobre_card2_texto1',   'Tratamos o inventário dos vossos equipamentos com o mesmo nível de precisão e seriedade exigidos num bloco operatório.'),
('sobre_card2_texto2',   'Trabalhamos com total transparência e responsabilidade para garantir a segurança dos vossos dados e bens.'),
('sobre_card2_texto3',   'Procuramos sempre as melhores metodologias e soluções tecnológicas para simplicar a gestão logística hospitalar.'),
('sobre_card3_titulo',   'Para quem trabalhamos'),
('sobre_card3_sub1',     'Hospitais Públicos e Privados'),
('sobre_card3_texto1',   'Pretendemos fazer a gestão e catalogação de grandes volumes de ativos em múltiplos departamentos.'),
('sobre_card3_sub2',     'Clínicas Médicas e Dentárias'),
('sobre_card3_texto2',   'Tencionamos organizar e controlar os stocks de pequenos equipamentos para garantir a fluidez do dia a dia.'),
('servicos_titulo',      'SERVIÇOS'),
('servico1_icon',        'fa-solid fa-laptop-medical'),
('servico1_titulo',      'Gestão dos equipamentos médicos'),
('servico1_texto',       'Em vez de folhas de Excel dispersas, a InveMed oferece uma plataforma única para acompanhar o ciclo de vida dos dispositivos médicos.'),
('servico2_icon',        'fa-solid fa-file-shield'),
('servico2_titulo',      'Gestão de documentação'),
('servico2_texto',       'Um dos grandes problemas hospitalares é perder manuais ou certificados. Com a InveMed, é garantido que a instituição está sempre pronta para inspeções.'),
('servico3_icon',        'fa-solid fa-location-dot'),
('servico3_titulo',      'Mapeamento e Rastreabilidade Logística'),
('servico3_texto',       'Com a InveMed, consegue sempre saber onde está o equipamento para evitar perdas e otimizar o tempos das equipas médicas.'),
('servico4_icon',        'fa-solid fa-shield-heart'),
('servico4_titulo',      'Consultoria da Criticidade Clínica'),
('servico4_texto',       'A InveMed ajuda a classificar os equipamentos médicos de acordo com a sua criticidade e estado.'),
('contacto_titulo',      'CONTACTO'),
('contacto_texto',       'Entre em contacto connosco para organizarmos a sua unidade de saúde.'),
('contacto_label_nome',     'Nome:'),
('contacto_label_email',    'Email:'),
('contacto_label_mensagem', 'Mensagem:'),
('contacto_botao_enviar',   'Enviar mensagem'),
('footer_localizacao',   'Rua do ISEP, 4424-023 Porto, Portugal'),
('footer_horario1',      '2ª a Sábado: 8h-19h'),
('footer_horario2',      'Domingos e Feriados: Encerrado'),
('footer_email',         'Email: admin@invemed.pt'),
('footer_telefone',      'Telefone: +351 913 035 024')
ON DUPLICATE KEY UPDATE conteudo = VALUES(conteudo);

-- ============================================================
-- UTILIZADORES (um por cada perfil) — ver README.txt para as credenciais de acesso
-- Hashes gerados com password_hash(), PASSWORD_DEFAULT
-- ============================================================
INSERT INTO utilizadores (email, password_hash, nome, perfil, ativo) VALUES
('admin@invemed.pt', '$2y$10$GdlNLI0am37T9zkXzCiJjutwXqMGXkN9o1XQ8ZzEQocsXAMcWbjcm', 'Sílvia Magalhães', 'Administrador', 1),
('carlos.mendes@gmail.com', '$2y$10$OC9CjRqWV8mapF0nrRKsoemYFNlw8egWhLK2ed5xrbCx.m8abHJR.', 'Carlos Mendes', 'Técnico', 1),
('marta.oliveira@gmail.com', '$2y$10$CgzLvJ8ay9mh5OHa8S/zfuJCS056uiuhmIqIgIdbLea6.WX72CK.W', 'Marta Oliveira', 'Gestor de Logística', 1),
('ana.ferreira@gmail.com', '$2y$10$oLz2qagQUWPcv2eQDEJRWO.wZ/WII6/Ss.OvNHjzYW6fuPFjK1U2e', 'Ana Ferreira', 'Profissional de saúde', 1)
ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), nome = VALUES(nome), perfil = VALUES(perfil), ativo = VALUES(ativo);
