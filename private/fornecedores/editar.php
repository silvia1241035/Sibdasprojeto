<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Gestor de Logística']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// 1. Recolher e validar o ID encriptado vindo do URL (GET) ou do formulário (POST)
$idEncrypted = $_GET['id'] ?? $_POST['id'] ?? null;
$idFornecedor = aes_decrypt($idEncrypted);

if (!$idFornecedor || !is_numeric($idFornecedor)) {
    header('Location: listar.php');
    exit;
}

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

// 2. Obter o fornecedor atual — feito antes do POST porque o NIF é imutável
// (identificador fiscal/legal da empresa) e o seu valor real, já guardado na
// BD, é o que vai ser usado no UPDATE, independentemente do que vier no POST.
$fornecedor = null;
if (empty($erro_sistema)) {
    try {
        $stmt = $ligacao->prepare("SELECT * FROM fornecedores WHERE id_fornecedor = :id");
        $stmt->execute([':id' => $idFornecedor]);
        $fornecedor = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$fornecedor) {
            header('Location: listar.php');
            exit;
        }
        // Um fornecedor inativo não pode ser editado — primeiro tem de ser reativado.
        if ((int)$fornecedor->ativo === 0) {
            header('Location: detalhes.php?id=' . $idEncrypted);
            exit;
        }
    } catch (PDOException $err) {
        $erro_sistema = "Aconteceu um erro na ligação.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erro_sistema)) {
    // 3. Recolher dados do formulário (o NIF não é recolhido do POST — é imutável)
    $nome      = trim($_POST['nome_fornecedor'] ?? '');
    $contacto  = trim($_POST['contacto_fornecedor'] ?? '');
    $email     = trim($_POST['email_fornecedor'] ?? '');
    $website   = trim($_POST['website_fornecedor'] ?? '');
    $morada    = trim($_POST['morada_fornecedor'] ?? '');
    $pessoa    = trim($_POST['pessoa_fornecedor'] ?? '');
    $telPessoa = trim($_POST['telefone_pessoa_fornecedor'] ?? '');
    $obs       = trim($_POST['observacoes_fornecedor'] ?? '');

    // 4. Validar dados (mesmas regras do inserir.php, exceto o NIF)
    if (empty($nome)) {
        $erros[] = "O campo Nome do fornecedor é obrigatório.";
    } elseif (preg_match('/^\d+$/', $nome)) {
        $erros[] = "O campo Nome do fornecedor não pode conter apenas números.";
    }

    if (!empty($contacto) && !preg_match('/^\d{9}$/', preg_replace('/\s+/', '', $contacto))) {
        $erros[] = "O contacto telefónico deve conter 9 dígitos.";
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "O endereço de email não é válido.";
    }

    if (!empty($telPessoa) && !preg_match('/^\d{9}$/', preg_replace('/\s+/', '', $telPessoa))) {
        $erros[] = "O telefone da pessoa de contacto deve conter 9 dígitos.";
    }

    if (!empty($website) && !preg_match('/^(https?:\/\/)?([\w-]+\.)+[a-zA-Z]{2,}(\/.*)?$/', $website)) {
        $erros[] = "O website indicado não é válido (ex: https://www.exemplo.com).";
    }

    if (empty($email) && empty($contacto)) {
        $erros[] = "É necessário indicar pelo menos um meio de contacto (email ou contacto telefónico).";
    }

    // 5. Normalizar dados
    $nome      = ucwords(strtolower($nome));
    $email     = strtolower($email);
    $pessoa    = $pessoa !== '' ? ucwords(strtolower($pessoa)) : null;
    $contacto  = $contacto !== '' ? preg_replace('/\s+/', '', $contacto) : null;
    $telPessoa = $telPessoa !== '' ? preg_replace('/\s+/', '', $telPessoa) : null;
    $website   = $website !== '' ? $website : null;
    $morada    = $morada !== '' ? $morada : null;
    $email     = $email !== '' ? $email : null;
    $obs       = $obs !== '' ? $obs : null;

    // 6. Atualizar na base de dados (nif mantém-se sempre o valor já guardado)
    if (empty($erros)) {
        try {
            $sql = "UPDATE fornecedores SET
                        nome = :nome, contacto = :contacto, email = :email,
                        website = :website, morada = :morada, pessoa_contacto = :pessoa_contacto,
                        telefone_pessoa = :telefone_pessoa, observacoes = :observacoes
                    WHERE id_fornecedor = :id";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':nome'            => $nome,
                ':contacto'        => $contacto,
                ':email'           => $email,
                ':website'         => $website,
                ':morada'          => $morada,
                ':pessoa_contacto' => $pessoa,
                ':telefone_pessoa' => $telPessoa,
                ':observacoes'     => $obs,
                ':id'              => $idFornecedor,
            ]);
            header('Location: listar.php');
            exit;
        } catch (PDOException $err) {
            $erro_sistema = "Erro ao atualizar os dados: " . $err->getMessage();
        }
    }
}

