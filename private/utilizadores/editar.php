<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$perfisDisponiveis = ['Administrador', 'Técnico', 'Gestor de Logística', 'Profissional de saúde'];

// 1. Recolher e validar o ID encriptado vindo do URL (GET) ou do formulário (POST)
$idEncrypted = $_GET['id'] ?? $_POST['id'] ?? null;
$idUtilizador = aes_decrypt($idEncrypted);

if (!$idUtilizador || !is_numeric($idUtilizador)) {
    header('Location: listar.php');
    exit;
}

// A editar a própria conta: o perfil não pode ser alterado por esta via, para evitar
// que o administrador se bloqueie a si próprio acidentalmente fora da área restrita.
$aEditarPropriaConta = (int)$idUtilizador === (int)($_SESSION['id_utilizador'] ?? 0);

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

// 2. Obter o utilizador atual
$utilizador = null;
if (empty($erro_sistema)) {
    try {
        $stmt = $ligacao->prepare("SELECT * FROM utilizadores WHERE id_utilizador = :id");
        $stmt->execute([':id' => $idUtilizador]);
        $utilizador = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$utilizador) {
            header('Location: listar.php');
            exit;
        }
        // Um utilizador inativo não pode ser editado — primeiro tem de ser reativado.
        if ((int)$utilizador->ativo === 0) {
            header('Location: detalhes.php?id=' . $idEncrypted);
            exit;
        }
    } catch (PDOException $err) {
        $erro_sistema = "Aconteceu um erro na ligação.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erro_sistema)) {
    // 3. Recolher dados do formulário
    $nome     = trim($_POST['nome_utilizador'] ?? '');
    $email    = trim($_POST['email_utilizador'] ?? '');
    $perfil   = $aEditarPropriaConta ? $utilizador->perfil : trim($_POST['perfil_utilizador'] ?? '');
    $pass     = trim($_POST['password_utilizador'] ?? '');
    $passConf = trim($_POST['confirmar_password_utilizador'] ?? '');

    // 4. Validar dados
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

    if (!$aEditarPropriaConta && (empty($perfil) || !in_array($perfil, $perfisDisponiveis, true))) {
        $erros[] = "Selecione um perfil válido.";
    }

    // A password só é alterada se o admin preencher o campo — caso contrário mantém-se a atual.
    $alterarPassword = $pass !== '' || $passConf !== '';
    if ($alterarPassword) {
        if (strlen($pass) < 6) {
            $erros[] = "A nova password deve ter pelo menos 6 caracteres.";
        } elseif ($pass !== $passConf) {
            $erros[] = "A confirmação da password não corresponde à password indicada.";
        }
    }

    // 5. Normalizar dados
    $nome  = ucwords(strtolower($nome));
    $email = strtolower($email);

    // 6. Atualizar na base de dados
    if (empty($erros)) {
        try {
            if ($alterarPassword) {
                $sql = "UPDATE utilizadores SET nome = :nome, email = :email, perfil = :perfil, password_hash = :password_hash WHERE id_utilizador = :id";
                $params = [
                    ':nome'          => $nome,
                    ':email'         => $email,
                    ':perfil'        => $perfil,
                    ':password_hash' => password_hash($pass, PASSWORD_DEFAULT),
                    ':id'            => $idUtilizador,
                ];
            } else {
                $sql = "UPDATE utilizadores SET nome = :nome, email = :email, perfil = :perfil WHERE id_utilizador = :id";
                $params = [
                    ':nome'   => $nome,
                    ':email'  => $email,
                    ':perfil' => $perfil,
                    ':id'     => $idUtilizador,
                ];
            }
            $stmt = $ligacao->prepare($sql);
            $stmt->execute($params);
            registar_log('editar', "Utilizador atualizado: {$nome} ({$email}), perfil {$perfil}.", $_SESSION['id_utilizador'] ?? null);
            if ($aEditarPropriaConta) {
                $_SESSION['nome_utilizador'] = $nome;
            }
            header('Location: listar.php');
            exit;
        } catch (PDOException $err) {
            if ($err->getCode() == 23000) {
                $erro_sistema = "Já existe um utilizador registado com este email.";
            } else {
                $erro_sistema = "Erro ao atualizar os dados: " . $err->getMessage();
            }
            registar_log('erro', "Erro ao atualizar o utilizador na base de dados.", $_SESSION['id_utilizador'] ?? null);
        }
    }
}

