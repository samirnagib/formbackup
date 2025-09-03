<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

$id = (int)($_GET['id'] ?? 0);

// Busca dados atuais
$stmt = $conn->prepare("SELECT usuario, nome, email FROM usuarios WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$usuarioData = $stmt->fetch();

if (!$usuarioData) {
    die("Usuário não encontrado.");
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if ($nome && $email) {
        if ($senha) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome = :nome, email = :email, senha_hash = :senha WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':senha', $senha_hash);
        } else {
            $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
            $stmt = $conn->prepare($sql);
        }
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: listar_usuarios.php');
        exit;
    } else {
        $erro = 'Preencha todos os campos obrigatórios.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Editar Usuário</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2>✏️ Editar Usuário</h2>
    <?php if ($erro): ?><div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Usuário (não editável)</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($usuarioData['usuario']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuarioData['nome']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuarioData['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nova Senha (opcional)</label>
            <input type="password" name="senha" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="listar_usuarios.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>