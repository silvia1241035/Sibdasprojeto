<?php
require_once 'includes/funcoes.php';
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
if (strlen($password) < 6 || strlen($password) > 12) {
    $validation_errors[] = 'A password deve ter entre 6 e 12 caracteres.';
}

// Se houver erros, guarda na sessão e volta ao login
if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;
    header('Location: ../public/login.php');
    return;
}

// SIMULAÇÃO DE RESULTADO DE LOGIN (substituir por consulta à BD mais tarde)
// 1 = login válido, 0 = inválido
$result['status'] = 1;

// Verifica o resultado
if (!$result['status']) {
    $_SESSION['server_error'] = 'Login inválido';
    header('Location: ../public/login.php');
    return;
}

// LOGIN BEM-SUCEDIDO: guarda o utilizador na sessão
$_SESSION['utilizador'] = $username;
$_SESSION['success_message'] = 'Login efetuado com sucesso!';

// Redireciona para a área privada
header('Location: ../private/index.php');
exit;