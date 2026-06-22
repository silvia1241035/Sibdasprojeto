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
$idDocumento = aes_decrypt($idEncrypted);

if (!$idDocumento || !is_numeric($idDocumento)) {
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
    // Sem reativação — um documento desativado significa que existe outro a
    // substituí-lo, não um erro a desfazer.
    if (isset($_GET['confirmar'])) {
        $stmtNome = $ligacao->prepare("SELECT nome FROM documentacao WHERE id_documento = :id");
        $stmtNome->execute([':id' => $idDocumento]);
        $nomeDocumento = $stmtNome->fetchColumn();

        $stmt = $ligacao->prepare("UPDATE documentacao SET ativo = 0 WHERE id_documento = :id");
        $stmt->execute([':id' => $idDocumento]);
        if ($nomeDocumento) {
            registar_log('eliminar', "Documento desativado: {$nomeDocumento}.", $_SESSION['id_utilizador'] ?? null);
        }
        header('Location: listar.php');
        exit;
    }

    // 3. Caso contrário, mostra a confirmação com os dados reais do documento
    $stmt = $ligacao->prepare("
        SELECT d.*, e.designacao AS equipamento_nome
        FROM documentacao d
        JOIN equipamentos e ON e.id_equipamento = d.id_equipamento
        WHERE d.id_documento = :id
    ");
    $stmt->execute([':id' => $idDocumento]);
    $documento = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$documento) {
        header('Location: listar.php');
        exit;
    }
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
    registar_log('erro', "Erro ao desativar o documento na base de dados.", $_SESSION['id_utilizador'] ?? null);
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
                <?php elseif ((int)$documento->ativo === 0) : ?>
                    <p class="mb-4 fs-5">Este documento já está marcado como <strong>Inativo</strong>.</p>
                    <a href="listar.php" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-arrow-left me-2"></i>Voltar
                    </a>
                <?php else : ?>

                <div class="text-warning display-4 mb-3">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <p class="mb-2 fs-5">Deseja desativar o documento?</p>

                <h4 class="mb-4"><strong><?= htmlspecialchars($documento->nome) ?></strong></h4>

                <div class="mb-4">
                    <span class="d-block mb-1"><i class="fa-solid fa-file-lines me-2"></i><strong><?= htmlspecialchars($documento->tipo) ?></strong></span>
                    <span class="d-block"><i class="fa-solid fa-laptop-medical me-2"></i><strong><?= htmlspecialchars($documento->equipamento_nome) ?></strong></span>
                </div>
                <p class="text-muted small mb-4">
                    O documento não é eliminado — fica guardado no histórico como inativo. Use esta opção quando o documento foi substituído por um mais recente.
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
