<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

// Filtro opcional
$filtro = isset($_GET['acao']) ? $_GET['acao'] : '';

$sql = "SELECT * FROM logs_auditoria";
if ($filtro) {
    $sql .= " WHERE acao LIKE ?";
}

// Supondo que a variável $conn seja uma instância de PDO
$stmt = $conn->prepare($sql);

if ($filtro) {
    // Para PDO, a forma de passar o parâmetro é diferente
    $stmt->bindValue(1, "%" . $filtro . "%");
}
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC); // <== CORREÇÃO AQUI
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<head>
    <title>Auditoria</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">   
        <h2>📋 Logs de Auditoria</h2>
        <form method="get">
            <input type="text" name="acao" placeholder="Filtrar por ação" value="<?= htmlspecialchars($filtro) ?>">
            <button type="submit">Filtrar</button>
            <a href="dashboard.php" class="btn btn-secondary">⬅ Voltar</a>
        </form>
        <table class="table table-bordered table-striped width: 100%; margin-top: 10px;">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Ação</th>
                    <th>ID Solicitação</th>
                    <th>IP</th>
                    <th>User Agent</th>
                    <th>Data/Hora</th>
                </tr>
            </thead>
            <?php foreach ($result as $row): // <== CORREÇÃO AQUI ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['acao']) ?></td>
                <td><?= htmlspecialchars($row['id_solicitacao']) ?></td>
                <td><?= htmlspecialchars($row['ip']) ?></td>
                <td><?= htmlspecialchars($row['user_agent']) ?></td>
                <td><?= htmlspecialchars($row['data_hora']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>