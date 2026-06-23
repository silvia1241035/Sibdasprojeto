<?php
require_once __DIR__ . '/../private/includes/funcoes.php';
start_session();

session_unset();
session_destroy();
header('Location: login.php');
return;