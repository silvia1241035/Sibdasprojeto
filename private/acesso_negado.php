<?php
require_once __DIR__ . '/includes/funcoes.php';
redirect_if_not_logged();

$perfil = $_SESSION['perfil'] ?? '';
?>

<?php include 'includes/header.php'; ?>

<?php include 'includes/nav.php'; ?>

    <?php include 'includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded text-center p-4" style="max-width: 600px;">

                <div class="text-danger display-4 mb-3">
                    <i class="fa-solid fa-ban"></i>
                </div>

                <p class="mb-2 fs-5">Acesso não autorizado</p>

                <p class="text-muted mb-4">
                    O teu perfil (<strong><?= htmlspecialchars($perfil) ?></strong>) não tem permissão para aceder a esta área da aplicação.
                </p>

                <div class="d-flex justify-content-center">
                    <a href="<?php echo BASE_URL; ?>/private/index.php" class="btn px-4" style="background-color: #0077a8; color: white;">
                        <i class="fa-solid fa-arrow-left me-2"></i>Voltar ao Dashboard
                    </a>
                </div>

            </div>
        </div>
    </main>

    <?php include 'includes/sidebarmobile.php'; ?>

<?php include 'includes/footer.php'; ?>
