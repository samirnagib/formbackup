<?php require_once '/var/secure/config.php';
$id = intval($_GET['id']);
$sol = $conn->query("SELECT * FROM `SolicitaçõesBackup` WHERE ID=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Detalhes da Solicitação</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h2>📄 Solicitação #<?= $sol['ID'] ?></h2>
  <ul class="list-group mb-3">
    <li class="list-group-item"><strong>Requisitante:</strong> <?= $sol['NomeRequisitante'] ?> (<?= $sol['EmailRequisitante'] ?>)</li>
    <li class="list-group-item