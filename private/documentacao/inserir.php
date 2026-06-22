<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Técnico']);

$erros = [];
$erro_sistema = '';
$equipamentos = [];
$fornecedores = [];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $equipamentos = $ligacao->query("SELECT id_equipamento, codigo_interno, designacao FROM equipamentos WHERE estado != 'Abatido' ORDER BY designacao")->fetchAll(PDO::FETCH_OBJ);
    $fornecedores = $ligacao->query("SELECT id_fornecedor, nome FROM fornecedores WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}

$tiposDocumentoValidos = ['Manual de utilizador', 'Manual de serviço', 'Certificado de calibração', 'Contrato de manutenção', 'Fatura/Guia de aquisição', 'Declaração de conformidade', 'Relatório técnico'];
$tiposComValidadeObrigatoria = ['Certificado de calibração', 'Contrato de manutenção'];
$extensoesPermitidas   = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

// Linhas de documentos submetidas (para repor no formulário em caso de erro)
$documentosSubmetidos = [];
if (!empty($_POST['tipo_documento']) && is_array($_POST['tipo_documento'])) {
    foreach ($_POST['tipo_documento'] as $i => $tipo) {
        $documentosSubmetidos[] = [
            'tipo'     => $tipo,
            'nome'     => $_POST['nome_documento'][$i] ?? '',
            'data'     => $_POST['data_documento'][$i] ?? '',
            'validade' => $_POST['validade_documento'][$i] ?? '',
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recolher dados
    $idEquipamento = trim($_POST['equipamento_documento'] ?? '');
    $idFornecedor  = trim($_POST['fornecedor_documento'] ?? '');

    // 2. Validar dados — Equipamento / Fornecedor
    $idsEquipamentoValidos = array_map(fn($e) => (string)$e->id_equipamento, $equipamentos);
    $idsFornecedorValidos  = array_map(fn($f) => (string)$f->id_fornecedor, $fornecedores);

    if (empty($idEquipamento)) {
        $erros[] = "O campo Equipamento associado é obrigatório.";
    } elseif (!in_array($idEquipamento, $idsEquipamentoValidos, true)) {
        $erros[] = "O equipamento selecionado é inválido.";
    }

    if (!empty($idFornecedor) && !in_array($idFornecedor, $idsFornecedorValidos, true)) {
        $erros[] = "O fornecedor selecionado é inválido.";
    }

    // Documentos: filtrar linhas em branco, validar conteúdo e preparar ficheiros
    $documentosValidos = [];
    $nomesVistos = [];
    foreach ($documentosSubmetidos as $idx => $doc) {
        $tipoDoc     = trim($doc['tipo']);
        $nomeDoc     = trim($doc['nome']);
        $dataDoc     = trim($doc['data']);
        $validadeDoc = trim($doc['validade']);
        $temFicheiro = isset($_FILES['ficheiro_documento']['error'][$idx])
            && $_FILES['ficheiro_documento']['error'][$idx] !== UPLOAD_ERR_NO_FILE;

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
        } elseif (in_array(strtolower($nomeDoc), $nomesVistos, true)) {
            $erros[] = "Documento {$numDoc}: já existe um documento com este nome nesta submissão.";
        } else {
            $nomesVistos[] = strtolower($nomeDoc);
        }

        if (empty($dataDoc)) {
            $erros[] = "Documento {$numDoc}: a data é obrigatória.";
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataDoc)) {
            $erros[] = "Documento {$numDoc}: formato de data inválido.";
        } else {
            [$ano, $mes, $dia] = explode('-', $dataDoc);
            if (!checkdate((int)$mes, (int)$dia, (int)$ano)) {
                $erros[] = "Documento {$numDoc}: data inválida.";
            } elseif ($dataDoc > date('Y-m-d')) {
                $erros[] = "Documento {$numDoc}: a data não pode ser futura.";
            }
        }

        if (empty($validadeDoc)) {
            if (in_array($tipoDoc, $tiposComValidadeObrigatoria, true)) {
                $erros[] = "Documento {$numDoc}: a validade é obrigatória para o tipo \"{$tipoDoc}\".";
            }
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $validadeDoc)) {
            $erros[] = "Documento {$numDoc}: formato de validade inválido.";
        } else {
            [$anoV, $mesV, $diaV] = explode('-', $validadeDoc);
            if (!checkdate((int)$mesV, (int)$diaV, (int)$anoV)) {
                $erros[] = "Documento {$numDoc}: validade inválida.";
            }
        }

        $ficheiroInfo = null;
        if (!$temFicheiro) {
            $erros[] = "Documento {$numDoc}: o ficheiro é obrigatório.";
        } elseif ($_FILES['ficheiro_documento']['error'][$idx] !== UPLOAD_ERR_OK) {
            $erros[] = "Documento {$numDoc}: erro ao carregar o ficheiro.";
        } else {
            $ext = strtolower(pathinfo($_FILES['ficheiro_documento']['name'][$idx], PATHINFO_EXTENSION));
            if (!in_array($ext, $extensoesPermitidas, true)) {
                $erros[] = "Documento {$numDoc}: tipo de ficheiro não permitido (use PDF, DOC, DOCX, JPG ou PNG).";
            } else {
                $ficheiroInfo = ['tmp' => $_FILES['ficheiro_documento']['tmp_name'][$idx], 'ext' => $ext];
            }
        }

        // 3. Normalizar dados — o nome não é forçado a Maiúsculas/minúsculas para não corromper
        // modelos e siglas (ex: "IntelliVue MP5", "ECG"); só se remove o espaço sobrante.
        $documentosValidos[] = [
            'tipo'     => $tipoDoc,
            'nome'     => $nomeDoc,
            'data'     => $dataDoc,
            'validade' => $validadeDoc !== '' ? $validadeDoc : null,
            'ficheiro' => $ficheiroInfo,
        ];
    }

    if (empty($documentosValidos) && empty($erros)) {
        $erros[] = "Adicione pelo menos um documento.";
    }

    // 4. Guardar na base de dados
    if (empty($erros) && empty($erro_sistema)) {
        try {
            $ligacao->beginTransaction();
            $stmtDoc = $ligacao->prepare("INSERT INTO documentacao (tipo, nome, data, validade, caminho_ficheiro, id_equipamento, id_fornecedor) VALUES (:tipo, :nome, :data, :validade, :ficheiro, :idequip, :idforn)");
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
                    ':idforn'   => $idFornecedor !== '' ? $idFornecedor : null,
                ]);
            }
            $ligacao->commit();
            $nomesDocumentos = implode(', ', array_column($documentosValidos, 'nome'));
            registar_log('inserir', "Documento(s) criado(s): {$nomesDocumentos}.", $_SESSION['id_utilizador'] ?? null);
            header('Location: listar.php');
            exit;
        } catch (PDOException $err) {
            $ligacao->rollBack();
            if ($err->getCode() == 23000) {
                $erro_sistema = "Já existe um documento com este nome associado a este equipamento.";
            } else {
                $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
            }
            $descricaoErro = $err->getCode() == 23000
                ? "Tentativa de inserir documento com nome já existente para este equipamento."
                : "Erro ao gravar o documento na base de dados.";
            registar_log('erro', $descricaoErro, $_SESSION['id_utilizador'] ?? null);
        }
    }
}

