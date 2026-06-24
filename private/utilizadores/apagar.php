<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// 1. Recolher e validar o ID encriptado
$idEncrypted = $_GET['id'] ?? null;
$idUtilizador = aes_decrypt($idEncrypted);

if (!$idUtilizador || !is_numeric($idUtilizador)) {
    header('Location: listar.php');
    exit;
}

// Nunca permitir desativar a própria conta.
if ((int)$idUtilizador === (int)($_SESSION['id_utilizador'] ?? 0)) {
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
        $stmtNome = $ligacao->prepare("SELECT nome FROM utilizadores WHERE id_utilizador = :id");
        $stmtNome->execute([':id' => $idUtilizador]);
        $nomeUtilizador = $stmtNome->fetchColumn();

        $stmt = $ligacao->prepare("UPDATE utilizadores SET ativo = 0 WHERE id_utilizador = :id");
        $stmt->execute([':id' => $idUtilizador]);
        if ($nomeUtilizador) {
            registar_log('eliminar', "Utilizador desativado: {$nomeUtilizador}.", $_SESSION['id_utilizador'] ?? null);
        }
        header('Location: listar.php');
        exit;
    }

    // 3. Caso contrário, mostra a confirmação com os dados reais do utilizador
    $stmt = $ligacao->prepare("SELECT * FROM utilizadores WHERE id_utilizador = :id");
    $stmt->execute([':id' => $idUtilizador]);
    $utilizador = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$utilizador) {
        header('Location: listar.php');
        exit;
    }
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
    registar_log('erro', "Erro ao desativar o utilizador na base de dados.", $_SESSION['id_utilizador'] ?? null);
}
$ligacao = null;
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-12 col-lg-10 col-sm-6">
                <div class="d-flex justify-content-center mt-4">
                    <div class="card w-100 shadow rounded text-center p-4" style="max-width: 700px;">

                        <?php if (!empty($erro_sistema)) : ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($erro_sistema) ?></div>
                        <?php elseif ((int)$utilizador->ativo === 0) : ?>
                            <p class="mb-4 fs-5">Este utilizador já está marcado como <strong>Inativo</strong>.</p>
                            <a href="listar.php" class="btn btn-outline-secondary px-4">
                                <i class="fa-solid fa-arrow-left me-2"></i>Voltar
                            </a>
                        <?php else : ?>

                        <div class="text-warning display-4 mb-3">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>

                        <p class="mb-2 fs-5">Deseja eliminar o utilizador?</p>

                        <h4 class="mb-4"><strong><?= htmlspecialchars($utilizador->nome) ?></strong></h4>

                        <div class="mb-4">
                            <span class="d-block mb-1"><i class="fa-solid fa-at me-2"></i><strong><?= htmlspecialchars($utilizador->email) ?></strong></span>
                            <span class="d-block"><i class="fa-solid fa-id-badge me-2"></i><strong><?= htmlspecialchars($utilizador->perfil) ?></strong></span>
                        </div>

                        <div class="alert alert-warning text-start">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            O utilizador deixa de poder iniciar sessão na aplicação. Pode ser reativado mais tarde a qualquer momento.
                        </div>

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
