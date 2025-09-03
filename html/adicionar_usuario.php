<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $nome    = trim($_POST['nome']);
    $email   = trim($_POST['email']);
    $senha   = $_POST['senha'];

    if ($usuario && $nome && $email && $senha) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (usuario, nome, email, senha_hash) 
                VALUES (:usuario, :nome, :email, :senha)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':senha', $senha_hash);
        $stmt->execute();

        header('Location: listar_usuarios.php');
        exit;
    } else {
        $erro = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Adicionar Usuário</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2>➕ Adicionar Usuário</h2>
    <?php if ($erro): ?><div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Usuário</label>
            <input type="text" name="usuario" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="listar_usuarios.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>