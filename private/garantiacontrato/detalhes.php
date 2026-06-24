<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Técnico']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// 1. Recolher e validar o ID encriptado
$idEncrypted = $_GET['id'] ?? null;
$idGarantia = aes_decrypt($idEncrypted);

if (!$idGarantia || !is_numeric($idGarantia)) {
    header('Location: listar.php');
    exit;
}

$erro_sistema = '';
$garantia = null;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("
        SELECT g.*, e.codigo_interno, e.designacao AS equipamento_nome
        FROM garantias_contratos g
        JOIN equipamentos e ON e.id_equipamento = g.id_equipamento
        WHERE g.id_garantia = :id
    ");
    $stmt->execute([':id' => $idGarantia]);
    $garantia = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$garantia) {
        header('Location: listar.php');
        exit;
    }
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}
$ligacao = null;

if (empty($erro_sistema)) {
    $hoje = new DateTime();
    $em90dias = (new DateTime())->modify('+90 days');
    $fimGarantia = new DateTime($garantia->data_fim_garantia);
    if ($fimGarantia < $hoje) {
        $estadoGarantia = 'expirada';
    } elseif ($fimGarantia <= $em90dias) {
        $estadoGarantia = 'expirar';
    } else {
        $estadoGarantia = 'valida';
    }
}
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-12 col-lg-10 col-sm-6">
                <div class="d-flex justify-content-center mt-4">
                    <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                        <div class="card-body">

                            <?php if (!empty($erro_sistema)) : ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($erro_sistema) ?></div>
                            <?php else : ?>

                            <h2 class="mb-4">
                                <strong><i class="fa-solid fa-file-contract fa-1x mb-3"></i> Detalhes da Garantia / Contrato</strong>
                            </h2>
                            <hr>

                            <!-- Linha 1: Equipamento + Entidade -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Equipamento associado</label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($garantia->codigo_interno . ' - ' . $garantia->equipamento_nome) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Entidade responsável</label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($garantia->entidade_responsavel ?? '—') ?></p>
                                </div>
                            </div>

                            <!-- Linha 2: Início + Fim + Estado -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Início da garantia</label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($garantia->data_inicio_garantia ?? '—') ?></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Fim da garantia</label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($garantia->data_fim_garantia) ?></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Estado da garantia</label>
                                    <p class="form-control-plaintext">
                                        <?php if ($estadoGarantia === 'valida') : ?>
                                            <span class="badge bg-success">Válida</span>
                                        <?php elseif ($estadoGarantia === 'expirar') : ?>
                                            <span class="badge bg-warning text-dark">A expirar</span>
                                        <?php else : ?>
                                            <span class="badge bg-danger">Expirada</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Linha 3: Contrato + Tipo + Periodicidade -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Contrato de manutenção</label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($garantia->tem_contrato ?? '—') ?></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tipo de contrato</label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($garantia->tipo_contrato ?? '—') ?></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Periodicidade da manutenção</label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($garantia->periodicidade ?? '—') ?></p>
                                </div>
                            </div>

                            <!-- Linha 4: Observações -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Observações</label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($garantia->observacoes ?? '—') ?></p>
                                </div>
                            </div>

                            <?php endif; ?>

                        </div>

                        <div class="d-flex justify-content-end gap-2 p-3">
                            <a href="listar.php" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                            </a>
                            <?php if (empty($erro_sistema)) : ?>
                            <a href="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>" class="btn btn-primary" style="background-color: #0077a8; border-color: #0077a8;">
                                <i class="fa-regular fa-pen-to-square me-1"></i> Editar
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>

    <!-- Menu Mobile -->
    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
