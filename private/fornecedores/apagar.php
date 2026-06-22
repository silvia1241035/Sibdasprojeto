<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Gestor de Logística']);

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
$nEquipamentos = 0;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Se já foi confirmado (botão "Sim"), faz o soft delete e redireciona
    if (isset($_GET['confirmar'])) {
        $stmtNome = $ligacao->prepare("SELECT nome FROM fornecedores WHERE id_fornecedor = :id");
        $stmtNome->execute([':id' => $idFornecedor]);
        $nomeFornecedor = $stmtNome->fetchColumn();

        $stmt = $ligacao->prepare("UPDATE fornecedores SET ativo = 0 WHERE id_fornecedor = :id");
        $stmt->execute([':id' => $idFornecedor]);
        if ($nomeFornecedor) {
            registar_log('eliminar', "Fornecedor desativado: {$nomeFornecedor}.", $_SESSION['id_utilizador'] ?? null);
        }
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

    // Aviso: a desativação não remove as associações a equipamentos existentes.
    $stmt = $ligacao->prepare("
        SELECT COUNT(*) FROM equipamento_fornecedor ef
        JOIN equipamentos e ON e.id_equipamento = ef.id_equipamento
        WHERE ef.id_fornecedor = :id AND e.estado != 'Abatido'
    ");
    $stmt->execute([':id' => $idFornecedor]);
    $nEquipamentos = (int)$stmt->fetchColumn();
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
    registar_log('erro', "Erro ao desativar o fornecedor na base de dados.", $_SESSION['id_utilizador'] ?? null);
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
                        <?php elseif ((int)$fornecedor->ativo === 0) : ?>
                            <p class="mb-4 fs-5">Este fornecedor já está marcado como <strong>Inativo</strong>.</p>
                            <a href="listar.php" class="btn btn-outline-secondary px-4">
                                <i class="fa-solid fa-arrow-left me-2"></i>Voltar
                            </a>
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

                        <?php if ($nEquipamentos > 0) : ?>
                        <div class="alert alert-warning text-start">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Existe(m) <strong><?= $nEquipamentos ?></strong> equipamento(s) ainda associado(s) a este fornecedor.
                            Desativá-lo não remove essas associações — se este fornecedor deixou mesmo de fornecer ou de dar assistência a esses equipamentos, deve-se editar cada equipamento e associar um novo fornecedor.
                        </div>
                        <?php endif; ?>

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
