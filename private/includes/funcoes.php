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

// Carrega todos os conteúdos editáveis da área pública, indexados por "chave".
// Cada elemento tem ->conteudo (texto) e ->imagem_path (caminho de imagem, quando aplicável).
// Em caso de erro de ligação, devolve um array vazio — quem chama usa sempre um valor por defeito.
function carregar_conteudos(): array
{
    $conteudos = [];
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $linhas = $ligacao->query("SELECT chave, conteudo, imagem_path FROM conteudos_publicos")->fetchAll(PDO::FETCH_OBJ);
        foreach ($linhas as $linha) {
            $conteudos[$linha->chave] = $linha;
        }
    } catch (PDOException $err) {
        // Mantém-se vazio — as páginas que chamam esta função usam sempre um valor por defeito.
    }
    $ligacao = null;
    return $conteudos;
}

// Devolve o texto guardado para uma chave, ou o valor por defeito se não existir.
function conteudo_texto(array $conteudos, string $chave, string $defeito = ''): string
{
    return $conteudos[$chave]->conteudo ?? $defeito;
}

// Devolve o caminho de imagem guardado para uma chave, ou o valor por defeito se não existir.
function conteudo_imagem(array $conteudos, string $chave, string $defeito = ''): string
{
    return $conteudos[$chave]->imagem_path ?? $defeito;
}

// Regista um evento de auditoria na tabela logs (login, logout, inserir, editar,
// eliminar, erro). Uma falha a registar nunca deve impedir a operação principal,
// por isso os erros são sempre engolidos.
function registar_log(string $tipo, ?string $descricao = null, ?int $id_utilizador = null): void
{
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $ligacao->prepare(
            "INSERT INTO logs (tipo, descricao, id_utilizador, ip_address) VALUES (:tipo, :descricao, :id_utilizador, :ip_address)"
        );
        $stmt->execute([
            ':tipo'          => $tipo,
            ':descricao'     => $descricao,
            ':id_utilizador' => $id_utilizador,
            ':ip_address'    => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    } catch (PDOException $err) {
        // Falha silenciosa — o registo de log nunca deve interromper a operação principal.
    }
    $ligacao = null;
}

// Carrega os slides do carrossel da área pública, ordenados.
// Em caso de erro de ligação, devolve um array vazio.
function carregar_slides(): array
{
    $slides = [];
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $slides = $ligacao->query("SELECT * FROM slides_carousel ORDER BY ordem")->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $err) {
        // Mantém-se vazio — quem chama deve lidar com a ausência de slides.
    }
    $ligacao = null;
    return $slides;
}
?>