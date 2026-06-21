<?php
// Funções reutilizáveis de gestão de sessões
require_once __DIR__ . '/../../config/config.php';
// Inicia a sessão apenas se ainda não estiver iniciada
function start_session()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Verifica se o utilizador está autenticado
function check_session()
{
    return isset($_SESSION['utilizador']);
}

// Redireciona para o login se não houver sessão iniciada
function redirect_if_not_logged($redirect_to = '/public/login.php')
{
    start_session();
    if (!check_session()) {
        header('Location: ' . BASE_URL . $redirect_to);
        exit;
    }
}

// Restringe o acesso a um conjunto de perfis. Deve ser chamada sempre depois
// de redirect_if_not_logged(), já que assume que já existe sessão iniciada.
function require_perfil(array $perfis_permitidos)
{
    if (!in_array($_SESSION['perfil'] ?? null, $perfis_permitidos, true)) {
        header('Location: ' . BASE_URL . '/private/acesso_negado.php');
        exit;
    }
}

// Termina a sessão e redireciona
function logout_and_redirect($redirect_to = '/public/login.php')
{
    start_session();
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . $redirect_to);
    exit;
}

// Encriptação e desencriptação de IDs com OpenSSL, para evitar manipulação direta nos URLs
function aes_encrypt($value)
{
    return bin2hex(openssl_encrypt(
        $value,
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    ));
}

function aes_decrypt($value)
{
    if (!is_string($value) || $value === '' || strlen($value) % 2 !== 0) {
        return false;
    }
    return openssl_decrypt(
        hex2bin($value),
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    );
}
?>