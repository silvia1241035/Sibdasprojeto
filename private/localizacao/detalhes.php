<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Gestor de Logística', 'Profissional de saúde']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// 1. Recolher e validar o ID encriptado
$idEncrypted = $_GET['id'] ?? null;
$idLocalizacao = aes_decrypt($idEncrypted);

if (!$idLocalizacao || !is_numeric($idLocalizacao)) {
    header('Location: listar.php');
    exit;
}

$erro_sistema = '';
$localizacao = null;
$equipamentos = [];
$perfil = $_SESSION['perfil'] ?? '';

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("SELECT * FROM localizacoes WHERE id_localizacao = :id");
    $stmt->execute([':id' => $idLocalizacao]);
    $localizacao = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$localizacao) {
        header('Location: listar.php');
        exit;
    }

    $stmt = $ligacao->prepare("
        SELECT id_equipamento, codigo_interno, designacao, marca, modelo, estado
        FROM equipamentos
        WHERE id_localizacao = :id
        ORDER BY codigo_interno
    ");
    $stmt->execute([':id' => $idLocalizacao]);
    $equipamentos = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}
$ligacao = null;

$badgeEstado = [
    'Ativo'           => 'badge-estado-ativo',
    'Em manutenção'   => 'badge-estado-manutencao',
    'Inativo'         => 'badge-estado-inativo',
    'Em calibração'   => 'badge-estado-calibracao',
    'Em quarentena'   => 'badge-estado-quarentena',
    'Abatido'         => 'badge-estado-abatido',
];
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
                        <strong><i class="fa-solid fa-map-location-dot fa-1x mb-3"></i> Detalhes da Localização</strong>
                    </h2>
                    <hr>

                    <!-- Linha 1: Edifício + Piso -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Edifício</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($localizacao->edificio) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Piso</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($localizacao->piso ?? '—') ?></p>
                        </div>
                    </div>

                    <!-- Linha 2: Serviço/Departamento + Sala/Gabinete -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Serviço/Departamento</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($localizacao->servico) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sala/Gabinete</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($localizacao->sala ?? '—') ?></p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Equipamentos nesta localização (a relação) -->
                    <h5 class="mb-3">
                        <i class="fa-solid fa-laptop-medical me-2" style="color:#0077a8;"></i>
                        Equipamentos nesta localização
                        <span class="badge bg-primary ms-1"><?= count($equipamentos) ?></span>
                    </h5>

                    <?php if (empty($equipamentos)) : ?>
                        <p class="text-center text-muted mt-3">
                            <i class="fa-solid fa-circle-info me-2"></i>Esta localização não tem equipamentos associados.
                        </p>
                    <?php else : ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Código</th>
                                        <th>Designação</th>
                                        <th>Marca / Modelo</th>
                                        <th>Estado</th>
                                        <?php if (in_array($perfil, ['Administrador', 'Técnico', 'Profissional de saúde'], true)) : ?>
                                        <th class="text-center">Ações</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($equipamentos as $eq) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($eq->codigo_interno) ?></td>
                                        <td><?= htmlspecialchars($eq->designacao) ?></td>
                                        <td><?= htmlspecialchars(trim(($eq->marca ?? '') . ' / ' . ($eq->modelo ?? ''), ' /')) ?: '—' ?></td>
                                        <td><span class="badge <?= $badgeEstado[$eq->estado] ?? 'bg-secondary' ?>"><?= htmlspecialchars($eq->estado) ?></span></td>
                                        <?php if (in_array($perfil, ['Administrador', 'Técnico', 'Profissional de saúde'], true)) : ?>
                                        <td class="text-center">
                                            <a href="../equipamentos/detalhes.php?id=<?= aes_encrypt($eq->id_equipamento) ?>" class="text-decoration-none" style="color:#0077a8;" title="Ver equipamento">
                                                <i class="fa-solid fa-eye me-1"></i>Consultar
                                            </a>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

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

<?php include '../includes/sidebarmobile.php'; ?>

<?php include '../includes/footer.php'; ?>
