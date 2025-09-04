<?php
require_once '/var/secure/auth.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Painel Administrativo</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1>⚙️ Painel Administrativo</h1>
    <p>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?>!</p>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">👥 Administração de Usuários</div>
                <div class="card-body">
                    <p>Gerencie contas de acesso ao sistema.</p>
                    <a href="listar_usuarios.php" class="btn btn-primary">Acessar</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white">💾 Solicitações de Backup</div>
                <div class="card-body">
                    <p>Visualize e gerencie solicitações enviadas pelo formulário público.</p>
                    <a href="listar_solicitacoes.php" class="btn btn-success">Acessar</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-info mb-3">
                <div class="card-header bg-info text-black">📋 Auditoria</div>
                <div class="card-body">
                    <p>Visualize e gerencie os logs de auditoria.</p>
                    <a href="auditoria.php" class="btn btn-success">Acessar</a>
                </div>
            </div>
        </div>
    </div>

    <a href="logout.php" class="btn btn-danger">Sair</a>
</div>
</body>
</html>