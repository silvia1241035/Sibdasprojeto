<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

$erros = [];
$erro_sistema = '';
$localizacoes = [];
$fornecedores = [];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $localizacoes = $ligacao->query("SELECT id_localizacao, edificio, servico, sala FROM localizacoes ORDER BY edificio, servico")->fetchAll(PDO::FETCH_OBJ);
    $fornecedores = $ligacao->query("SELECT id_fornecedor, nome FROM fornecedores ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}

$categoriasValidas      = ['Monitorização', 'Suporte de vida', 'Terapia', 'Diagnóstico', 'Laboratório', 'Esterilização', 'Reabilitação'];
$tiposEntradaValidos    = ['Compra', 'Doação', 'Aluguer', 'Empréstimo'];
$estadosValidos         = ['Ativo', 'Em manutenção', 'Inativo', 'Em calibração', 'Em quarentena', 'Abatido'];
$criticidadesValidas    = ['Baixa', 'Média', 'Alta', 'Suporte de vida'];
$tiposFornecedorValidos = ['Fabricante', 'Distribuidor', 'Assistência técnica', 'Consumíveis', 'Outro'];
$tiposDocumentoValidos  = ['Manual de utilizador', 'Manual de serviço', 'Certificado de calibração', 'Contrato de manutenção', 'Fatura/Guia de aquisição', 'Declaração de conformidade', 'Relatório técnico'];
$extensoesPermitidas    = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
$tiposContratoValidos   = ['Preventiva', 'Corretiva', 'Completa', 'Outro'];
$periodicidadesValidas  = ['Mensal', 'Trimestral', 'Semestral', 'Anual'];

// Linhas de fornecedores submetidas (para repor no formulário em caso de erro)
$fornecedoresSubmetidos = [];
if (!empty($_POST['fornecedor_equipamento']) && is_array($_POST['fornecedor_equipamento'])) {
    foreach ($_POST['fornecedor_equipamento'] as $i => $idF) {
        $fornecedoresSubmetidos[] = [
            'id_fornecedor' => $idF,
            'tipo'          => $_POST['tipo_relacao_fornecedor'][$i] ?? '',
        ];
    }
}

// Linhas de documentos submetidas (para repor no formulário em caso de erro)
$documentosSubmetidos = [];
if (!empty($_POST['tipo_documento_equip']) && is_array($_POST['tipo_documento_equip'])) {
    foreach ($_POST['tipo_documento_equip'] as $i => $tipo) {
        $documentosSubmetidos[] = [
            'tipo'     => $tipo,
            'nome'     => $_POST['nome_documento_equip'][$i] ?? '',
            'data'     => $_POST['data_documento_equip'][$i] ?? '',
            'validade' => $_POST['validade_documento_equip'][$i] ?? '',
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recolher dados — Detalhes do equipamento
    $codigo      = trim($_POST['codigo_equipamento'] ?? '');
    $designacao  = trim($_POST['designacao_equipamento'] ?? '');
    $categoria   = trim($_POST['categoria_equipamento'] ?? '');
    $marca       = trim($_POST['marca_equipamento'] ?? '');
    $modelo      = trim($_POST['modelo_equipamento'] ?? '');
    $nserie      = trim($_POST['nserie_equipamento'] ?? '');
    $fabricante  = trim($_POST['fabricante_equipamento'] ?? '');
    $dataAquis   = trim($_POST['dataaquisicao_equipamento'] ?? '');
    $anoFabrico  = trim($_POST['anofabrico_equipamento'] ?? '');
    $custo       = trim($_POST['custo_equipamento'] ?? '');
    $tipoEntrada = trim($_POST['tipoentrada_equipamento'] ?? '');
    $estado      = trim($_POST['estado_equipamento'] ?? '');
    $criticidade = trim($_POST['criticidade_equipamento'] ?? '');
    $idLoc       = trim($_POST['localizacao_equipamento'] ?? '');
    $obs         = trim($_POST['observacoes_equipamento'] ?? '');

    // Recolher dados — Garantia (opcional: só se a data de fim for preenchida)
    $entidadeGarantia = trim($_POST['entidade_garantia'] ?? '');
    $inicioGarantia   = trim($_POST['inicio_garantia'] ?? '');
    $fimGarantia      = trim($_POST['fim_garantia'] ?? '');
    $contratoGarantia = trim($_POST['contrato_garantia'] ?? '');
    $tipoContratoGar  = trim($_POST['tipocontrato_garantia'] ?? '');
    $periodicidadeGar = trim($_POST['periodicidade_garantia'] ?? '');
    $obsGarantia      = trim($_POST['observacoes_garantia'] ?? '');
    $temGarantia      = $fimGarantia !== '';

    // Fornecedores associados: filtrar linhas em branco e remover duplicados
    $relacoesFornecedor = [];
    $idsFornecedorVistos = [];
    foreach ($fornecedoresSubmetidos as $linha) {
        $idF = trim($linha['id_fornecedor']);
        $tipoRel = trim($linha['tipo']);
        if ($idF === '') {
            continue;
        }
        if (in_array((int)$idF, $idsFornecedorVistos, true)) {
            $erros[] = "O mesmo fornecedor não pode ser associado mais do que uma vez ao equipamento.";
            continue;
        }
        $idsFornecedorVistos[] = (int)$idF;
        $relacoesFornecedor[] = ['id_fornecedor' => (int)$idF, 'tipo' => $tipoRel !== '' ? $tipoRel : null];
    }

    // Documentos: filtrar linhas em branco, validar e preparar ficheiros
    $documentosValidos = [];
    foreach ($documentosSubmetidos as $idx => $doc) {
        $tipoDoc = trim($doc['tipo']);
        $nomeDoc = trim($doc['nome']);
        $dataDoc = trim($doc['data']);
        $validadeDoc = trim($doc['validade']);
        $temFicheiro = isset($_FILES['ficheiro_documento_equip']['error'][$idx])
            && $_FILES['ficheiro_documento_equip']['error'][$idx] !== UPLOAD_ERR_NO_FILE;

        if ($tipoDoc === '' && $nomeDoc === '' && $dataDoc === '' && !$temFicheiro) {
            continue; // linha em branco, ignorar
        }

        $numDoc = $idx + 1;
        if (empty($tipoDoc)) {
            $erros[] = "Documento {$numDoc}: o tipo é obrigatório.";
        } elseif (!in_array($tipoDoc, $tiposDocumentoValidos, true)) {
            $erros[] = "Documento {$numDoc}: tipo inválido.";
        }
        if (empty($nomeDoc)) {
            $erros[] = "Documento {$numDoc}: o nome é obrigatório.";
        }
        if (empty($dataDoc)) {
            $erros[] = "Documento {$numDoc}: a data é obrigatória.";
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataDoc)) {
            $erros[] = "Documento {$numDoc}: formato de data inválido.";
        }
        if (!empty($validadeDoc) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $validadeDoc)) {
            $erros[] = "Documento {$numDoc}: formato de validade inválido.";
        }

        $caminhoRelativo = null;
        if ($temFicheiro) {
            if ($_FILES['ficheiro_documento_equip']['error'][$idx] !== UPLOAD_ERR_OK) {
                $erros[] = "Documento {$numDoc}: erro ao carregar o ficheiro.";
            } else {
                $ext = strtolower(pathinfo($_FILES['ficheiro_documento_equip']['name'][$idx], PATHINFO_EXTENSION));
                if (!in_array($ext, $extensoesPermitidas, true)) {
                    $erros[] = "Documento {$numDoc}: tipo de ficheiro não permitido (use PDF, DOC, DOCX, JPG ou PNG).";
                } else {
                    $caminhoRelativo = ['tmp' => $_FILES['ficheiro_documento_equip']['tmp_name'][$idx], 'ext' => $ext];
                }
            }
        }

        $documentosValidos[] = [
            'tipo' => $tipoDoc, 'nome' => $nomeDoc, 'data' => $dataDoc,
            'validade' => $validadeDoc !== '' ? $validadeDoc : null, 'ficheiro' => $caminhoRelativo,
        ];
    }

    // 2. Validar dados — Detalhes
    if (empty($codigo)) {
        $erros[] = "O campo Código interno é obrigatório.";
    }

    if (empty($designacao)) {
        $erros[] = "O campo Designação é obrigatório.";
    } elseif (preg_match('/^\d+$/', $designacao)) {
        $erros[] = "O campo Designação não pode conter apenas números.";
    }

    if (!empty($categoria) && !in_array($categoria, $categoriasValidas, true)) {
        $erros[] = "Categoria inválida.";
    }

    if (!empty($dataAquis)) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataAquis)) {
            $erros[] = "Formato de Data de Aquisição inválido. Use AAAA-MM-DD.";
        } else {
            $partes = explode('-', $dataAquis);
            if (!checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])) {
                $erros[] = "Data de Aquisição inválida.";
            } elseif ($dataAquis > date('Y-m-d')) {
                $erros[] = "A Data de Aquisição não pode ser no futuro.";
            }
        }
    }

    if (!empty($anoFabrico)) {
        if (!preg_match('/^\d{4}$/', $anoFabrico) || (int)$anoFabrico < 1980 || (int)$anoFabrico > (int)date('Y')) {
            $erros[] = "Ano de Fabrico inválido (deve estar entre 1980 e " . date('Y') . ").";
        }
    }

    if (!empty($custo)) {
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $custo) || (float)$custo < 0) {
            $erros[] = "Custo de Aquisição inválido. Use um valor numérico positivo (ex: 1000.00).";
        }
    }

    if (!empty($tipoEntrada) && !in_array($tipoEntrada, $tiposEntradaValidos, true)) {
        $erros[] = "Tipo de Entrada inválido.";
    }

    if (!empty($estado) && !in_array($estado, $estadosValidos, true)) {
        $erros[] = "Estado inválido.";
    }

    if (!empty($criticidade) && !in_array($criticidade, $criticidadesValidas, true)) {
        $erros[] = "Criticidade inválida.";
    }

    // Validar — Localização (obrigatória)
    if (empty($idLoc)) {
        $erros[] = "A Localização é obrigatória: cada equipamento deve estar associado a uma localização atual.";
    } elseif (!in_array((int)$idLoc, array_column($localizacoes, 'id_localizacao'), true)) {
        $erros[] = "Localização selecionada não é válida.";
    }

    // Validar — Fornecedores
    foreach ($relacoesFornecedor as $rel) {
        if (!in_array($rel['id_fornecedor'], array_column($fornecedores, 'id_fornecedor'), true)) {
            $erros[] = "Um dos fornecedores selecionados não é válido.";
            break;
        }
        if ($rel['tipo'] !== null && !in_array($rel['tipo'], $tiposFornecedorValidos, true)) {
            $erros[] = "Tipo de fornecedor inválido.";
            break;
        }
    }

    // Validar — Garantia (só se ativada, isto é, se a data de fim foi preenchida)
    if ($temGarantia) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fimGarantia)) {
            $erros[] = "Garantia: formato de data de fim inválido.";
        } else {
            $partes = explode('-', $fimGarantia);
            if (!checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])) {
                $erros[] = "Garantia: data de fim inválida.";
            }
        }
        if (!empty($inicioGarantia)) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicioGarantia)) {
                $erros[] = "Garantia: formato de data de início inválido.";
            } else {
                $partes = explode('-', $inicioGarantia);
                if (!checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])) {
                    $erros[] = "Garantia: data de início inválida.";
                } elseif ($fimGarantia !== '' && $inicioGarantia > $fimGarantia) {
                    $erros[] = "Garantia: a data de início não pode ser depois da data de fim.";
                }
            }
        }
        if (!empty($contratoGarantia) && !in_array($contratoGarantia, ['Sim', 'Não'], true)) {
            $erros[] = "Garantia: valor de contrato de manutenção inválido.";
        }
        if (!empty($tipoContratoGar) && !in_array($tipoContratoGar, $tiposContratoValidos, true)) {
            $erros[] = "Garantia: tipo de contrato inválido.";
        }
        if (!empty($periodicidadeGar) && !in_array($periodicidadeGar, $periodicidadesValidas, true)) {
            $erros[] = "Garantia: periodicidade inválida.";
        }
    }

    // 3. Normalizar dados — Detalhes
    $designacao = ucwords(strtolower($designacao));
    $marca      = $marca !== '' ? ucwords(strtolower($marca)) : null;
    $modelo     = $modelo !== '' ? $modelo : null;
    $fabricante = $fabricante !== '' ? ucwords(strtolower($fabricante)) : null;
    $nserie     = $nserie !== '' ? strtoupper($nserie) : null;
    $dataAquis  = $dataAquis !== '' ? $dataAquis : null;
    $anoFabrico = $anoFabrico !== '' ? (int)$anoFabrico : null;
    $custo      = $custo !== '' ? (float)$custo : null;
    $tipoEntrada = $tipoEntrada !== '' ? $tipoEntrada : null;
    $estado     = $estado !== '' ? $estado : 'Ativo';
    $criticidade = $criticidade !== '' ? $criticidade : null;
    $categoria  = $categoria !== '' ? $categoria : null;
    $idLoc      = $idLoc !== '' ? (int)$idLoc : null;
    $obs        = $obs !== '' ? $obs : null;

    // Normalizar — Garantia
    $entidadeGarantia = $entidadeGarantia !== '' ? ucwords(strtolower($entidadeGarantia)) : null;
    $inicioGarantia   = $inicioGarantia !== '' ? $inicioGarantia : null;
    $contratoGarantia = $contratoGarantia !== '' ? $contratoGarantia : null;
    $tipoContratoGar  = $tipoContratoGar !== '' ? $tipoContratoGar : null;
    $periodicidadeGar = $periodicidadeGar !== '' ? $periodicidadeGar : null;
    $obsGarantia      = $obsGarantia !== '' ? $obsGarantia : null;

    // Regra de negócio: número de série não pode repetir-se para o mesmo fabricante + modelo
    if (empty($erros) && empty($erro_sistema) && !empty($nserie) && !empty($fabricante) && !empty($modelo)) {
        try {
            $stmtDup = $ligacao->prepare("SELECT COUNT(*) FROM equipamentos WHERE fabricante = :fab AND modelo = :mod AND numero_serie = :ns");
            $stmtDup->execute([':fab' => $fabricante, ':mod' => $modelo, ':ns' => $nserie]);
            if ($stmtDup->fetchColumn() > 0) {
                $erros[] = "Já existe um equipamento do mesmo fabricante e modelo com este número de série.";
            }
        } catch (PDOException $err) {
            $erro_sistema = "Erro ao validar o número de série: " . $err->getMessage();
        }
    }

    // 4. Guardar na base de dados
    if (empty($erros) && empty($erro_sistema)) {
        try {
            $ligacao->beginTransaction();

            $sql = "INSERT INTO equipamentos (
                        codigo_interno, designacao, categoria, marca, modelo, numero_serie, fabricante,
                        data_aquisicao, ano_fabrico, custo_aquisicao, tipo_entrada, estado, criticidade,
                        id_localizacao, observacoes
                    ) VALUES (
                        :codigo, :designacao, :categoria, :marca, :modelo, :nserie, :fabricante,
                        :dataaquis, :anofabrico, :custo, :tipoentrada, :estado, :criticidade,
                        :idloc, :obs
                    )";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':codigo'      => $codigo,
                ':designacao'  => $designacao,
                ':categoria'   => $categoria,
                ':marca'       => $marca,
                ':modelo'      => $modelo,
                ':nserie'      => $nserie,
                ':fabricante'  => $fabricante,
                ':dataaquis'   => $dataAquis,
                ':anofabrico'  => $anoFabrico,
                ':custo'       => $custo,
                ':tipoentrada' => $tipoEntrada,
                ':estado'      => $estado,
                ':criticidade' => $criticidade,
                ':idloc'       => $idLoc,
                ':obs'         => $obs,
            ]);
            $idEquipamento = (int)$ligacao->lastInsertId();

            // Fornecedores associados
            if (!empty($relacoesFornecedor)) {
                $stmtRel = $ligacao->prepare("INSERT INTO equipamento_fornecedor (id_equipamento, id_fornecedor, tipo) VALUES (:idequip, :idforn, :tipo)");
                foreach ($relacoesFornecedor as $rel) {
                    $stmtRel->execute([
                        ':idequip' => $idEquipamento,
                        ':idforn'  => $rel['id_fornecedor'],
                        ':tipo'    => $rel['tipo'],
                    ]);
                }
            }

            // Documentos associados (com upload de ficheiro, se fornecido)
            if (!empty($documentosValidos)) {
                $stmtDoc = $ligacao->prepare("INSERT INTO documentacao (tipo, nome, data, validade, caminho_ficheiro, id_equipamento) VALUES (:tipo, :nome, :data, :validade, :ficheiro, :idequip)");
                foreach ($documentosValidos as $doc) {
                    $caminhoFicheiro = null;
                    if ($doc['ficheiro'] !== null) {
                        $nomeFicheiro = uniqid('doc_') . '.' . $doc['ficheiro']['ext'];
                        $destino = __DIR__ . '/../../uploads/documentacao/' . $nomeFicheiro;
                        if (move_uploaded_file($doc['ficheiro']['tmp'], $destino)) {
                            $caminhoFicheiro = BASE_URL . '/uploads/documentacao/' . $nomeFicheiro;
                        }
                    }
                    $stmtDoc->execute([
                        ':tipo'     => $doc['tipo'],
                        ':nome'     => $doc['nome'],
                        ':data'     => $doc['data'],
                        ':validade' => $doc['validade'],
                        ':ficheiro' => $caminhoFicheiro,
                        ':idequip'  => $idEquipamento,
                    ]);
                }
            }

            // Garantia / contrato (opcional)
            if ($temGarantia) {
                $stmtGar = $ligacao->prepare("INSERT INTO garantias_contratos (
                                id_equipamento, entidade_responsavel, data_inicio_garantia, data_fim_garantia,
                                tem_contrato, tipo_contrato, periodicidade, observacoes
                            ) VALUES (
                                :idequip, :entidade, :inicio, :fim, :contrato, :tipocontrato, :periodicidade, :obs
                            )");
                $stmtGar->execute([
                    ':idequip'      => $idEquipamento,
                    ':entidade'     => $entidadeGarantia,
                    ':inicio'       => $inicioGarantia,
                    ':fim'          => $fimGarantia,
                    ':contrato'     => $contratoGarantia,
                    ':tipocontrato' => $tipoContratoGar,
                    ':periodicidade' => $periodicidadeGar,
                    ':obs'          => $obsGarantia,
                ]);
            }

            $ligacao->commit();
            header('Location: listar.php');
            exit;
        } catch (PDOException $err) {
            $ligacao->rollBack();
            if ($err->getCode() == 23000) {
                $erro_sistema = "Já existe um registo com estes dados — verifique se o código interno do equipamento já está em uso ou se há nomes de documentos repetidos.";
            } else {
                $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
            }
        }
    }
}
$ligacao = null;

