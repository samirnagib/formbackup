<?php
require_once '/var/secure/config.php';

// Busca todas as solicitações
$sql = "SELECT id, solicitante, email, descricao, status, comentario, data_criacao, data_decisao 
        FROM solicitacoes ORDER BY data_criacao DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Solicitações de Backup</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-4">Solicitações de Backup</h1>

    <!-- Formulário de nova solicitação -->
    <div class="card mb-4">
        <div class="card-header">Nova Solicitação</div>
        <div class="card-body">
            <form action="processa_solicitacao.php" method="POST">
                <div class="mb-3">
                    <label for="solicitante" class="form-label">Solicitante</label>
                    <input type="text" name="solicitante" id="solicitante" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea name="descricao" id="descricao" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Enviar Solicitação</button>
            </form>
        </div>
    </div>

    <!-- Lista de solicitações -->
    <div class="card">
        <div class="card-header">Solicitações Registradas</div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Solicitante</th>
                        <th>E-mail</th>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th>Comentário</th>
                        <th>Data Criação</th>
                        <th>Data Decisão</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['solicitante']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['descricao']) ?></td>
                            <td>
                                <?php
                                    $badgeClass = match($row['status']) {
                                        'Aprovado' => 'success',
                                        'Rejeitado' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= $row['status'] ?></span>
                            </td>
                            <td><?= htmlspecialchars($row['comentario'] ?? '') ?></td>
                            <td><?= $row['data_criacao'] ?></td>
                            <td><?= $row['data_decisao'] ?? '-' ?></td>
                            <td>
                                <?php if ($row['status'] === 'Pendente'): ?>
                                    <form action="processa_aprovacao.php" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="acao" value="aprovar">
                                        <input type="hidden" name="comentario" value="">
                                        <button type="submit" class="btn btn-sm btn-success">Aprovar</button>
                                    </form>
                                    <form action="processa_aprovacao.php" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="acao" value="rejeitar">
                                        <input type="hidden" name="comentario" value="">
                                        <button type="submit" class="btn btn-sm btn-danger">Rejeitar</button>
                                    </form>
                                <?php else: ?>
                                    <em>—</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center">Nenhuma solicitação encontrada.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>