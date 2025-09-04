<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

// Busca todas as solicitações
$sql = "SELECT id, NomeRequisitante, EmailRequisitante, DataSolicitacao, status FROM solicitacoes ORDER BY DataSolicitacao DESC";
$stmt = $conn->query($sql);
$solicitacoes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Solicitações de Backup</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2>💾 Solicitações de Backup</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Data</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitacoes as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['id']) ?></td>
                <td><?= htmlspecialchars($s['NomeRequisitante']) ?></td>
                <td><?= htmlspecialchars($s['EmailRequisitante']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($s['DataSolicitacao'])) ?></td>
                <td><?= htmlspecialchars($s['status']) ?></td>
                <td>
                    <a href="ver_solicitacao.php?id=<?= $s['id'] ?>" class="btn btn-primary btn-sm">🔍 Ver</a>
                    <a href="excluir_solicitacao.php?id=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir esta solicitação?')">🗑️ Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Voltar</a>
</div>
</body>
</html>