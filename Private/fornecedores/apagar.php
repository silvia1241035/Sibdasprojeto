<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// 1. Recolher e validar o ID encriptado
$idEncrypted = $_GET['id'] ?? null;
$idFornecedor = aes_decrypt($idEncrypted);

if (!$idFornecedor || !is_numeric($idFornecedor)) {
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

    // 2. Se já foi confirmado (botão "Sim"), faz o soft delete e redireciona
    if (isset($_GET['confirmar'])) {
        $stmt = $ligacao->prepare("UPDATE fornecedores SET ativo = 0 WHERE id_fornecedor = :id");
        $stmt->execute([':id' => $idFornecedor]);
        header('Location: listar.php');
        exit;
    }

    // 3. Caso contrário, mostra a confirmação com os dados reais do fornecedor
    $stmt = $ligacao->prepare("SELECT * FROM fornecedores WHERE id_fornecedor = :id");
    $stmt->execute([':id' => $idFornecedor]);
    $fornecedor = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$fornecedor) {
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

                        <p class="mb-2 fs-5">Deseja desativar o fornecedor?</p>

                        <h4 class="mb-4"><strong><?= htmlspecialchars($fornecedor->nome) ?></strong></h4>

                        <div class="mb-4">
                            <span class="d-block mb-1"><i class="fa-solid fa-at me-2"></i><strong><?= htmlspecialchars($fornecedor->email ?? '—') ?></strong></span>
                            <span class="d-block"><i class="fa-solid fa-phone me-2"></i><strong><?= htmlspecialchars($fornecedor->contacto ?? '—') ?></strong></span>
                        </div>
                        <p class="text-muted small mb-4">
                            O fornecedor não é eliminado definitivamente — fica marcado como inativo e pode ser reativado mais tarde.
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

    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