// Garante pelo menos uma linha de fornecedor e de documento para o formulário
if (empty($fornecedoresSubmetidos)) {
    $fornecedoresSubmetidos = [['id_fornecedor' => '', 'tipo' => '']];
}
if (empty($documentosSubmetidos)) {
    $documentosSubmetidos = [['tipo' => '', 'nome' => '', 'data' => '', 'validade' => '']];
}
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

<?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">

        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1200px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-square-plus fa-1x mb-3"></i> Adicionar novo equipamento</strong></h2>
                    <hr>

                    <!-- Área de erros de validação / sistema (PHP) -->
                    <?php if (!empty($erros)) : ?>
                    <div class="alert alert-danger mb-4">
                        <strong>Foram encontrados os seguintes erros:</strong>
                        <ul class="mb-0">
                            <?php foreach ($erros as $erro) : ?>
                                <li><?= htmlspecialchars($erro) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($erro_sistema)) : ?>
                    <div class="alert alert-danger mb-4">
                        <strong>Erro:</strong> <?= htmlspecialchars($erro_sistema) ?>
                    </div>
                    <?php endif; ?>

                    <form action="#" method="post" enctype="multipart/form-data" novalidate id="formEquipamento">

                        <!-- Área de erros — validação no browser -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao inserir o equipamento. Por favor, tente novamente.
                        </div>

                        <!-- TABS -->
                        <ul class="nav nav-tabs" id="inserirEquipTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-detalhes-btn" data-bs-toggle="tab" data-bs-target="#tab-detalhes" type="button" role="tab">
                                    <i class="fa-solid fa-circle-info me-1"></i> Detalhes
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-fornecedor-btn" data-bs-toggle="tab" data-bs-target="#tab-fornecedor" type="button" role="tab">
                                    <i class="fa-solid fa-truck me-1"></i> Fornecedor
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-localizacao-btn" data-bs-toggle="tab" data-bs-target="#tab-localizacao" type="button" role="tab">
                                    <i class="fa-solid fa-map-location-dot me-1"></i> Localização
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-documentacao-btn" data-bs-toggle="tab" data-bs-target="#tab-documentacao" type="button" role="tab">
                                    <i class="fa-solid fa-file-medical me-1"></i> Documentação
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-garantia-btn" data-bs-toggle="tab" data-bs-target="#tab-garantia" type="button" role="tab">
                                    <i class="fa-solid fa-file-contract me-1"></i> Garantia e Contrato
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content border border-top-0 rounded-bottom p-4 bg-white mb-3" id="inserirEquipTabsContent">

                            <!-- ABA 1: DETALHES -->
                            <div class="tab-pane fade show active" id="tab-detalhes" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="codigo" class="form-label">Código interno<span class="text-danger" title="Campo obrigatório">*</span></label>
                                        <input type="text" class="form-control" id="codigo" name="codigo_equipamento" required placeholder="Ex: 04.002.00" value="<?= htmlspecialchars($_POST['codigo_equipamento'] ?? '') ?>">
                                        <div class="invalid-feedback">Por favor, insira o código interno.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="designacao" class="form-label">Designação<span class="text-danger" title="Campo obrigatório">*</span></label>
                                        <input type="text" class="form-control" id="designacao" name="designacao_equipamento" required placeholder="Ex: Monitor Multiparamétrico" value="<?= htmlspecialchars($_POST['designacao_equipamento'] ?? '') ?>">
                                        <div class="invalid-feedback">Por favor, insira a designação.</div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="categoria" class="form-label">Categoria</label>
                                        <select class="form-select" id="categoria" name="categoria_equipamento">
                                            <option value="">Selecione...</option>
                                            <?php foreach ($categoriasValidas as $cat) : ?>
                                                <option value="<?= htmlspecialchars($cat) ?>" <?= (($_POST['categoria_equipamento'] ?? '') === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="marca" class="form-label">Marca</label>
                                        <input type="text" class="form-control" id="marca" name="marca_equipamento" placeholder="Ex: Philips" value="<?= htmlspecialchars($_POST['marca_equipamento'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="modelo" class="form-label">Modelo</label>
                                        <input type="text" class="form-control" id="modelo" name="modelo_equipamento" placeholder="Ex: IntelliVue MP5" value="<?= htmlspecialchars($_POST['modelo_equipamento'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="nserie" class="form-label">Número de Série</label>
                                        <input type="text" class="form-control" id="nserie" name="nserie_equipamento" placeholder="Ex: MP5-2022-45873" value="<?= htmlspecialchars($_POST['nserie_equipamento'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="fabricante" class="form-label">Fabricante</label>
                                        <input type="text" class="form-control" id="fabricante" name="fabricante_equipamento" placeholder="Ex: Philips" value="<?= htmlspecialchars($_POST['fabricante_equipamento'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="dataaquisicao" class="form-label">Data de Aquisição</label>
                                        <input type="date" class="form-control" id="dataaquisicao" name="dataaquisicao_equipamento" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['dataaquisicao_equipamento'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="anofabrico" class="form-label">Ano de Fabrico</label>
                                        <input type="number" class="form-control" id="anofabrico" name="anofabrico_equipamento" placeholder="Ex: 2022" min="1980" max="<?= date('Y') ?>" value="<?= htmlspecialchars($_POST['anofabrico_equipamento'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="custo" class="form-label">Custo de Aquisição (€)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="custo" name="custo_equipamento" placeholder="Ex: 1000.00" value="<?= htmlspecialchars($_POST['custo_equipamento'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="tipoentrada" class="form-label">Tipo de Entrada</label>
                                        <select class="form-select" id="tipoentrada" name="tipoentrada_equipamento">
                                            <option value="">Selecione...</option>
                                            <?php foreach ($tiposEntradaValidos as $te) : ?>
                                                <option value="<?= htmlspecialchars($te) ?>" <?= (($_POST['tipoentrada_equipamento'] ?? '') === $te) ? 'selected' : '' ?>><?= htmlspecialchars($te) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="estado" class="form-label">Estado atual</label>
                                        <select class="form-select" id="estado" name="estado_equipamento">
                                            <?php foreach ($estadosValidos as $est) : ?>
                                                <option value="<?= htmlspecialchars($est) ?>" <?= ((($_POST['estado_equipamento'] ?? 'Ativo')) === $est) ? 'selected' : '' ?>><?= htmlspecialchars($est) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="criticidade" class="form-label">Criticidade</label>
                                        <select class="form-select" id="criticidade" name="criticidade_equipamento">
                                            <option value="">Selecione...</option>
                                            <?php foreach ($criticidadesValidas as $crit) : ?>
                                                <option value="<?= htmlspecialchars($crit) ?>" <?= (($_POST['criticidade_equipamento'] ?? '') === $crit) ? 'selected' : '' ?>><?= htmlspecialchars($crit) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="observacoes" class="form-label">Observações</label>
                                        <textarea class="form-control" id="observacoes" name="observacoes_equipamento" rows="3" placeholder="Notas adicionais sobre o equipamento..."><?= htmlspecialchars($_POST['observacoes_equipamento'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- ABA 2: FORNECEDOR -->
                            <div class="tab-pane fade" id="tab-fornecedor" role="tabpanel">
                                <p class="text-muted">Um equipamento pode estar associado a vários fornecedores (fabricante, distribuidor, assistência técnica, consumíveis, etc.).</p>
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAdicionarFornecedor">
                                        <i class="fa-solid fa-plus me-1"></i> Adicionar fornecedor
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="tabelaFornecedoresEquip">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fornecedor</th>
                                                <th>Tipo de fornecedor</th>
                                                <th class="text-center" style="width:50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="linhasFornecedoresEquip">
                                            <?php foreach ($fornecedoresSubmetidos as $linha) : ?>
                                            <tr class="linha-fornecedor-equip">
                                                <td>
                                                    <select class="form-select form-select-sm" name="fornecedor_equipamento[]">
                                                        <option value="">Selecione...</option>
                                                        <?php foreach ($fornecedores as $forn) : ?>
                                                            <option value="<?= $forn->id_fornecedor ?>" <?= ((int)($linha['id_fornecedor'] ?? 0) === (int)$forn->id_fornecedor) ? 'selected' : '' ?>><?= htmlspecialchars($forn->nome) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" name="tipo_relacao_fornecedor[]">
                                                        <option value="">Selecione...</option>
                                                        <?php foreach ($tiposFornecedorValidos as $tr) : ?>
                                                            <option value="<?= htmlspecialchars($tr) ?>" <?= (($linha['tipo'] ?? '') === $tr) ? 'selected' : '' ?>><?= htmlspecialchars($tr) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remover-fornecedor-equip" title="Remover linha" <?= count($fornecedoresSubmetidos) === 1 ? 'disabled' : '' ?>>
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="../fornecedores/inserir.php" target="_blank" class="text-decoration-none">
                                    <i class="fa-solid fa-up-right-from-square me-1"></i> O fornecedor não existe? Crie-o numa nova aba.
                                </a>
                            </div>

                            <!-- ABA 3: LOCALIZAÇÃO -->
                            <div class="tab-pane fade" id="tab-localizacao" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="localizacao" class="form-label">Localização<span class="text-danger" title="Campo obrigatório">*</span></label>
                                        <select class="form-select" id="localizacao" name="localizacao_equipamento" required>
                                            <option value="">Selecione...</option>
                                            <?php foreach ($localizacoes as $loc) : ?>
                                                <option value="<?= $loc->id_localizacao ?>" <?= ((int)($_POST['localizacao_equipamento'] ?? 0) === (int)$loc->id_localizacao) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($loc->edificio) ?> - <?= htmlspecialchars($loc->servico) ?><?= $loc->sala ? ' (' . htmlspecialchars($loc->sala) . ')' : '' ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Por favor, selecione a localização.</div>
                                        <div class="form-text">Cada equipamento deve estar associado a uma localização atual.</div>
                                    </div>
                                </div>
                                <a href="../localizacao/inserir.php" target="_blank" class="text-decoration-none" style="color:#0077a8;">
                                    <i class="fa-solid fa-up-right-from-square me-1"></i> A localização não existe? Crie-a numa nova aba.
                                </a>
                            </div>

                            <!-- ABA 4: DOCUMENTAÇÃO -->
                            <div class="tab-pane fade" id="tab-documentacao" role="tabpanel">
                                <p class="text-muted">Documentos a associar a este equipamento (opcional).</p>
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAdicionarDocumentoEquip">
                                        <i class="fa-solid fa-plus me-1"></i> Adicionar documento
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="tabelaDocumentosEquip">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="min-width:180px;">Tipo</th>
                                                <th style="min-width:180px;">Nome</th>
                                                <th style="min-width:140px;">Data</th>
                                                <th style="min-width:140px;">Validade</th>
                                                <th style="min-width:180px;">Ficheiro</th>
                                                <th class="text-center" style="width:50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="linhasDocumentosEquip">
                                            <?php foreach ($documentosSubmetidos as $doc) : ?>
                                            <tr class="linha-documento-equip">
                                                <td>
                                                    <select class="form-select form-select-sm" name="tipo_documento_equip[]">
                                                        <option value="">Selecione...</option>
                                                        <?php foreach ($tiposDocumentoValidos as $td) : ?>
                                                            <option value="<?= htmlspecialchars($td) ?>" <?= (($doc['tipo'] ?? '') === $td) ? 'selected' : '' ?>><?= htmlspecialchars($td) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="nome_documento_equip[]" placeholder="Ex: Manual de utilizador" value="<?= htmlspecialchars($doc['nome'] ?? '') ?>">
                                                </td>
                                                <td>
                                                    <input type="date" class="form-control form-control-sm" name="data_documento_equip[]" value="<?= htmlspecialchars($doc['data'] ?? '') ?>">
                                                </td>
                                                <td>
                                                    <input type="date" class="form-control form-control-sm" name="validade_documento_equip[]" value="<?= htmlspecialchars($doc['validade'] ?? '') ?>">
                                                </td>
                                                <td>
                                                    <input type="file" class="form-control form-control-sm" name="ficheiro_documento_equip[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remover-documento-equip" title="Remover linha" <?= count($documentosSubmetidos) === 1 ? 'disabled' : '' ?>>
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- ABA 5: GARANTIA E CONTRATO -->
                            <div class="tab-pane fade" id="tab-garantia" role="tabpanel">
                                <p class="text-muted">Informação de garantia/contrato (opcional). Só é guardada se preencheres a data de fim da garantia.</p>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="entidade_garantia" class="form-label">Entidade responsável</label>
                                        <input type="text" class="form-control campo-garantia-equip" id="entidade_garantia" name="entidade_garantia" placeholder="Ex: Philips Healthcare" value="<?= htmlspecialchars($_POST['entidade_garantia'] ?? '') ?>" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="inicio_garantia" class="form-label">Data de início</label>
                                        <input type="date" class="form-control campo-garantia-equip" id="inicio_garantia" name="inicio_garantia" value="<?= htmlspecialchars($_POST['inicio_garantia'] ?? '') ?>" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="fim_garantia_equip" class="form-label">Data de fim</label>
                                        <input type="date" class="form-control" id="fim_garantia_equip" name="fim_garantia" value="<?= htmlspecialchars($_POST['fim_garantia'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="contrato_garantia" class="form-label">Contrato de manutenção</label>
                                        <select class="form-select campo-garantia-equip" id="contrato_garantia" name="contrato_garantia" disabled>
                                            <option value="">Selecione...</option>
                                            <option value="Sim" <?= (($_POST['contrato_garantia'] ?? '') === 'Sim') ? 'selected' : '' ?>>Sim</option>
                                            <option value="Não" <?= (($_POST['contrato_garantia'] ?? '') === 'Não') ? 'selected' : '' ?>>Não</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="tipocontrato_garantia" class="form-label">Tipo de contrato</label>
                                        <select class="form-select campo-garantia-equip" id="tipocontrato_garantia" name="tipocontrato_garantia" disabled>
                                            <option value="">Selecione...</option>
                                            <?php foreach ($tiposContratoValidos as $tc) : ?>
                                                <option value="<?= htmlspecialchars($tc) ?>" <?= (($_POST['tipocontrato_garantia'] ?? '') === $tc) ? 'selected' : '' ?>><?= htmlspecialchars($tc) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="periodicidade_garantia" class="form-label">Periodicidade</label>
                                        <select class="form-select campo-garantia-equip" id="periodicidade_garantia" name="periodicidade_garantia" disabled>
                                            <option value="">Selecione...</option>
                                            <?php foreach ($periodicidadesValidas as $per) : ?>
                                                <option value="<?= htmlspecialchars($per) ?>" <?= (($_POST['periodicidade_garantia'] ?? '') === $per) ? 'selected' : '' ?>><?= htmlspecialchars($per) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="observacoes_garantia" class="form-label">Observações</label>
                                        <textarea class="form-control campo-garantia-equip" id="observacoes_garantia" name="observacoes_garantia" rows="3" disabled><?= htmlspecialchars($_POST['observacoes_garantia'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between align-items-center gap-2 pt-3 border-top">
                            <small class="text-muted">
                                <span class="text-danger">*</span> campos obrigatórios
                            </small>
                            <div class="d-flex gap-2">
                                <a href="listar.php" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-xmark me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnGuardar" disabled>
                                    <i class="fa-regular fa-floppy-disk me-1"></i> Guardar
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </main>


    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>