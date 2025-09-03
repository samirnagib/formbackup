<?php
// Inicia a sessão antes de qualquer saída
session_start();

// Inclui a configuração segura de conexão
require_once '/var/secure/config.php'; // ajuste o caminho conforme onde salvou

$total = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch()['total'];
if ($total == 0) {
    header('Location: criar_admin.php');
    exit;
}

// Se o usuário já estiver logado, redireciona
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitiza entradas
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = trim($_POST['senha'] ?? '');

    if ($usuario === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } else {
        try {
            // Busca usuário no banco
            $sql = "SELECT id, senha_hash FROM usuarios WHERE usuario = :usuario LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':usuario', $usuario);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user && password_verify($senha, $user['senha_hash'])) {
                // Login bem-sucedido
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nome'] = $usuario;

                // Redireciona para o painel
                header('Location: dashboard.php');
                exit;
            } else {
                $erro = 'Usuário ou senha inválidos.';
            }
        } catch (PDOException $e) {
            // Em produção, logar o erro e mostrar mensagem genérica
            error_log("Erro no login: " . $e->getMessage());
            $erro = 'Ocorreu um erro ao processar o login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .login-container {
            width: 300px; margin: 100px auto; padding: 20px;
            background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type=text], input[type=password] {
            width: 100%; padding: 10px; margin: 5px 0 15px;
            border: 1px solid #ccc; border-radius: 4px;
        }
        input[type=submit] {
            width: 100%; padding: 10px; background: #007BFF; color: #fff;
            border: none; border-radius: 4px; cursor: pointer;
        }
        input[type=submit]:hover { background: #0056b3; }
        .erro { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <?php if ($erro): ?>
        <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <label for="usuario">Usuário:</label>
        <input type="text" name="usuario" id="usuario" required>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required>

        <input type="submit" value="Entrar">
    </form>
</div>
</body>
</html>