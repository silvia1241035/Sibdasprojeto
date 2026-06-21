<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

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

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Se já foi confirmado (botão "Sim"), faz o soft delete e redireciona.
    // Sem reativação — pelo mesmo motivo da documentação: um registo desativado
    // significa que existe um novo (renovado) a substituí-lo.
    if (isset($_GET['confirmar'])) {
        $stmt = $ligacao->prepare("UPDATE garantias_contratos SET ativo = 0 WHERE id_garantia = :id");
        $stmt->execute([':id' => $idGarantia]);
        header('Location: listar.php');
        exit;
    }

    // 3. Caso contrário, mostra a confirmação com os dados reais do registo
    $stmt = $ligacao->prepare("
        SELECT g.*, e.designacao AS equipamento_nome
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
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-center mt-4">
                    <div class="card w-100 shadow rounded text-center p-4" style="max-width: 700px;">

                        <?php if (!empty($erro_sistema)) : ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($erro_sistema) ?></div>
                        <?php else : ?>

                        <div class="text-warning display-4 mb-3">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>

                        <p class="mb-2 fs-5">Deseja desativar este registo de garantia / contrato?</p>

                        <h4 class="mb-4"><strong><?= htmlspecialchars($garantia->equipamento_nome) ?></strong></h4>

                        <div class="mb-4">
                            <span class="d-block mb-1"><i class="fa-solid fa-calendar-xmark me-2"></i>Fim da garantia: <strong><?= htmlspecialchars($garantia->data_fim_garantia) ?></strong></span>
                            <span class="d-block"><i class="fa-solid fa-building me-2"></i>Entidade: <strong><?= htmlspecialchars($garantia->entidade_responsavel ?? '—') ?></strong></span>
                        </div>
                        <p class="text-muted small mb-4">
                            O registo não é eliminado — fica guardado no histórico como inativo. Use esta opção quando a garantia/contrato foi renovado num novo registo.
                        </p>

                        <div class="d-flex justify-content-center gap-3">
                            <a href="listar.php" class="btn btn-outline-secondary px-4">
                                <i class="fa-solid fa-xmark me-2"></i>Não
                            </a>
                            <a href="apagar.php?id=<?= htmlspecialchars($idEncrypted) ?>&confirmar=1" class="btn btn-danger px-4">
                                <i class="fa-solid fa-check me-2"></i>Sim
                            </a>
                        </div>

                        <?php endif; ?>

                    </div>
                </div>
            </main>

    <!-- Menu Mobile -->
    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