$linhasParaRenderizar = !empty($documentosSubmetidos) ? $documentosSubmetidos : [['tipo' => '', 'nome' => '', 'data' => '', 'validade' => '']];

$ligacao = null;
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1100px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-square-plus fa-1x mb-3"></i> Adicionar documentos</strong></h2>
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

                    <form action="#" method="post" enctype="multipart/form-data" novalidate id="formDocumento">

                        <!-- Área de erros — validação no browser -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao inserir os documentos. Por favor, tente novamente.
                        </div>

                        <!-- Equipamento e fornecedor (partilhados por todos os documentos) -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="equipamento" class="form-label">Equipamento associado<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <select class="form-select" id="equipamento" name="equipamento_documento" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($equipamentos as $eq) : ?>
                                        <option value="<?= $eq->id_equipamento ?>" <?= ((string)($_POST['equipamento_documento'] ?? '') === (string)$eq->id_equipamento) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($eq->codigo_interno . ' - ' . $eq->designacao) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o equipamento associado.</div>
                                <?php if (empty($equipamentos)) : ?>
                                    <div class="form-text">Não existem equipamentos registados. <a href="../equipamentos/inserir.php" target="_blank" style="color:#0077a8;">Adicionar equipamento</a></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="fornecedor" class="form-label">Fornecedor associado</label>
                                <select class="form-select" id="fornecedor" name="fornecedor_documento">
                                    <option value="">Nenhum / Selecione...</option>
                                    <?php foreach ($fornecedores as $forn) : ?>
                                        <option value="<?= $forn->id_fornecedor ?>" <?= ((string)($_POST['fornecedor_documento'] ?? '') === (string)$forn->id_fornecedor) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($forn->nome) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <hr class="mb-4">

                        <!-- Cabeçalho da secção de documentos -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fa-solid fa-file-lines me-2"></i>Documentos</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAdicionarLinha">
                                <i class="fa-solid fa-plus me-1"></i> Adicionar documento
                            </button>
                        </div>

                        <!-- Tabela de linhas de documentos -->
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle" id="tabelaDocumentos">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width:180px;">Tipo<span class="text-danger">*</span></th>
                                        <th style="min-width:200px;">Nome<span class="text-danger">*</span></th>
                                        <th style="min-width:150px;">Data<span class="text-danger">*</span></th>
                                        <th style="min-width:150px;">Validade</th>
                                        <th style="min-width:200px;">Ficheiro<span class="text-danger">*</span></th>
                                        <th class="text-center" style="width:50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="linhasDocumentos">
                                    <?php foreach ($linhasParaRenderizar as $i => $linha) : ?>
                                    <tr class="linha-documento">
                                        <td>
                                            <select class="form-select form-select-sm" name="tipo_documento[]" required>
                                                <option value="">Selecione...</option>
                                                <?php foreach ($tiposDocumentoValidos as $tipoOpcao) : ?>
                                                    <option value="<?= htmlspecialchars($tipoOpcao) ?>" data-requer-validade="<?= in_array($tipoOpcao, $tiposComValidadeObrigatoria, true) ? '1' : '0' ?>" <?= ($linha['tipo'] === $tipoOpcao) ? 'selected' : '' ?>><?= htmlspecialchars($tipoOpcao) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">Obrigatório.</div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="nome_documento[]" required placeholder="Ex: Manual de utilizador v2" value="<?= htmlspecialchars($linha['nome']) ?>">
                                            <div class="invalid-feedback">Obrigatório.</div>
                                        </td>
                                        <td>
                                            <input type="date" class="form-control form-control-sm" name="data_documento[]" required value="<?= htmlspecialchars($linha['data']) ?>">
                                            <div class="invalid-feedback">Obrigatório.</div>
                                        </td>
                                        <td>
                                            <input type="date" class="form-control form-control-sm" name="validade_documento[]" value="<?= htmlspecialchars($linha['validade']) ?>">
                                            <div class="form-text small label-validade-info">Obrigatória para Certificado de calibração / Contrato de manutenção.</div>
                                            <div class="invalid-feedback">A validade é obrigatória para este tipo de documento.</div>
                                        </td>
                                        <td>
                                            <input type="file" class="form-control form-control-sm" name="ficheiro_documento[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                            <div class="form-text small">PDF, DOC, JPG, PNG.</div>
                                            <div class="invalid-feedback">O ficheiro é obrigatório.</div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remover-linha" title="Remover linha" <?= count($linhasParaRenderizar) === 1 ? 'disabled' : '' ?>>
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
                                <button type="submit" class="btn btn-primary" id="btnGuardar">
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