$ligacao = null;

// Valor a apresentar em cada campo: o que foi submetido (em caso de erro) ou o valor atual na BD
function valorCampo($postKey, $fornecedor, $campoBd)
{
    return $_POST[$postKey] ?? ($fornecedor->$campoBd ?? '');
}
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/nav.php'; ?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="col-md-9 col-lg-10 p-4">

        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow rounded" style="max-width: 1200px;">
                <div class="card-body">
                    <h2 class="mb-4"><strong><i class="fa-solid fa-pen fa-1x mb-3"></i> Atualizar fornecedor</strong></h2>
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

                    <form action="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>" method="post" novalidate id="formFornecedor">

                        <!-- Área de erros — validação no browser -->
                        <div class="alert alert-danger d-none mb-4" id="errorBanner" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            Erro ao atualizar o fornecedor. Por favor, tente novamente.
                        </div>

                        <!-- Linha 1: Nome + NIF -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="texto_nome" class="form-label">Nome do fornecedor<span class="text-danger" title="Campo obrigatório">*</span></label>
                                <input type="text" class="form-control" id="texto_nome" name="nome_fornecedor" required placeholder="Ex: MedTech Solutions" value="<?= htmlspecialchars(valorCampo('nome_fornecedor', $fornecedor, 'nome')) ?>">
                                <div class="invalid-feedback">Por favor, insira o nome do fornecedor.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="texto_nif" class="form-label">NIF</label>
                                <input type="text" class="form-control" id="texto_nif" readonly value="<?= htmlspecialchars($fornecedor->nif ?? '') ?>">
                                <div class="form-text">O NIF é o identificador fiscal da empresa.</div>
                            </div>
                        </div>

                        <!-- Linha 2: Contacto + Email + Website + Morada -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="texto_contacto" class="form-label">Contacto Telefónico</label>
                                <input type="text" class="form-control" id="texto_contacto" name="contacto_fornecedor" placeholder="Ex: 912345678" value="<?= htmlspecialchars(valorCampo('contacto_fornecedor', $fornecedor, 'contacto')) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="texto_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="texto_email" name="email_fornecedor" placeholder="Ex: contato@medtech.com" value="<?= htmlspecialchars(valorCampo('email_fornecedor', $fornecedor, 'email')) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="texto_website" class="form-label">Website</label>
                                <input type="text" class="form-control" id="texto_website" name="website_fornecedor" placeholder="Ex: https://www.medtech.com" value="<?= htmlspecialchars(valorCampo('website_fornecedor', $fornecedor, 'website')) ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="texto_morada" class="form-label">Morada</label>
                                <input type="text" class="form-control" id="texto_morada" name="morada_fornecedor" placeholder="Ex: Rua Exemplo, 123" value="<?= htmlspecialchars(valorCampo('morada_fornecedor', $fornecedor, 'morada')) ?>">
                            </div>
                        </div>

                        <!-- Linha 3: Pessoa + Telefone -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="texto_pessoa" class="form-label">Pessoa de Contacto</label>
                                <input type="text" class="form-control" id="texto_pessoa" name="pessoa_fornecedor" placeholder="Ex: João Silva" value="<?= htmlspecialchars(valorCampo('pessoa_fornecedor', $fornecedor, 'pessoa_contacto')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="texto_pessoa_telefone" class="form-label">Telefone da pessoa de contacto</label>
                                <input type="text" class="form-control" id="texto_pessoa_telefone" name="telefone_pessoa_fornecedor" placeholder="Ex: 912345678" value="<?= htmlspecialchars(valorCampo('telefone_pessoa_fornecedor', $fornecedor, 'telefone_pessoa')) ?>">
                            </div>
                        </div>

                        <!-- Linha 4: Observações -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="texto_observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="texto_observacoes" name="observacoes_fornecedor" rows="3" placeholder="Notas adicionais sobre o fornecedor..."><?= htmlspecialchars(valorCampo('observacoes_fornecedor', $fornecedor, 'observacoes')) ?></textarea>
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

<?php include '../includes/footer.php'; ?>