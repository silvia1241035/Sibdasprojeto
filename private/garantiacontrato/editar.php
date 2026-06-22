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
$idGarantia = aes_decrypt($idEncrypted);

if (!$idGarantia || !is_numeric($idGarantia)) {
    header('Location: listar.php');
    exit;
}

$erros = [];
$erro_sistema = '';
$equipamentos = [];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $equipamentos = $ligacao->query("SELECT id_equipamento, codigo_interno, designacao FROM equipamentos ORDER BY designacao")->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}

$contratosValidos      = ['Sim', 'Não'];
$tiposContratoValidos  = ['Preventiva', 'Corretiva', 'Completa', 'Outro'];
$periodicidadesValidas = ['Mensal', 'Trimestral', 'Semestral', 'Anual'];

// 2. Obter o registo atual
$garantia = null;
if (empty($erro_sistema)) {
    try {
        $stmt = $ligacao->prepare("SELECT * FROM garantias_contratos WHERE id_garantia = :id");
        $stmt->execute([':id' => $idGarantia]);
        $garantia = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$garantia) {
            header('Location: listar.php');
            exit;
        }
        // Um registo inativo (substituído por uma renovação) não pode ser editado.
        if ((int)$garantia->ativo === 0) {
            header('Location: detalhes.php?id=' . $idEncrypted);
            exit;
        }
    } catch (PDOException $err) {
        $erro_sistema = "Aconteceu um erro na ligação.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erro_sistema)) {
    // 3. Recolher dados — equipamento, início e fim são imutáveis (mesmo motivo da
    // validade em documentacao/editar.php: evitar "renovar" só editando a data,
    // sem o contrato/garantia ter sido de facto renovado). Para isso, cria-se um
    // novo registo (a relação é 1:N).
    $idEquipamento  = $garantia->id_equipamento;
    $entidade       = trim($_POST['entidade_garantia'] ?? '');
    $inicioGarantia = $garantia->data_inicio_garantia;
    $fimGarantia    = $garantia->data_fim_garantia;
    $contrato       = trim($_POST['contrato_garantia'] ?? '');
    $tipoContrato   = trim($_POST['tipocontrato_garantia'] ?? '');
    $periodicidade  = trim($_POST['periodicidade_garantia'] ?? '');
    $obs            = trim($_POST['observacoes_garantia'] ?? '');

    // 4. Validar dados (mesmas regras do inserir.php, exceto equipamento/início/fim)
    if (!empty($contrato) && !in_array($contrato, $contratosValidos, true)) {
        $erros[] = "Valor inválido para Contrato de manutenção.";
    }

    if (!empty($tipoContrato) && !in_array($tipoContrato, $tiposContratoValidos, true)) {
        $erros[] = "Tipo de contrato inválido.";
    }

    if (!empty($periodicidade) && !in_array($periodicidade, $periodicidadesValidas, true)) {
        $erros[] = "Periodicidade inválida.";
    }

    // 5. Normalizar dados
    $entidade      = $entidade !== '' ? $entidade : null;
    $contrato      = $contrato !== '' ? $contrato : null;
    $tipoContrato  = $tipoContrato !== '' ? $tipoContrato : null;
    $periodicidade = $periodicidade !== '' ? $periodicidade : null;
    $obs           = $obs !== '' ? $obs : null;

    // 6. Atualizar na base de dados
    if (empty($erros)) {
        try {
            $sql = "UPDATE garantias_contratos SET
                        id_equipamento = :idequip, entidade_responsavel = :entidade,
                        data_inicio_garantia = :inicio, data_fim_garantia = :fim,
                        tem_contrato = :contrato, tipo_contrato = :tipocontrato,
                        periodicidade = :periodicidade, observacoes = :obs
                    WHERE id_garantia = :id";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':idequip'       => $idEquipamento,
                ':entidade'      => $entidade,
                ':inicio'        => $inicioGarantia,
                ':fim'           => $fimGarantia,
                ':contrato'      => $contrato,
                ':tipocontrato'  => $tipoContrato,
                ':periodicidade' => $periodicidade,
                ':obs'           => $obs,
                ':id'            => $idGarantia,
            ]);
            header('Location: listar.php');
            exit;
        } catch (PDOException $err) {
            $erro_sistema = "Erro ao atualizar os dados: " . $err->getMessage();
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

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-pen fa-1x mb-3"></i> Atualizar garantia / contrato</strong></h2>
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

                    <form action="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>" method="post" novalidate id="formGarantia">

                        <!-- Área de erros -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao atualizar o registo. Por favor, tente novamente.
                        </div>

                        <!-- Linha 1: Equipamento + Entidade -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="equipamento" class="form-label">Equipamento associado</label>
                                <select class="form-select" id="equipamento" disabled>
                                    <?php foreach ($equipamentos as $eq) : ?>
                                        <option value="<?= $eq->id_equipamento ?>" <?= ((string)$garantia->id_equipamento === (string)$eq->id_equipamento) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($eq->codigo_interno . ' - ' . $eq->designacao) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Não pode ser alterado — para outro equipamento, crie um novo registo.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="entidade" class="form-label">Entidade responsável</label>
                                <input type="text" class="form-control" id="entidade" name="entidade_garantia" placeholder="Ex: Philips Healthcare" value="<?= htmlspecialchars(valorCampo('entidade_garantia', $garantia, 'entidade_responsavel')) ?>">
                            </div>
                        </div>

                        <!-- Linha 2: Início + Fim da garantia -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="inicio_garantia" class="form-label">Data de início da garantia</label>
                                <input type="date" class="form-control" id="inicio_garantia" readonly value="<?= htmlspecialchars($garantia->data_inicio_garantia ?? '') ?>">
                                <div class="form-text">Não pode ser alterada — para renovar, crie um novo registo.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="fim" class="form-label">Data de fim da garantia</label>
                                <input type="date" class="form-control" id="fim" readonly value="<?= htmlspecialchars($garantia->data_fim_garantia ?? '') ?>">
                                <div class="form-text">Não pode ser alterada — para renovar, crie um novo registo.</div>
                            </div>
                        </div>

                        <!-- Linha 3: Contrato + Tipo + Periodicidade -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="contrato" class="form-label">Contrato de manutenção</label>
                                <?php $contratoAtual = valorCampo('contrato_garantia', $garantia, 'tem_contrato'); ?>
                                <select class="form-select" id="contrato" name="contrato_garantia">
                                    <option value="">Selecione...</option>
                                    <option value="Sim" <?= ($contratoAtual === 'Sim') ? 'selected' : '' ?>>Sim</option>
                                    <option value="Não" <?= ($contratoAtual === 'Não') ? 'selected' : '' ?>>Não</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tipocontrato" class="form-label">Tipo de contrato</label>
                                <?php $tipoContratoAtual = valorCampo('tipocontrato_garantia', $garantia, 'tipo_contrato'); ?>
                                <select class="form-select" id="tipocontrato" name="tipocontrato_garantia">
                                    <option value="">Selecione...</option>
                                    <option value="Preventiva" <?= ($tipoContratoAtual === 'Preventiva') ? 'selected' : '' ?>>Manutenção preventiva</option>
                                    <option value="Corretiva" <?= ($tipoContratoAtual === 'Corretiva') ? 'selected' : '' ?>>Manutenção corretiva</option>
                                    <option value="Completa" <?= ($tipoContratoAtual === 'Completa') ? 'selected' : '' ?>>Completa (preventiva + corretiva)</option>
                                    <option value="Outro" <?= ($tipoContratoAtual === 'Outro') ? 'selected' : '' ?>>Outro</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="periodicidade" class="form-label">Periodicidade</label>
                                <?php $periodicidadeAtual = valorCampo('periodicidade_garantia', $garantia, 'periodicidade'); ?>
                                <select class="form-select" id="periodicidade" name="periodicidade_garantia">
                                    <option value="">Selecione...</option>
                                    <option value="Mensal" <?= ($periodicidadeAtual === 'Mensal') ? 'selected' : '' ?>>Mensal</option>
                                    <option value="Trimestral" <?= ($periodicidadeAtual === 'Trimestral') ? 'selected' : '' ?>>Trimestral</option>
                                    <option value="Semestral" <?= ($periodicidadeAtual === 'Semestral') ? 'selected' : '' ?>>Semestral</option>
                                    <option value="Anual" <?= ($periodicidadeAtual === 'Anual') ? 'selected' : '' ?>>Anual</option>
                                </select>
                            </div>
                        </div>

                        <!-- Linha 4: Observações -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes_garantia" rows="3" placeholder="Notas adicionais sobre a garantia ou o contrato..."><?= htmlspecialchars(valorCampo('observacoes_garantia', $garantia, 'observacoes')) ?></textarea>
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

    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
