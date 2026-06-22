<?php
require_once __DIR__ . '/../private/includes/funcoes.php';
start_session();

if (isset($_SESSION['id_utilizador'])) {
    registar_log('logout', "Logout efetuado por {$_SESSION['nome_utilizador']}.", $_SESSION['id_utilizador']);
}

session_unset();
session_destroy();
header('Location: login.php');
return;