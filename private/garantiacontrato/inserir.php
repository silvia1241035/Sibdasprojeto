<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Técnico']);

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
    // Um equipamento pode ter vários registos de garantia/contrato ao longo do tempo (relação 1:N)
    $equipamentos = $ligacao->query("
        SELECT id_equipamento, codigo_interno, designacao
        FROM equipamentos
        ORDER BY designacao
    ")->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}

$contratosValidos      = ['Sim', 'Não'];
$tiposContratoValidos  = ['Preventiva', 'Corretiva', 'Completa', 'Outro'];
$periodicidadesValidas = ['Mensal', 'Trimestral', 'Semestral', 'Anual'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recolher dados
    $idEquipamento    = trim($_POST['equipamento_garantia'] ?? '');
    $entidade         = trim($_POST['entidade_garantia'] ?? '');
    $inicioGarantia   = trim($_POST['inicio_garantia'] ?? '');
    $fimGarantia      = trim($_POST['fim_garantia'] ?? '');
    $contrato         = trim($_POST['contrato_garantia'] ?? '');
    $tipoContrato     = trim($_POST['tipocontrato_garantia'] ?? '');
    $periodicidade    = trim($_POST['periodicidade_garantia'] ?? '');
    $obs              = trim($_POST['observacoes_garantia'] ?? '');

    // 2. Validar dados
    $idsEquipamentoValidos = array_map(fn($e) => (string)$e->id_equipamento, $equipamentos);

    if (empty($idEquipamento)) {
        $erros[] = "O campo Equipamento associado é obrigatório.";
    } elseif (!in_array($idEquipamento, $idsEquipamentoValidos, true)) {
        $erros[] = "O equipamento selecionado é inválido.";
    }

    if (empty($fimGarantia)) {
        $erros[] = "O campo Data de fim da garantia é obrigatório.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fimGarantia)) {
        $erros[] = "Formato de data de fim inválido.";
    } else {
        [$anoF, $mesF, $diaF] = explode('-', $fimGarantia);
        if (!checkdate((int)$mesF, (int)$diaF, (int)$anoF)) {
            $erros[] = "Data de fim da garantia inválida.";
        }
    }

    if (empty($inicioGarantia)) {
        $erros[] = "O campo Data de início da garantia é obrigatório.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicioGarantia)) {
        $erros[] = "Formato de data de início inválido.";
    } else {
        [$anoI, $mesI, $diaI] = explode('-', $inicioGarantia);
        if (!checkdate((int)$mesI, (int)$diaI, (int)$anoI)) {
            $erros[] = "Data de início da garantia inválida.";
        } elseif (!empty($fimGarantia) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fimGarantia) && $inicioGarantia > $fimGarantia) {
            $erros[] = "A data de início não pode ser posterior à data de fim.";
        }
    }

    if (!empty($contrato) && !in_array($contrato, $contratosValidos, true)) {
        $erros[] = "Valor inválido para Contrato de manutenção.";
    }

    if (!empty($tipoContrato) && !in_array($tipoContrato, $tiposContratoValidos, true)) {
        $erros[] = "Tipo de contrato inválido.";
    }

    if (!empty($periodicidade) && !in_array($periodicidade, $periodicidadesValidas, true)) {
        $erros[] = "Periodicidade inválida.";
    }

    // 3. Normalizar dados — entidade não é forçada a Maiúsculas/minúsculas para não corromper
    // nomes de empresas com siglas (ex: "GE Healthcare", "B. Braun").
    $entidade      = $entidade !== '' ? $entidade : null;
    $inicioGarantia = $inicioGarantia !== '' ? $inicioGarantia : null;
    $contrato      = $contrato !== '' ? $contrato : null;
    $tipoContrato  = $tipoContrato !== '' ? $tipoContrato : null;
    $periodicidade = $periodicidade !== '' ? $periodicidade : null;
    $obs           = $obs !== '' ? $obs : null;

    // 4. Guardar na base de dados
    if (empty($erros) && empty($erro_sistema)) {
        try {
            $sql = "INSERT INTO garantias_contratos (
                        id_equipamento, entidade_responsavel, data_inicio_garantia, data_fim_garantia,
                        tem_contrato, tipo_contrato, periodicidade, observacoes
                    ) VALUES (
                        :idequip, :entidade, :inicio, :fim, :contrato, :tipocontrato, :periodicidade, :obs
                    )";
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
            ]);
            $equipamentoNome = '';
            foreach ($equipamentos as $eq) {
                if ((string)$eq->id_equipamento === (string)$idEquipamento) {
                    $equipamentoNome = $eq->designacao;
                    break;
                }
            }
            registar_log('inserir', "Garantia/contrato criado para o equipamento: {$equipamentoNome}.", $_SESSION['id_utilizador'] ?? null);
            header('Location: listar.php');
            exit;
        } catch (PDOException $err) {
            $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
            registar_log('erro', "Erro ao gravar a garantia/contrato na base de dados.", $_SESSION['id_utilizador'] ?? null);
        }
    }
}

