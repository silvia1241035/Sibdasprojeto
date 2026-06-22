<?php
require_once __DIR__ . '/../private/includes/funcoes.php';
start_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 1. Recolher e validar dados
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

$erros = [];
if (empty($nome)) {
    $erros[] = "O nome é obrigatório.";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = "Indique um endereço de email válido.";
}
if (empty($mensagem)) {
    $erros[] = "A mensagem é obrigatória.";
}

if (!empty($erros)) {
    $_SESSION['contacto_erro'] = implode(' ', $erros);
    $_SESSION['contacto_dados'] = ['nome' => $nome, 'email' => $email, 'mensagem' => $mensagem];
    header('Location: index.php#contacto');
    exit;
}

// 2. Guardar na base de dados
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $ligacao->prepare("INSERT INTO mensagens_contacto (nome, email, mensagem) VALUES (:nome, :email, :mensagem)");
    $stmt->execute([':nome' => $nome, ':email' => $email, ':mensagem' => $mensagem]);
    registar_log('mensagem', "Nova mensagem de contacto de {$nome} ({$email}).");
    $_SESSION['contacto_sucesso'] = "Mensagem enviada com sucesso! Entraremos em contacto em breve.";
} catch (PDOException $err) {
    $_SESSION['contacto_erro'] = "Aconteceu um erro ao enviar a mensagem. Por favor, tenta novamente mais tarde.";
    $_SESSION['contacto_dados'] = ['nome' => $nome, 'email' => $email, 'mensagem' => $mensagem];
    registar_log('erro', "Erro ao gravar mensagem de contacto: " . $err->getMessage());
}
$ligacao = null;

header('Location: index.php#contacto');
exit;
