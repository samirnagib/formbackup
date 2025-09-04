<?php
// auth.php — proteção para páginas restritas
session_start();

// Se não estiver logado, redireciona para o login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
