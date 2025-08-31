<?php require_once '/var/secure/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Nova Solicitação</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h2>📝 Nova Solicitação</h2>
  <form action="processa_solicitacao.php" method="POST" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Nome do Requisitante</label>
      <input type="text" name="NomeRequisitante" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">E-mail</label>
      <input type="email" name="EmailRequisitante" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Projeto</label>
      <input type="text" name="Projeto" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Ambiente</label>
      <select name="Ambiente" class="form-select" required>
        <option value="">Selecione...</option>
        <option>Produção</option>
        <option>Homologação</option>
        <option>Desenvolvimento</option>
      </select>
    </div>
    <div class="col-12">
      <label class="form-label">Objeto Protegido</label>
      <textarea name="ObjetoProtegido" class="form-control" rows="3" required></textarea>
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-success">Enviar Solicitação</button>
      <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>