$ligacao = null;
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-square-plus fa-1x mb-3"></i> Adicionar garantia / contrato</strong></h2>
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

                    <form action="#" method="post" novalidate id="formGarantia">

                        <!-- Área de erros — validação no browser -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao inserir o registo. Por favor, tente novamente.
                        </div>

                        <!-- Linha 1: Equipamento + Entidade -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="equipamento" class="form-label">Equipamento associado<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <select class="form-select" id="equipamento" name="equipamento_garantia" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($equipamentos as $eq) : ?>
                                        <option value="<?= $eq->id_equipamento ?>" <?= ((string)($_POST['equipamento_garantia'] ?? '') === (string)$eq->id_equipamento) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($eq->codigo_interno . ' - ' . $eq->designacao) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione o equipamento.</div>
                                <div class="form-text">Um equipamento pode ter vários registos (ex: garantia inicial e contratos de manutenção sucessivos).</div>
                                <?php if (empty($equipamentos)) : ?>
                                    <div class="form-text">Não existem equipamentos disponíveis. <a href="../equipamentos/inserir.php" target="_blank" style="color:#0077a8;">Adicionar equipamento</a></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="entidade" class="form-label">Entidade responsável</label>
                                <input type="text" class="form-control" id="entidade" name="entidade_garantia" placeholder="Ex: Philips Healthcare" value="<?= htmlspecialchars($_POST['entidade_garantia'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Linha 2: Início + Fim da garantia -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="inicio_garantia" class="form-label">Data de início da garantia<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="date" class="form-control" id="inicio_garantia" name="inicio_garantia" required value="<?= htmlspecialchars($_POST['inicio_garantia'] ?? '') ?>">
                                <div class="invalid-feedback">Por favor, insira a data de início da garantia.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="fim" class="form-label">Data de fim da garantia<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="date" class="form-control" id="fim" name="fim_garantia" required value="<?= htmlspecialchars($_POST['fim_garantia'] ?? '') ?>">
                                <div class="invalid-feedback">Por favor, insira a data de fim da garantia.</div>
                            </div>
                        </div>

                        <!-- Linha 3: Contrato + Tipo + Periodicidade -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="contrato" class="form-label">Contrato de manutenção</label>
                                <select class="form-select" id="contrato" name="contrato_garantia">
                                    <option value="">Selecione...</option>
                                    <option value="Sim" <?= (($_POST['contrato_garantia'] ?? '') === 'Sim') ? 'selected' : '' ?>>Sim</option>
                                    <option value="Não" <?= (($_POST['contrato_garantia'] ?? '') === 'Não') ? 'selected' : '' ?>>Não</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tipocontrato" class="form-label">Tipo de contrato</label>
                                <select class="form-select" id="tipocontrato" name="tipocontrato_garantia">
                                    <option value="">Selecione...</option>
                                    <option value="Preventiva" <?= (($_POST['tipocontrato_garantia'] ?? '') === 'Preventiva') ? 'selected' : '' ?>>Manutenção preventiva</option>
                                    <option value="Corretiva" <?= (($_POST['tipocontrato_garantia'] ?? '') === 'Corretiva') ? 'selected' : '' ?>>Manutenção corretiva</option>
                                    <option value="Completa" <?= (($_POST['tipocontrato_garantia'] ?? '') === 'Completa') ? 'selected' : '' ?>>Completa (preventiva + corretiva)</option>
                                    <option value="Outro" <?= (($_POST['tipocontrato_garantia'] ?? '') === 'Outro') ? 'selected' : '' ?>>Outro</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="periodicidade" class="form-label">Periodicidade</label>
                                <select class="form-select" id="periodicidade" name="periodicidade_garantia">
                                    <option value="">Selecione...</option>
                                    <option value="Mensal" <?= (($_POST['periodicidade_garantia'] ?? '') === 'Mensal') ? 'selected' : '' ?>>Mensal</option>
                                    <option value="Trimestral" <?= (($_POST['periodicidade_garantia'] ?? '') === 'Trimestral') ? 'selected' : '' ?>>Trimestral</option>
                                    <option value="Semestral" <?= (($_POST['periodicidade_garantia'] ?? '') === 'Semestral') ? 'selected' : '' ?>>Semestral</option>
                                    <option value="Anual" <?= (($_POST['periodicidade_garantia'] ?? '') === 'Anual') ? 'selected' : '' ?>>Anual</option>
                                </select>
                            </div>
                        </div>

                        <!-- Linha 4: Observações -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes_garantia" rows="3" placeholder="Notas adicionais sobre a garantia ou o contrato..."><?= htmlspecialchars($_POST['observacoes_garantia'] ?? '') ?></textarea>
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