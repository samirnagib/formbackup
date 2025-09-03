<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

// Busca todos os usuários
$sql = "SELECT id, usuario, nome, email FROM usuarios ORDER BY nome ASC";
$stmt = $conn->query($sql);
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Listar Usuários</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2>📋 Lista de Usuários</h2>
    <a href="adicionar_usuario.php" class="btn btn-success mb-3">➕ Adicionar Usuário</a>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['id']) ?></td>
                <td><?= htmlspecialchars($u['usuario']) ?></td>
                <td><?= htmlspecialchars($u['nome']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <a href="editar_usuario.php?id=<?= $u['id'] ?>" class="btn btn-primary btn-sm">✏️ Editar</a>
                    <a href="excluir_usuario.php?id=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">🗑️ Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Voltar</a>
</div>
</body>
</html>