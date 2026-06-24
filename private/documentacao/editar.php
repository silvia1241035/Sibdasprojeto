<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Técnico']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// 1. Recolher e validar o ID encriptado
$idEncrypted = $_GET['id'] ?? $_POST['id'] ?? null;
$idDocumento = aes_decrypt($idEncrypted);

if (!$idDocumento || !is_numeric($idDocumento)) {
    header('Location: listar.php');
    exit;
}

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
    $equipamentos = $ligacao->query("SELECT id_equipamento, codigo_interno, designacao FROM equipamentos ORDER BY designacao")->fetchAll(PDO::FETCH_OBJ);
    // Só faz sentido escolher um fornecedor que esteja realmente associado ao equipamento deste
    // documento. Inclui também o fornecedor atual do documento mesmo que tenha sido entretanto
    // desativado ou desassociado do equipamento, para não o "perder" silenciosamente do formulário.
    $stmtForn = $ligacao->prepare("
        SELECT f.id_fornecedor, f.nome FROM fornecedores f
        WHERE (
            f.ativo = 1
            AND f.id_fornecedor IN (
                SELECT ef.id_fornecedor FROM equipamento_fornecedor ef
                WHERE ef.id_equipamento = (SELECT id_equipamento FROM documentacao WHERE id_documento = :id1)
            )
        )
        OR f.id_fornecedor = (SELECT id_fornecedor FROM documentacao WHERE id_documento = :id2)
        ORDER BY f.nome
    ");
    $stmtForn->execute([':id1' => $idDocumento, ':id2' => $idDocumento]);
    $fornecedores = $stmtForn->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}

$tiposDocumentoValidos       = ['Manual de utilizador', 'Manual de serviço', 'Certificado de calibração', 'Contrato de manutenção', 'Fatura/Guia de aquisição', 'Declaração de conformidade', 'Relatório técnico'];
$tiposComValidadeObrigatoria = ['Certificado de calibração', 'Contrato de manutenção'];
$extensoesPermitidas         = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

// 2. Obter o documento atual — feito antes do POST porque, se não for escolhido
// um novo ficheiro, é o caminho atual que se mantém guardado.
$documento = null;
if (empty($erro_sistema)) {
    try {
        $stmt = $ligacao->prepare("SELECT * FROM documentacao WHERE id_documento = :id");
        $stmt->execute([':id' => $idDocumento]);
        $documento = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$documento) {
            header('Location: listar.php');
            exit;
        }
        // Um documento inativo (substituído por outro mais recente) não pode ser editado.
        if ((int)$documento->ativo === 0) {
            header('Location: detalhes.php?id=' . $idEncrypted);
            exit;
        }
    } catch (PDOException $err) {
        $erro_sistema = "Aconteceu um erro na ligação.";
    }
}

