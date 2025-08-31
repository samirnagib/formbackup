<?php require_once '/var/secure/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Sistema de Solicitações de Backup</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">📦 Solicitações de Backup</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="nova_solicitacao.php">📝 Nova Solicitação</a></li>
        <li class="nav-item"><a class="nav-link" href="aprovacoes.php">✅ Aprovações Pendentes</a></li>
        <li class="nav-item"><a class="nav-link" href="consulta.php">🔍 Consultar Solicitações</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="alert alert-info">
    Bem-vindo! Escolha uma das opções no menu acima para continuar.
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>