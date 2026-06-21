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
$fornecedor = null;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
                <div class="card w-100 shadow rounded" style="max-width: 900px;">
                    <div class="card-body">

                        <?php if (!empty($erro_sistema)) : ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($erro_sistema) ?></div>
                        <?php else : ?>

                        <h2 class="mb-4">
                            <strong><i class="fa-solid fa-truck fa-1x mb-3"></i> Detalhes do Fornecedor</strong>
                            <?php if ((int)$fornecedor->ativo === 1) : ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php else : ?>
                                <span class="badge bg-secondary">Inativo</span>
                            <?php endif; ?>
                        </h2>
                        <hr>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome do fornecedor</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($fornecedor->nome) ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">NIF</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($fornecedor->nif) ?></p>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Contacto Telefónico</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($fornecedor->contacto ?? '—') ?></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Email</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($fornecedor->email ?? '—') ?></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Website</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($fornecedor->website ?? '—') ?></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Morada</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($fornecedor->morada ?? '—') ?></p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Pessoa de Contacto</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($fornecedor->pessoa_contacto ?? '—') ?></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Telefone da pessoa de contacto</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($fornecedor->telefone_pessoa ?? '—') ?></p>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Observações</label>
                                    <p class="form-control-plaintext"><?= htmlspecialchars($fornecedor->observacoes ?? '—') ?></p>
                                </div>
                            </div>
                        </div>

                        <?php endif; ?>

                    </div>
                    <div class="d-flex justify-content-end gap-2 p-3 pt-0">
                        <a href="listar.php" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                        <?php if (empty($erro_sistema)) : ?>
                        <a href="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>" class="btn btn-primary">
                            <i class="fa-regular fa-pen-to-square me-1"></i> Editar
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>

    <?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
