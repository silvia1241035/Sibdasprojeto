<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$idEncrypted = $_GET['id'] ?? null;
$idUtilizador = aes_decrypt($idEncrypted);

if (!$idUtilizador || !is_numeric($idUtilizador)) {
    header('Location: listar.php');
    exit;
}

$erro_sistema = '';
$utilizador = null;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $ligacao->prepare("SELECT * FROM utilizadores WHERE id_utilizador = :id");
    $stmt->execute([':id' => $idUtilizador]);
    $utilizador = $stmt->fetch(PDO::FETCH_OBJ);
    if (!$utilizador) {
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

    <main class="col-md-12 col-lg-10 col-sm-6">
            <div class="d-flex justify-content-center mt-4">
                <div class="card w-100 shadow rounded" style="max-width: 900px;">
                    <div class="card-body">

                        <?php if (!empty($erro_sistema)) : ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($erro_sistema) ?></div>
                        <?php else : ?>

                        <h2 class="mb-4">
                            <strong><i class="fa-solid fa-users-gear fa-1x mb-3"></i> Detalhes do Utilizador</strong>
                            <?php if ((int)$utilizador->ativo === 1) : ?>
                                <span class="badge bg-success ms-2">Ativo</span>
                            <?php else : ?>
                                <span class="badge bg-secondary ms-2">Inativo</span>
                            <?php endif; ?>
                        </h2>
                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Nome</p>
                                <p class="fs-5"><strong><?= htmlspecialchars($utilizador->nome) ?></strong></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Email</p>
                                <p class="fs-5"><strong><?= htmlspecialchars($utilizador->email) ?></strong></p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Perfil</p>
                                <p class="fs-5"><strong><?= htmlspecialchars($utilizador->perfil) ?></strong></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Criado em</p>
                                <p class="fs-5"><strong><?= htmlspecialchars(date('d-m-Y H:i', strtotime($utilizador->criado_em))) ?></strong></p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 pt-3 mt-3 border-top">
                            <a href="listar.php" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                            </a>
                            <?php if ((int)$utilizador->ativo === 1) : ?>
                            <a href="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>" class="btn btn-primary">
                                <i class="fa-regular fa-pen-to-square me-1"></i> Editar
                            </a>
                            <?php endif; ?>
                        </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
    </main>

    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
