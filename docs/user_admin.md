Claro, Samir — vou te entregar os quatro arquivos completos, já no padrão que definimos:  
- **Usando o `config.php` unificado** (PDO + credenciais criptografadas)  
- **Protegidos pelo `auth.php`** para que apenas usuários logados acessem  
- **Seguros contra SQL Injection** com `prepare()` e `bindValue()`  
- **Compatíveis com a tabela `usuarios`** que definimos antes  

---

## 📄 listar_usuarios.php
```php
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
```

---

## 📄 adicionar_usuario.php
```php
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
```

---

## 📄 editar_usuario.php
```php
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
```

---

## 📄 excluir_usuario.php
```php
<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

header('Location: listar_usuarios.php');
exit;
```

---

✅ **Todos esses arquivos**:
- Incluem `auth.php` para proteção de acesso  
- Usam `config.php` com PDO e credenciais criptografadas  
- Fazem consultas e alterações com prepared statements  
- Escapam a saída com `htmlspecialchars()` para evitar XSS  

---

Se quiser, posso agora **mont