// A validade só fica bloqueada se o documento já tinha, à entrada, um tipo que
// exige validade real (calibração/contrato) — para os outros tipos, onde a
// validade normalmente nem é usada, continua livremente editável.
$validadeBloqueada = in_array($documento->tipo ?? '', $tiposComValidadeObrigatoria, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erro_sistema)) {
    // 3. Recolher dados do formulário — equipamento é sempre imutável; a validade
    // só é imutável quando $validadeBloqueada for verdadeiro (ver acima).
    $tipo          = trim($_POST['tipo_documento'] ?? '');
    $nome          = trim($_POST['nome_documento'] ?? '');
    $data          = trim($_POST['data_documento'] ?? '');
    $validade      = $validadeBloqueada ? $documento->validade : trim($_POST['validade_documento'] ?? '');
    $idEquipamento = $documento->id_equipamento;
    $idFornecedor  = trim($_POST['fornecedor_documento'] ?? '');
    $temFicheiro   = isset($_FILES['ficheiro_documento']) && $_FILES['ficheiro_documento']['error'] !== UPLOAD_ERR_NO_FILE;

    // 4. Validar dados (mesmas regras do inserir.php, exceto equipamento e, quando bloqueada, a validade)
    $idsFornecedorValidos = array_map(fn($f) => (string)$f->id_fornecedor, $fornecedores);

    if (empty($tipo)) {
        $erros[] = "O campo Tipo de documento é obrigatório.";
    } elseif (!in_array($tipo, $tiposDocumentoValidos, true)) {
        $erros[] = "Tipo de documento inválido.";
    }

    if (empty($nome)) {
        $erros[] = "O campo Nome do documento é obrigatório.";
    }

    if (empty($data)) {
        $erros[] = "O campo Data do documento é obrigatório.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        $erros[] = "Formato de data inválido.";
    } else {
        [$ano, $mes, $dia] = explode('-', $data);
        if (!checkdate((int)$mes, (int)$dia, (int)$ano)) {
            $erros[] = "Data do documento inválida.";
        } elseif ($data > date('Y-m-d')) {
            $erros[] = "A data não pode ser futura.";
        }
    }

    if (!$validadeBloqueada) {
        if (empty($validade)) {
            if (in_array($tipo, $tiposComValidadeObrigatoria, true)) {
                $erros[] = "A validade é obrigatória para o tipo \"{$tipo}\".";
            }
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $validade)) {
            $erros[] = "Formato de validade inválido.";
        } else {
            [$anoV, $mesV, $diaV] = explode('-', $validade);
            if (!checkdate((int)$mesV, (int)$diaV, (int)$anoV)) {
                $erros[] = "Validade inválida.";
            }
        }
    }

    if (!empty($idFornecedor) && !in_array($idFornecedor, $idsFornecedorValidos, true)) {
        $erros[] = "O fornecedor selecionado é inválido.";
    }

    $novoFicheiro = null;
    if ($temFicheiro) {
        if ($_FILES['ficheiro_documento']['error'] !== UPLOAD_ERR_OK) {
            $erros[] = "Erro ao carregar o ficheiro.";
        } else {
            $ext = strtolower(pathinfo($_FILES['ficheiro_documento']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $extensoesPermitidas, true)) {
                $erros[] = "Tipo de ficheiro não permitido (use PDF, DOC, DOCX, JPG ou PNG).";
            } else {
                $novoFicheiro = ['tmp' => $_FILES['ficheiro_documento']['tmp_name'], 'ext' => $ext];
            }
        }
    }

    // 5. Normalizar dados — o nome não é forçado a Maiúsculas/minúsculas, pelo
    // mesmo motivo já aplicado no inserir.php (proteger siglas/modelos).
    $validade = $validade !== '' ? $validade : null;

    // 6. Atualizar na base de dados
    if (empty($erros)) {
        try {
            $caminhoFicheiro = $documento->caminho_ficheiro;
            if ($novoFicheiro !== null) {
                $nomeFicheiro = uniqid('doc_') . '.' . $novoFicheiro['ext'];
                $destino = __DIR__ . '/../../uploads/documentacao/' . $nomeFicheiro;
                if (move_uploaded_file($novoFicheiro['tmp'], $destino)) {
                    $ficheiroAntigo = $documento->caminho_ficheiro
                        ? __DIR__ . '/../../uploads/documentacao/' . basename($documento->caminho_ficheiro)
                        : null;
                    $caminhoFicheiro = BASE_URL . '/uploads/documentacao/' . $nomeFicheiro;
                    if ($ficheiroAntigo && is_file($ficheiroAntigo)) {
                        @unlink($ficheiroAntigo);
                    }
                }
            }

            $sql = "UPDATE documentacao SET
                        tipo = :tipo, nome = :nome, data = :data, validade = :validade,
                        caminho_ficheiro = :ficheiro, id_equipamento = :idequip, id_fornecedor = :idforn
                    WHERE id_documento = :id";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':tipo'     => $tipo,
                ':nome'     => $nome,
                ':data'     => $data,
                ':validade' => $validade,
                ':ficheiro' => $caminhoFicheiro,
                ':idequip'  => $idEquipamento,
                ':idforn'   => $idFornecedor !== '' ? $idFornecedor : null,
                ':id'       => $idDocumento,
            ]);
            registar_log('editar', "Documento atualizado: {$nome}.", $_SESSION['id_utilizador'] ?? null);
            header('Location: listar.php');
            exit;
        } catch (PDOException $err) {
            if ($err->getCode() == 23000) {
                $erro_sistema = "Já existe um documento com este nome associado a este equipamento.";
            } else {
                $erro_sistema = "Erro ao atualizar os dados: " . $err->getMessage();
            }
            $descricaoErro = $err->getCode() == 23000
                ? "Tentativa de atualizar documento para um nome já existente neste equipamento."
                : "Erro ao atualizar o documento na base de dados.";
            registar_log('erro', $descricaoErro, $_SESSION['id_utilizador'] ?? null);
        }
    }
}

$ligacao = null;

