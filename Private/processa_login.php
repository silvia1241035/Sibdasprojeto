<?php
require_once __DIR__ . '/includes/funcoes.php';
start_session();

// SEGURANÇA: só aceita acesso via POST (submissão do formulário)
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../public/login.php');
    return;
}

// RECOLHA DE DADOS
$username = isset($_POST['text_username']) ? $_POST['text_username'] : '';
$password = isset($_POST['text_password']) ? $_POST['text_password'] : '';

// VALIDAÇÃO DOS DADOS
$validation_errors = [];

if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    $validation_errors[] = 'O e-mail tem que ser um email válido.';
}
if (strlen($username) < 5 || strlen($username) > 50) {
    $validation_errors[] = 'O e-mail deve ter entre 5 e 50 caracteres.';
}
if (strlen($password) < 8) {
    $validation_errors[] = 'A password deve ter no mínimo 8 caracteres.';
}
if (!preg_match('/[A-Z]/', $password)) {
    $validation_errors[] = 'A password deve conter pelo menos uma letra maiúscula.';
}
if (!preg_match('/[a-z]/', $password)) {
    $validation_errors[] = 'A password deve conter pelo menos uma letra minúscula.';
}
if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
    $validation_errors[] = 'A password deve conter pelo menos um caractere especial.';
}

// Se houver erros, guarda na sessão e volta ao login
if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;
    header('Location: ../public/login.php');
    return;
}

// VERIFICAÇÃO REAL NA BASE DE DADOS
// Procura o utilizador pelo email e confirma a password com password_verify(),
// que compara a password introduzida com o hash guardado (nunca a password em texto simples).
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("SELECT * FROM utilizadores WHERE email = :email");
    $stmt->execute([':email' => $username]);
    $utilizador = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$utilizador || !password_verify($password, $utilizador->password_hash)) {
        $_SESSION['server_error'] = 'Login inválido.';
        header('Location: ../public/login.php');
        return;
    }
} catch (PDOException $err) {
    $_SESSION['server_error'] = 'Erro ao ligar à base de dados.';
    header('Location: ../public/login.php');
    return;
}
$ligacao = null;

// LOGIN BEM-SUCEDIDO: guarda o utilizador na sessão
$_SESSION['utilizador'] = $utilizador->email;
$_SESSION['nome_utilizador'] = $utilizador->nome;
$_SESSION['success_message'] = 'Login efetuado com sucesso!';

// Redireciona para a área privada
header('Location: ../private/index.php');
exit;