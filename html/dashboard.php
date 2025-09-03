<?php
// Protege a página — só acessa se estiver logado
require_once '/var/secure/auth.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        header { background: #007BFF; color: #fff; padding: 15px; }
        header h1 { margin: 0; font-size: 20px; }
        nav { background: #e9ecef; padding: 10px; }
        nav a {
            margin-right: 15px; text-decoration: none; color: #007BFF;
            font-weight: bold;
        }
        nav a:hover { text-decoration: underline; }
        main { padding: 20px; }
    </style>
</head>
<body>
<header>
    <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</h1>
</header>
<nav>
    <a href="listar_usuarios.php">📋 Listar Usuários</a>
    <a href="adicionar_usuario.php">➕ Adicionar Usuário</a>
    <a href="logout.php">🚪 Sair</a>
</nav>
<main>
    <h2>Painel de Controle</h2>
    <p>Escolha uma das opções acima para gerenciar o sistema.</p>
</main>
</body>
</html>