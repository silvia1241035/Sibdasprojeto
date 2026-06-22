<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
require_perfil(['Administrador', 'Gestor de Logística']);

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$idEncrypted = $_GET['id'] ?? null;
$idFornecedor = aes_decrypt($idEncrypted);

if (!$idFornecedor || !is_numeric($idFornecedor)) {
    header('Location: listar.php');
    exit;
}

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmtNome = $ligacao->prepare("SELECT nome FROM fornecedores WHERE id_fornecedor = :id");
    $stmtNome->execute([':id' => $idFornecedor]);
    $nomeFornecedor = $stmtNome->fetchColumn();

    $stmt = $ligacao->prepare("UPDATE fornecedores SET ativo = 1 WHERE id_fornecedor = :id");
    $stmt->execute([':id' => $idFornecedor]);
    if ($nomeFornecedor) {
        registar_log('editar', "Fornecedor reativado: {$nomeFornecedor}.", $_SESSION['id_utilizador'] ?? null);
    }
} catch (PDOException $err) {
    registar_log('erro', "Erro ao reativar o fornecedor na base de dados.", $_SESSION['id_utilizador'] ?? null);
}
$ligacao = null;

header('Location: listar.php');
exit;
