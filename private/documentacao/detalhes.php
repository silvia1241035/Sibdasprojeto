<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Técnico', 'Profissional de saúde']);

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
$documento = null;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("
        SELECT d.*, e.codigo_interno, e.designacao AS equipamento_nome, f.nome AS fornecedor_nome
        FROM documentacao d
        JOIN equipamentos e ON e.id_equipamento = d.id_equipamento
        LEFT JOIN fornecedores f ON f.id_fornecedor = d.id_fornecedor
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
}
$ligacao = null;

if (empty($erro_sistema)) {
    if ($documento->validade === null) {
        $estadoValidade = 'sem';
    } elseif (new DateTime($documento->validade) < new DateTime()) {
        $estadoValidade = 'expirado';
    } else {
        $estadoValidade = 'valido';
    }
}
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1000px;">
                <div class="card-body">

                    <?php if (!empty($erro_sistema)) : ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erro_sistema) ?></div>
                    <?php else : ?>

                    <h2 class="mb-4">
                        <strong><i class="fa-solid fa-file-medical fa-1x mb-3"></i> Detalhes do Documento</strong>
                    </h2>
                    <hr>

                    <!-- Linha 1: Tipo + Nome -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipo de documento</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($documento->tipo) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nome do documento</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($documento->nome) ?></p>
                        </div>
                    </div>

                    <!-- Linha 2: Data + Validade -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data do documento</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($documento->data) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data de validade</label>
                            <p class="form-control-plaintext">
                                <?php if ($estadoValidade === 'sem') : ?>
                                    <span class="text-muted">Sem validade</span>
                                <?php elseif ($estadoValidade === 'expirado') : ?>
                                    <span class="badge bg-danger"><?= htmlspecialchars($documento->validade) ?> — Expirado</span>
                                <?php else : ?>
                                    <span class="badge bg-success"><?= htmlspecialchars($documento->validade) ?> — Válido</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <!-- Linha 3: Equipamento associado + Fornecedor associado -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Equipamento associado</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($documento->codigo_interno . ' - ' . $documento->equipamento_nome) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fornecedor associado</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($documento->fornecedor_nome ?? '—') ?></p>
                        </div>
                    </div>

                    <!-- Linha 4: Ficheiro -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Ficheiro</label>
                            <p class="form-control-plaintext">
                                <?php if (!empty($documento->caminho_ficheiro)) : ?>
                                    <a href="<?= htmlspecialchars($documento->caminho_ficheiro) ?>" target="_blank" style="color:#0077a8;text-decoration:none;">
                                        <i class="fa-solid fa-file-arrow-down me-1"></i><?= htmlspecialchars(basename($documento->caminho_ficheiro)) ?>
                                    </a>
                                <?php else : ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </p>
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