// Valor a apresentar em cada campo: o que foi submetido (em caso de erro) ou o valor atual na BD
function valorCampo($postKey, $registo, $campoBd)
{
    return $_POST[$postKey] ?? ($registo->$campoBd ?? '');
}
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-12 col-lg-10 col-sm-6">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-pen fa-1x mb-3"></i> Atualizar documento</strong></h2>
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

                    <form action="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>" method="post" enctype="multipart/form-data" novalidate id="formDocumento">

                        <!-- Área de erros — validação no browser -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao atualizar o documento. Por favor, tente novamente.
                        </div>

                        <!-- Linha 1: Tipo de documento + Nome -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo" class="form-label">Tipo de documento<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <?php $tipoAtual = valorCampo('tipo_documento', $documento, 'tipo'); ?>
                                <select class="form-select" id="tipo" name="tipo_documento" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($tiposDocumentoValidos as $tipoOpcao) : ?>
                                        <option value="<?= htmlspecialchars($tipoOpcao) ?>" data-requer-validade="<?= in_array($tipoOpcao, $tiposComValidadeObrigatoria, true) ? '1' : '0' ?>" <?= ($tipoAtual === $tipoOpcao) ? 'selected' : '' ?>><?= htmlspecialchars($tipoOpcao) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o tipo de documento.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="nome" class="form-label">Nome do documento<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="nome" name="nome_documento" required placeholder="Ex: Manual de utilizador do monitor X" value="<?= htmlspecialchars(valorCampo('nome_documento', $documento, 'nome')) ?>">
                                <div class="invalid-feedback">Por favor, insira o nome do documento.</div>
                            </div>
                        </div>

                        <!-- Linha 2: Data do documento + Data de validade -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="data" class="form-label">Data do documento<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="date" class="form-control" id="data" name="data_documento" required value="<?= htmlspecialchars(valorCampo('data_documento', $documento, 'data')) ?>">
                                <div class="invalid-feedback">Por favor, insira a data do documento.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="validade" class="form-label">Data de validade</label>
                                <?php if ($validadeBloqueada) : ?>
                                    <input type="date" class="form-control" id="validade" readonly value="<?= htmlspecialchars($documento->validade ?? '') ?>">
                                    <div class="form-text">Não pode ser alterada — corresponde a uma calibração/contrato já emitido. Para renovar, crie um novo documento.</div>
                                <?php else : ?>
                                    <input type="date" class="form-control" id="validade" name="validade_documento" value="<?= htmlspecialchars(valorCampo('validade_documento', $documento, 'validade')) ?>">
                                    <div class="form-text label-validade-info">Obrigatória para Certificado de calibração / Contrato de manutenção.</div>
                                    <div class="invalid-feedback">A validade é obrigatória para este tipo de documento.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Linha 3: Equipamento associado + Fornecedor associado -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="equipamento" class="form-label">Equipamento associado</label>
                                <select class="form-select" id="equipamento" disabled>
                                    <?php foreach ($equipamentos as $eq) : ?>
                                        <option value="<?= $eq->id_equipamento ?>" <?= ((string)$documento->id_equipamento === (string)$eq->id_equipamento) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($eq->codigo_interno . ' - ' . $eq->designacao) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Não pode ser alterado — para mudar o equipamento, crie um novo documento.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="fornecedor" class="form-label">Fornecedor associado</label>
                                <?php $idFornecedorAtual = valorCampo('fornecedor_documento', $documento, 'id_fornecedor'); ?>
                                <select class="form-select" id="fornecedor" name="fornecedor_documento">
                                    <option value="">Nenhum / Selecione...</option>
                                    <?php foreach ($fornecedores as $forn) : ?>
                                        <option value="<?= $forn->id_fornecedor ?>" <?= ((string)$idFornecedorAtual === (string)$forn->id_fornecedor) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($forn->nome) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Apenas fornecedores associados a este equipamento. <a href="../equipamentos/editar.php?id=<?= aes_encrypt($documento->id_equipamento) ?>" target="_blank" style="color:#0077a8;">Gerir fornecedores do equipamento</a>.</div>
                            </div>
                        </div>

                        <!-- Linha 4: Ficheiro -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="ficheiro" class="form-label">Ficheiro</label>
                                <?php if (!empty($documento->caminho_ficheiro)) : ?>
                                <div class="mb-2">
                                    <i class="fa-solid fa-paperclip me-1" style="color:#0077a8;"></i>
                                    Ficheiro atual:
                                    <a href="<?= htmlspecialchars($documento->caminho_ficheiro) ?>" target="_blank" style="color:#0077a8;"><?= htmlspecialchars(basename($documento->caminho_ficheiro)) ?></a>
                                </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="ficheiro" name="ficheiro_documento" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <div class="form-text">Escolha um novo ficheiro apenas se quiser substituir o atual.</div>
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

    <!-- Menu Mobile -->
    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
