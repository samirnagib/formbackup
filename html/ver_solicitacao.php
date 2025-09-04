<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM solicitacoes WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$solicitacao) {
    die("Solicitação não encontrada.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Detalhes da Solicitação</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2>🔍 Detalhes da Solicitação</h2>
    <ul class="list-group">
        <?php foreach ($solicitacao as $campo => $valor): ?>
            <li class="list-group-item"><strong><?= htmlspecialchars($campo) ?>:</strong> <?= nl2br(htmlspecialchars($valor)) ?></li>
        <?php endforeach; ?>
    </ul>
    <div class="mt-3">
        <a href="acao_solicitacao.php?id=<?= $solicitacao['ID'] ?>&acao=aprovar" class="btn btn-success<?= $desabilitarBotoes ? 'disabled' : '' ?>" 
           <?= $desabilitarBotoes ? 'aria-disabled="true"' : '' ?>>
           Aprovar</a>
        <a href="acao_solicitacao.php?id=<?= $solicitacao['ID'] ?>&acao=rejeitar" class="btn btn-warning<?= $desabilitarBotoes ? 'disabled' : '' ?>" 
           <?= $desabilitarBotoes ? 'aria-disabled="true"' : '' ?>>
        Rejeitar</a>
        <a href="acao_solicitacao.php?id=<?= $solicitacao['ID'] ?>&acao=finalizar" class="btn btn-primary<?= $desabilitarBotoes ? 'disabled' : '' ?>" 
           <?= $desabilitarBotoes ? 'aria-disabled="true"' : '' ?>>
        Finalizar</a>
    </div>
    <a href="listar_solicitacoes.php" class="btn btn-secondary mt-3">⬅ Voltar</a>
</div>
</body>
</html>