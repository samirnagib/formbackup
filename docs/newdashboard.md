Entendi, Samir — você quer que o **dashboard** central tenha agora **duas áreas de administração**:  
1. **Gerenciar Usuários** (já temos)  
2. **Gerenciar Solicitações de Backup** (vindas do formulário público)  

Vou te entregar o **dashboard.php** reformulado e também os arquivos para listar, visualizar e excluir solicitações de backup, todos já integrados ao mesmo padrão de segurança que usamos antes (`auth.php` + `config.php` + PDO + prepared statements).

---

## 📄 dashboard.php
```php
<?php
require_once '/var/secure/auth.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Painel Administrativo</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1>⚙️ Painel Administrativo</h1>
    <p>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?>!</p>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">👥 Administração de Usuários</div>
                <div class="card-body">
                    <p>Gerencie contas de acesso ao sistema.</p>
                    <a href="listar_usuarios.php" class="btn btn-primary">Acessar</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white">💾 Solicitações de Backup</div>
                <div class="card-body">
                    <p>Visualize e gerencie solicitações enviadas pelo formulário público.</p>
                    <a href="listar_solicitacoes.php" class="btn btn-success">Acessar</a>
                </div>
            </div>
        </div>
    </div>

    <a href="logout.php" class="btn btn-danger">Sair</a>
</div>
</body>
</html>
```

---

## 📄 listar_solicitacoes.php
```php
<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

// Busca todas as solicitações
$sql = "SELECT id, nome, email, data_solicitacao, status FROM solicitacoes_backup ORDER BY data_solicitacao DESC";
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
                <td><?= htmlspecialchars($s['nome']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($s['data_solicitacao'])) ?></td>
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
```

---

## 📄 ver_solicitacao.php
```php
<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM solicitacoes_backup WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$solicitacao = $stmt->fetch();

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
        <li class="list-group-item"><strong>ID:</strong> <?= htmlspecialchars($solicitacao['id']) ?></li>
        <li class="list-group-item"><strong>Nome:</strong> <?= htmlspecialchars($solicitacao['nome']) ?></li>
        <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($solicitacao['email']) ?></li>
        <li class="list-group-item"><strong>Mensagem:</strong> <?= nl2br(htmlspecialchars($solicitacao['mensagem'])) ?></li>
        <li class="list-group-item"><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($solicitacao['data_solicitacao'])) ?></li>
        <li class="list-group-item"><strong>Status:</strong> <?= htmlspecialchars($solicitacao['status']) ?></li>
    </ul>
    <a href="listar_solicitacoes.php" class="btn btn-secondary mt-3">⬅ Voltar</a>
</div>
</body>
</html>
```

---

## 📄 excluir_solicitacao.php
```php
<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM solicitacoes WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

header('Location: listar_solicitacoes.php');
exit;
```

---

### 🗂 Estrutura esperada no banco para `solicitacoes_backup`
```sql
CREATE TABLE solicitacoes_backup (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    mensagem TEXT,
    data_solicitacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'Pendente'
);
```

---

Se quiser, posso já **integrar o formulário público** que grava nessa tabela, para que as solicitações apareçam automaticamente no painel.  
Quer que eu já crie esse formulário público também?
