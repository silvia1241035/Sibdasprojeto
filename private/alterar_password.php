<?php
require_once __DIR__ . '/includes/funcoes.php';
redirect_if_not_logged();

$erros = [];
$erro_sistema = '';
$sucesso = false;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $err) {
    $erro_sistema = "Aconteceu um erro na ligação.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erro_sistema)) {
    $passAtual = trim($_POST['password_atual'] ?? '');
    $passNova  = trim($_POST['password_nova'] ?? '');
    $passConf  = trim($_POST['confirmar_password_nova'] ?? '');

    try {
        $stmt = $ligacao->prepare("SELECT password_hash FROM utilizadores WHERE id_utilizador = :id");
        $stmt->execute([':id' => $_SESSION['id_utilizador'] ?? 0]);
        $hashAtual = $stmt->fetchColumn();

        if (empty($passAtual) || !$hashAtual || !password_verify($passAtual, $hashAtual)) {
            $erros[] = "A password atual indicada está incorreta.";
        }

        if (empty($passNova)) {
            $erros[] = "O campo Nova password é obrigatório.";
        } elseif (strlen($passNova) < 6) {
            $erros[] = "A nova password deve ter pelo menos 6 caracteres.";
        } elseif ($passNova !== $passConf) {
            $erros[] = "A confirmação da nova password não corresponde à password indicada.";
        }

        if (empty($erros)) {
            $stmt = $ligacao->prepare("UPDATE utilizadores SET password_hash = :password_hash WHERE id_utilizador = :id");
            $stmt->execute([
                ':password_hash' => password_hash($passNova, PASSWORD_DEFAULT),
                ':id'            => $_SESSION['id_utilizador'] ?? 0,
            ]);
            registar_log('editar', "Utilizador alterou a própria password.", $_SESSION['id_utilizador'] ?? null);
            $sucesso = true;
        }
    } catch (PDOException $err) {
        $erro_sistema = "Aconteceu um erro na ligação.";
        registar_log('erro', "Erro ao alterar a password do utilizador na base de dados.", $_SESSION['id_utilizador'] ?? null);
    }
}
$ligacao = null;
?>

<?php include 'includes/header.php'; ?>

<?php include 'includes/nav.php'; ?>

    <?php include 'includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">

        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 600px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-key fa-1x mb-3"></i> Alterar a minha password</strong></h2>
                    <hr>

                    <?php if ($sucesso) : ?>
                        <div class="alert alert-success mb-0">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            Password alterada com sucesso.
                        </div>
                    <?php else : ?>

                    <?php if (!empty($erros)) : ?>
                    <div class="alert alert-danger mb-4">
                        <strong>Foram encontrados os seguintes erros:</strong>
                        <ul class="mb-0">
                            <?php foreach ($erros as $erro) : ?>
                                <li><?= htmlspecialchars($erro) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($erro_sistema)) : ?>
                    <div class="alert alert-danger mb-4">
                        <strong>Erro:</strong> <?= htmlspecialchars($erro_sistema) ?>
                    </div>
                    <?php endif; ?>

                    <form action="alterar_password.php" method="post" novalidate id="formAlterarPassword">

                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao alterar a password. Por favor, tente novamente.
                        </div>

                        <div class="mb-3">
                            <label for="texto_password_atual" class="form-label">Password atual<span class="text-danger" title="Campo obrigatório">*</span></label>
                            <input type="password" class="form-control" id="texto_password_atual" name="password_atual" required>
                        </div>

                        <div class="mb-3">
                            <label for="texto_password_nova" class="form-label">Nova password<span class="text-danger" title="Campo obrigatório">*</span></label>
                            <input type="password" class="form-control" id="texto_password_nova" name="password_nova" required minlength="6" placeholder="Mínimo 6 caracteres">
                        </div>

                        <div class="mb-3">
                            <label for="texto_confirmar_password_nova" class="form-label">Confirmar nova password<span class="text-danger" title="Campo obrigatório">*</span></label>
                            <input type="password" class="form-control" id="texto_confirmar_password_nova" name="confirmar_password_nova" required minlength="6" placeholder="Repita a nova password">
                            <div class="invalid-feedback">As passwords não coincidem.</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 pt-3 mt-3 border-top">
                            <small class="text-muted">
                                <span class="text-danger">*</span> campos obrigatórios
                            </small>
                            <button type="submit" class="btn btn-primary" id="btnGuardar">
                                <i class="fa-regular fa-floppy-disk me-1"></i> Guardar
                            </button>
                        </div>
                    </form>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/sidebarmobile.php'; ?>

<script>
$(document).ready(function () {
    var form = document.getElementById('formAlterarPassword');
    if (!form) {
        return;
    }
    var novaInput = document.getElementById('texto_password_nova');
    var confirmarInput = document.getElementById('texto_confirmar_password_nova');

    function validarConfirmacao() {
        if (confirmarInput.value !== '' && confirmarInput.value !== novaInput.value) {
            confirmarInput.setCustomValidity('Diferente');
        } else {
            confirmarInput.setCustomValidity('');
        }
    }

    form.addEventListener('input', validarConfirmacao);

    form.addEventListener('submit', function (e) {
        validarConfirmacao();
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('errorBanner').classList.remove('d-none');
        }
        form.classList.add('was-validated');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
