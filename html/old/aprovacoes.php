<?php require_once '/var/secure/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Aprovações Pendentes</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h2>✅ Aprovações Pendentes</h2>
  <table class="table table-striped table-hover">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Data</th>
        <th>Requisitante</th>
        <th>Projeto</th>
        <th>Ação</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $result = $conn->query("SELECT ID, DataSolicitacao, NomeRequisitante, Projeto FROM `SolicitaçõesBackup` WHERE Status='Aberto' ORDER BY DataSolicitacao ASC");
      while($row = $result->fetch_assoc()):
      ?>
      <tr>
        <td><?= $row['ID'] ?></td>
        <td><?= $row['DataSolicitacao'] ?></td>
        <td><?= $row['NomeRequisitante'] ?></td>
        <td><?= $row['Projeto'] ?></td>
        <td><a href="ver_solicitacao.php?id=<?= $row['ID'] ?>" class="btn btn-primary btn-sm">Abrir</a></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <a href="index.php" class="btn btn-secondary">Voltar</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>