$ligacao = null;

// Valor a apresentar em cada campo: o que foi submetido (em caso de erro) ou o valor atual na BD
function valorCampo($postKey, $utilizador, $campoBd)
{
    return $_POST[$postKey] ?? ($utilizador->$campoBd ?? '');
}
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-12 col-lg-10 col-sm-6">

        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 900px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-pen fa-1x mb-3"></i> Atualizar utilizador</strong></h2>
                    <hr>

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

                    <?php if ($aEditarPropriaConta) : ?>
                    <div class="alert alert-info mb-4">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        Está a editar a sua própria conta — o perfil não pode ser alterado aqui.
                    </div>
                    <?php endif; ?>

                    <form action="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>" method="post" novalidate id="formUtilizador">

                        <!-- Área de erros — validação no browser -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao atualizar o utilizador. Por favor, tente novamente.
                        </div>

                        <!-- Linha 1: Nome + Email -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="texto_nome" class="form-label">Nome<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="texto_nome" name="nome_utilizador" required value="<?= htmlspecialchars(valorCampo('nome_utilizador', $utilizador, 'nome')) ?>">
                                <div class="invalid-feedback">Por favor, insira o nome do utilizador.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="texto_email" class="form-label">Email<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="email" class="form-control" id="texto_email" name="email_utilizador" required value="<?= htmlspecialchars(valorCampo('email_utilizador', $utilizador, 'email')) ?>">
                                <div class="invalid-feedback">Por favor, insira um email válido.</div>
                            </div>
                        </div>

                        <!-- Linha 2: Perfil -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="select_perfil" class="form-label">Perfil<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <select class="form-select" id="select_perfil" name="perfil_utilizador" required <?= $aEditarPropriaConta ? 'disabled' : '' ?>>
                                    <?php foreach ($perfisDisponiveis as $opcao) : ?>
                                        <option value="<?= htmlspecialchars($opcao) ?>" <?= valorCampo('perfil_utilizador', $utilizador, 'perfil') === $opcao ? 'selected' : '' ?>><?= htmlspecialchars($opcao) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($aEditarPropriaConta) : ?>
                                <input type="hidden" name="perfil_utilizador" value="<?= htmlspecialchars($utilizador->perfil) ?>">
                                <?php endif; ?>
                                <div class="invalid-feedback">Por favor, selecione um perfil.</div>
                            </div>
                        </div>

                        <!-- Linha 3: Nova password (opcional) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="texto_password" class="form-label">Nova password</label>
                                <input type="password" class="form-control" id="texto_password" name="password_utilizador" minlength="6" placeholder="Deixe em branco para manter a atual">
                            </div>
                            <div class="col-md-6">
                                <label for="texto_confirmar_password" class="form-label">Confirmar nova password</label>
                                <input type="password" class="form-control" id="texto_confirmar_password" name="confirmar_password_utilizador" minlength="6" placeholder="Repita a nova password">
                                <div class="invalid-feedback">As passwords não coincidem.</div>
                            </div>
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
                                <button type="submit" class="btn btn-primary" id="btnGuardar">
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
    var passwordInput = document.getElementById('texto_password');
    var confirmarInput = document.getElementById('texto_confirmar_password');

    function validarConfirmacao() {
        if (confirmarInput.value !== '' && confirmarInput.value !== passwordInput.value) {
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

<?php include '../includes/footer.php'; ?>
