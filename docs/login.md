Claro, Samir — vou te entregar um **`login.php`** totalmente reescrito, já integrado ao `config.php` unificado com PDO e credenciais criptografadas, seguindo boas práticas de segurança.  

Esse script vai:  
- Usar **prepared statements** para evitar SQL Injection  
- Armazenar/verificar senhas com `password_hash()` e `password_verify()`  
- Iniciar sessão de forma segura  
- Redirecionar para o painel após login bem-sucedido  

---

## 📄 login.php
```php
<?php
// Inicia a sessão antes de qualquer saída
session_start();

// Inclui a configuração segura de conexão
require_once '/var/secure/config.php'; // ajuste o caminho conforme onde salvou

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
```

---

### 🔹 Observações importantes
- **Tabela `usuarios`** deve ter pelo menos:  
  ```sql
  CREATE TABLE usuarios (
      id INT AUTO_INCREMENT PRIMARY KEY,
      usuario VARCHAR(50) UNIQUE NOT NULL,
      nome VARCHAR(100),
      email VARCHAR(100),
      senha_hash VARCHAR(255) NOT NULL
  );
  ```
- Ao criar novos usuários, use:
  ```php
  $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
  ```
- O `dashboard.php` é a página inicial após login; você pode trocar pelo destino que quiser.

---

Se quiser, posso já escrever também o **logout.php** e o **middleware de proteção** para páginas restritas, assim você fecha o ciclo de autenticação com segurança.  
Quer que eu já faça isso agora?
