<!-- Navbar (topo) -->
<?php
require_once __DIR__ . '/funcoes.php';
start_session();

// Protege: se não houver sessão, volta ao login
if (!check_session()) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}
$nome = $_SESSION['nome_utilizador'] ?? $_SESSION['utilizador'];
$perfil = $_SESSION['perfil'] ?? '';

$mensagensNaoLidas = 0;
if ($perfil === 'Administrador') {
    try {
        $ligacaoNav = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacaoNav->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $mensagensNaoLidas = (int)$ligacaoNav->query("SELECT COUNT(*) FROM mensagens_contacto WHERE lida = 0")->fetchColumn();
    } catch (PDOException $err) {
        // Falha silenciosa — não impede o resto da página de carregar
    }
    $ligacaoNav = null;
}
?>
<header class="container-fluid text-dark topbar fixed-top w-100" style="background-color: #f5f7fa; border-bottom: 2px solid #0077a8;">
    <div class="row align-items-center">
        
        <div class="col-6 d-flex align-items-center p-3">
            
            <a href="<?php echo BASE_URL; ?>/private/index.php">
                <img alt="Logo do InveMed" height="50"
                     src="<?php echo BASE_URL; ?>/assets/img/logo.png" class="me-3">
            </a>
            
            <h2 class="mt-2"><?php echo APP_NAME; ?></h2>
            <?php if (empty($esconder_sidebar)) : ?>
            <button class="btn d-lg-none me-2" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#menuMobile" aria-label="Abrir menu">
            <i class="fa-solid fa-bars" style="color: #0077a8;"></i>
            </button>
            <?php endif; ?>
        </div>

        <div class="col-6 text-end p-3 mb-3 d-flex justify-content-end align-items-center gap-3">
            <?php if ($perfil === 'Administrador') : ?>
            <a href="<?php echo BASE_URL; ?>/private/mensagens.php" class="position-relative text-decoration-none" style="color: #0077a8;" title="Mensagens de Contacto">
                <i class="fa-solid fa-envelope fa-lg"></i>
                <?php if ($mensagensNaoLidas > 0) : ?>
                <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size: 0.6rem;">
                    <?= $mensagensNaoLidas ?>
                </span>
                <?php endif; ?>
            </a>
            <?php endif; ?>
            <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="color: #0077a8; border: 1px solid #0077a8; border-radius: 20px;">
                    <i class="fa-regular fa-user me-2"></i> <?= htmlspecialchars($nome) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text"><i class="fa-solid fa-id-badge me-2" style="color: #0077a8;"></i><?= htmlspecialchars($perfil) ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/private/alterar_password.php"><i class="fa-solid fa-key me-2" style="color: #0077a8;"></i>Alterar password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/public/logout.php"><i class="fa-solid fa-right-from-bracket me-2" style="color: #0077a8;"></i>Sair</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<?php if ($perfil === 'Administrador') : ?>
<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainerLogs" style="z-index: 1080;"></div>
<script>
    window.INVEMED_BASE_URL = "<?= BASE_URL ?>";
    window.INVEMED_NOTIFICACOES_LOGS = true;
</script>
<?php endif; ?>