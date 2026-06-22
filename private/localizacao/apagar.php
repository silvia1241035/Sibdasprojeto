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
$idLocalizacao = aes_decrypt($idEncrypted);

if (!$idLocalizacao || !is_numeric($idLocalizacao)) {
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
        $stmt = $ligacao->prepare("UPDATE localizacoes SET ativo = 0 WHERE id_localizacao = :id");
        $stmt->execute([':id' => $idLocalizacao]);
        header('Location: listar.php');
        exit;
    }

    // 3. Caso contrário, mostra a confirmação com os dados reais da localização
    $stmt = $ligacao->prepare("SELECT * FROM localizacoes WHERE id_localizacao = :id");
    $stmt->execute([':id' => $idLocalizacao]);
    $localizacao = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$localizacao) {
        header('Location: listar.php');
        exit;
    }

    // Aviso: equipamentos que ainda apontam para esta localização não são
    // desassociados — continuam a referir-se a uma localização inativa.
    $stmt = $ligacao->prepare("SELECT COUNT(*) FROM equipamentos WHERE id_localizacao = :id");
    $stmt->execute([':id' => $idLocalizacao]);
    $nEquipamentos = (int)$stmt->fetchColumn();
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
                <?php elseif ((int)$localizacao->ativo === 0) : ?>
                    <p class="mb-4 fs-5">Esta localização já está marcada como <strong>Inativa</strong>.</p>
                    <a href="listar.php" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-arrow-left me-2"></i>Voltar
                    </a>
                <?php else : ?>

                <div class="text-warning display-4 mb-3">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <p class="mb-2 fs-5">Deseja desativar a localização?</p>

                <h4 class="mb-4"><strong><?= htmlspecialchars($localizacao->edificio) ?></strong></h4>

                <div class="mb-4">
                    <span class="d-block mb-1"><i class="fa-solid fa-hospital fa-at me-2"></i><strong><?= htmlspecialchars($localizacao->servico) ?></strong></span>
                    <span class="d-block"><i class="fa-solid fa-door-open me-2"></i><strong><?= htmlspecialchars($localizacao->sala ?? '—') ?></strong></span>
                </div>

                <?php if ($nEquipamentos > 0) : ?>
                <div class="alert alert-warning text-start">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    Existe(m) <strong><?= $nEquipamentos ?></strong> equipamento(s) ainda associado(s) a esta localização.
                    Desativá-la não os remove nem os move — apenas deixa de aparecer como opção para novos registos.
                </div>
                <?php endif; ?>

                <p class="text-muted small mb-4">
                    A localização não é eliminada — fica marcada como inativa e pode ser reativada mais tarde.
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
