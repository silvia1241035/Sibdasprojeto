<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador']);

$perfisDisponiveis = ['Administrador', 'Técnico', 'Gestor de Logística', 'Profissional de saúde'];

$erros = [];
$erro_sistema = '';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recolher dados
    $nome    = trim($_POST['nome_utilizador'] ?? '');
    $email   = trim($_POST['email_utilizador'] ?? '');
    $perfil  = trim($_POST['perfil_utilizador'] ?? '');
    $pass    = trim($_POST['password_utilizador'] ?? '');
    $passConf = trim($_POST['confirmar_password_utilizador'] ?? '');

    // 2. Validar dados
    if (empty($nome)) {
        $erros[] = "O campo Nome é obrigatório.";
    } elseif (preg_match('/^\d+$/', $nome)) {
        $erros[] = "O campo Nome não pode conter apenas números.";
    }

    if (empty($email)) {
        $erros[] = "O campo Email é obrigatório.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "O endereço de email não é válido.";
    }

    if (empty($perfil) || !in_array($perfil, $perfisDisponiveis, true)) {
        $erros[] = "Selecione um perfil válido.";
    }

    if (empty($pass)) {
        $erros[] = "O campo Password é obrigatório.";
    } elseif (strlen($pass) < 6) {
        $erros[] = "A password deve ter pelo menos 6 caracteres.";
    } elseif ($pass !== $passConf) {
        $erros[] = "A confirmação da password não corresponde à password indicada.";
    }

    // 3. Normalizar dados
    $nome  = ucwords(strtolower($nome));
    $email = strtolower($email);

    // 4. Guardar na base de dados
    if (empty($erros) && empty($erro_sistema)) {
        try {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $ligacao->prepare("
                INSERT INTO utilizadores (nome, email, perfil, password_hash) VALUES (:nome, :email, :perfil, :password_hash)
            ");
            $stmt->execute([
                ':nome'          => $nome,
                ':email'         => $email,
                ':perfil'        => $perfil,
                ':password_hash' => $hash,
            ]);
            registar_log('inserir', "Utilizador criado: {$nome} ({$email}), perfil {$perfil}.", $_SESSION['id_utilizador'] ?? null);
            header('Location: listar.php');
            exit;
        } catch (PDOException $err) {
            if ($err->getCode() == 23000) {
                $erro_sistema = "Já existe um utilizador registado com este email.";
            } else {
                $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
            }
            $descricaoErro = $err->getCode() == 23000
                ? "Tentativa de inserir utilizador com email já existente."
                : "Erro ao gravar o utilizador na base de dados.";
            registar_log('erro', $descricaoErro, $_SESSION['id_utilizador'] ?? null);
        }
    }
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
                    <h2 class="mb-4"><strong><i class="fa-solid fa-square-plus fa-1x mb-3"></i> Adicionar novo utilizador</strong></h2>
                    <hr>

                    <form action="#" method="post" novalidate id="formUtilizador">

                        <!-- Área de erros de validação / sistema (PHP) -->
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

                        <!-- Área de erros — validação no browser -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao inserir o utilizador. Por favor, tente novamente.
                        </div>

                        <!-- Linha 1: Nome + Email -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="texto_nome" class="form-label">Nome<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="texto_nome" name="nome_utilizador" required placeholder="Ex: João Silva" value="<?= htmlspecialchars($_POST['nome_utilizador'] ?? '') ?>">
                                <div class="invalid-feedback">Por favor, insira o nome do utilizador.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="texto_email" class="form-label">Email<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="email" class="form-control" id="texto_email" name="email_utilizador" required placeholder="Ex: joao.silva@hospital.pt" value="<?= htmlspecialchars($_POST['email_utilizador'] ?? '') ?>">
                                <div class="invalid-feedback">Por favor, insira um email válido.</div>
                            </div>
                        </div>

                        <!-- Linha 2: Perfil -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="select_perfil" class="form-label">Perfil<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <select class="form-select" id="select_perfil" name="perfil_utilizador" required>
                                    <option value="" disabled <?= empty($_POST['perfil_utilizador']) ? 'selected' : '' ?>>Selecione um perfil...</option>
                                    <?php foreach ($perfisDisponiveis as $opcao) : ?>
                                        <option value="<?= htmlspecialchars($opcao) ?>" <?= ($_POST['perfil_utilizador'] ?? '') === $opcao ? 'selected' : '' ?>><?= htmlspecialchars($opcao) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, selecione um perfil.</div>
                            </div>
                        </div>

                        <!-- Linha 3: Password + Confirmar -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="texto_password" class="form-label">Password<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="password" class="form-control" id="texto_password" name="password_utilizador" required minlength="6" placeholder="Mínimo 6 caracteres">
                                <div class="invalid-feedback">A password deve ter pelo menos 6 caracteres.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="texto_confirmar_password" class="form-label">Confirmar password<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="password" class="form-control" id="texto_confirmar_password" name="confirmar_password_utilizador" required minlength="6" placeholder="Repita a password">
                                <div class="invalid-feedback">As passwords não coincidem.</div>
                            </div>
                        </div>

                        <div class="form-text mb-3">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            Esta password é definida por si e deve ser comunicada diretamente ao utilizador. Cada utilizador pode alterá-la depois através de "Alterar a minha password".
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between align-items-center gap-2 pt-3 mt-3 border-top">
                            <small class="text-muted">
                                <span class="text-danger">*</span> campos obrigatórios
                            </small>
                            <div class="d-flex gap-2">
                                <a href="listar.php" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-xmark me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnGuardar" disabled>
                                    <i class="fa-regular fa-floppy-disk me-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/sidebarmobile.php'; ?>

<script>
$(document).ready(function () {
    var form = document.getElementById('formUtilizador');
    var btnGuardar = document.getElementById('btnGuardar');
    var passwordInput = document.getElementById('texto_password');
    var confirmarInput = document.getElementById('texto_confirmar_password');

    function validarConfirmacao() {
        if (confirmarInput.value !== '' && confirmarInput.value !== passwordInput.value) {
            confirmarInput.setCustomValidity('Diferente');
        } else {
            confirmarInput.setCustomValidity('');
        }
    }

    function verificarFormulario() {
        validarConfirmacao();
        btnGuardar.disabled = !form.checkValidity();
    }

    form.addEventListener('input', verificarFormulario);
    verificarFormulario();

    form.addEventListener('submit', function (e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('errorBanner').classList.remove('d-none');
        }
        form.classList.add('was-validated');
    });
});
</script>

<?php include '../includes/footer.php'; ?>
