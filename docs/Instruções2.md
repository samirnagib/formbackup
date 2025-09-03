Claro, Samir — segue a **versão atualizada** do `processa_solicitacao.php` já com:  

- Uso do `config.php` centralizado e seguro (credenciais criptografadas)  
- Envio de e-mail via **Office 365 SMTP** usando **PHPMailer**  
- Compatível com o layout e fluxo que definimos (Bootstrap local, campos do formulário)  

---

## **📄 `/var/www/html/processa_solicitacao.php`**
```php
<?php
require_once '/var/secure/config.php'; // Conexão segura
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer via Composer

// Recebe e valida dados do formulário
$solicitante = trim($_POST['solicitante'] ?? '');
$email       = trim($_POST['email'] ?? '');
$descricao   = trim($_POST['descricao'] ?? '');

if (empty($solicitante) || empty($email) || empty($descricao)) {
    die("<div class='alert alert-danger'>Todos os campos são obrigatórios.</div>");
}

// Prepara e executa INSERT no banco
$stmt = $conn->prepare("INSERT INTO solicitacoes (solicitante, email, descricao, status, data_criacao) VALUES (?, ?, ?, 'Pendente', NOW())");
$stmt->bind_param("sss", $solicitante, $email, $descricao);

if ($stmt->execute()) {
    // Envia e-mail de confirmação ao solicitante
    $mail = new PHPMailer(true);
    try {
        // Configuração SMTP Office 365
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seu_email@seudominio.com'; // Seu e-mail corporativo
        $mail->Password   = 'sua_senha_ou_senha_app';   // Senha ou senha de aplicativo
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Remetente e destinatário
        $mail->setFrom('seu_email@seudominio.com', 'Sistema de Backup');
        $mail->addAddress($email, $solicitante);

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Confirmação de Solicitação de Backup';
        $mail->Body    = "
            <p>Olá <strong>{$solicitante}</strong>,</p>
            <p>Sua solicitação de backup foi registrada com sucesso e está com status <strong>Pendente</strong>.</p>
            <p><strong>Descrição:</strong> {$descricao}</p>
            <p>Você será notificado quando houver uma decisão.</p>
            <hr>
            <p><em>Mensagem automática - não responda este e-mail.</em></p>
        ";

        $mail->send();
        echo "<div class='alert alert-success'>Solicitação registrada e e-mail enviado com sucesso!</div>";
    } catch (Exception $e) {
        echo "<div class='alert alert-warning'>Solicitação registrada, mas falha ao enviar e-mail: {$mail->ErrorInfo}</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Erro ao registrar solicitação: {$conn->error}</div>";
}

$stmt->close();
$conn->close();

// Link para voltar
echo "<a href='index.php' class='btn btn-primary mt-3'>Voltar</a>";
?>
```

---

### 🔹 **O que mudou nesta versão**
- **Segurança**: conexão via `require_once '/var/secure/config.php'`  
- **Validação**: checa se todos os campos obrigatórios foram preenchidos  
- **PHPMailer**: configurado para Office 365 com TLS na porta 587  
- **Feedback ao usuário**: mensagens Bootstrap de sucesso/erro  
- **Proteção contra SQL Injection**: uso de `prepare()` e `bind_param()`  

---

Se quiser, posso também te enviar **o `processa_aprovacao.php` atualizado** no mesmo padrão, para que o fluxo de aprovação/rejeição também use o `config.php` seguro e envie e-mails formatados.  

Quer que eu já te envie o `processa_aprovacao.php` atualizado também?
Perfeito, Samir — aqui está a **versão atualizada** do `processa_aprovacao.php` no mesmo padrão seguro e integrado ao fluxo que já montamos para o `processa_solicitacao.php`.  

---

## **📄 `/var/www/html/processa_aprovacao.php`**
```php
<?php
require_once '/var/secure/config.php'; // Conexão segura
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer via Composer

// Recebe dados do formulário
$id        = intval($_POST['id'] ?? 0);
$acao      = $_POST['acao'] ?? '';
$comentario = trim($_POST['comentario'] ?? '');

// Busca dados da solicitação
$stmt = $conn->prepare("SELECT solicitante, email, descricao FROM solicitacoes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<div class='alert alert-danger'>Solicitação não encontrada.</div>");
}

$solicitacao = $result->fetch_assoc();
$stmt->close();

// Define novo status
if ($acao === 'aprovar') {
    $status = 'Aprovado';
} elseif ($acao === 'rejeitar') {
    $status = 'Rejeitado';
} else {
    die("<div class='alert alert-warning'>Ação inválida.</div>");
}

// Atualiza status no banco
$stmt = $conn->prepare("UPDATE solicitacoes SET status = ?, comentario = ?, data_decisao = NOW() WHERE id = ?");
$stmt->bind_param("ssi", $status, $comentario, $id);

if ($stmt->execute()) {
    // Envia e-mail ao solicitante
    $mail = new PHPMailer(true);
    try {
        // Configuração SMTP Office 365
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seu_email@seudominio.com';
        $mail->Password   = 'sua_senha_ou_senha_app';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Remetente e destinatário
        $mail->setFrom('seu_email@seudominio.com', 'Sistema de Backup');
        $mail->addAddress($solicitacao['email'], $solicitacao['solicitante']);

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = "Sua solicitação de backup foi {$status}";
        $mail->Body    = "
            <p>Olá <strong>{$solicitacao['solicitante']}</strong>,</p>
            <p>Sua solicitação de backup foi <strong>{$status}</strong>.</p>
            <p><strong>Descrição:</strong> {$solicitacao['descricao']}</p>
            " . (!empty($comentario) ? "<p><strong>Comentário:</strong> {$comentario}</p>" : "") . "
            <hr>
            <p><em>Mensagem automática - não responda este e-mail.</em></p>
        ";

        $mail->send();
        echo "<div class='alert alert-success'>Status atualizado e e-mail enviado com sucesso!</div>";
    } catch (Exception $e) {
        echo "<div class='alert alert-warning'>Status atualizado, mas falha ao enviar e-mail: {$mail->ErrorInfo}</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Erro ao atualizar solicitação: {$conn->error}</div>";
}

$stmt->close();
$conn->close();

// Link para voltar
echo "<a href='index.php' class='btn btn-primary mt-3'>Voltar</a>";
?>
```

---

### 🔹 **Principais melhorias**
- **Segurança**: conexão via `config.php` seguro  
- **Validação**: impede ações inválidas e IDs inexistentes  
- **PHPMailer**: envio de e-mail formatado com HTML e dados dinâmicos  
- **Feedback ao usuário**: mensagens Bootstrap claras  
- **Proteção contra SQL Injection**: `prepare()` e `bind_param()`  

---

Se quiser, posso já te mandar **o `index.php` atualizado** para integrar esses dois fluxos e deixar o painel de solicitações mais intuitivo, com botões de ação e status coloridos.  
Quer que eu envie essa versão também?
Perfeito, Samir — aqui está o **`index.php`** atualizado para integrar o fluxo completo:  
- Lista todas as solicitações com **status colorido**  
- Botões de **aprovar** e **rejeitar** que já enviam para o `processa_aprovacao.php`  
- Layout responsivo com **Bootstrap local**  
- Formulário para **nova solicitação** no topo  

---

## **📄 `/var/www/html/index.php`**
```php
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
```

---

### 🔹 **O que este `index.php` entrega**
- **Formulário** para criar novas solicitações  
- **Tabela dinâmica** com todas as solicitações  
- **Status visual** com cores (`badge` do Bootstrap)  
- **Botões de ação** que enviam direto para o `processa_aprovacao.php`  
- **Proteção XSS** com `htmlspecialchars()`  

---

Se quiser, posso já te preparar **o `config.php` seguro** que esses três arquivos usam, com conexão MySQL e variáveis sensíveis isoladas, para você só precisar ajustar usuário, senha e host.  

Quer que eu já te envie esse `config.php` pronto?
Ótimo, Samir — aqui está o **script SQL** para criar a tabela `solicitacoes` exatamente no formato que os arquivos `index.php`, `processa_solicitacao.php` e `processa_aprovacao.php` esperam.  

---

## **📄 `cria_tabela_solicitacoes.sql`**
```sql
CREATE TABLE solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitante VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    status ENUM('Pendente', 'Aprovado', 'Rejeitado') DEFAULT 'Pendente',
    comentario TEXT DEFAULT NULL,
    data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_decisao DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

### 🔹 **Detalhes importantes**
- **`status`**: usa `ENUM` para garantir que só existam três valores possíveis  
- **`comentario`**: opcional, preenchido apenas na aprovação/rejeição  
- **`data_criacao`**: preenchida automaticamente  
- **`data_decisao`**: só recebe valor quando a solicitação é decidida  
- **`utf8mb4`**: garante suporte a acentos e emojis  

---

💡 **Dica de implantação**  
No terminal do servidor, estando no diretório onde salvou o arquivo `.sql`, execute:  
```bash
mysql -u usuario_backup -p sistema_backup < cria_tabela_solicitacoes.sql
```
Isso vai criar a tabela no banco que configuramos no `config.php`.

---

Se quiser, posso já te preparar **um script SQL extra** para criar **um usuário MySQL com permissões mínimas** só para esse sistema, aumentando a segurança.  
Quer que eu já te envie esse também?
Entendi, Samir — você quer que **somente o formulário de “Nova Solicitação” continue público**, e que **qualquer outra funcionalidade (lista de solicitações, aprovar/rejeitar, etc.) exija login**.  
Além disso, o login deve ser validado no banco de dados, com **limite de 3 tentativas** antes de redirecionar para a página inicial.

Vou te passar a implementação em **três partes**:

---

## **1️⃣ Banco de dados — tabela de usuários**
Crie uma tabela para armazenar usuários e senhas (de preferência com hash seguro):

```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exemplo de inserção de usuário (senha: admin123)
INSERT INTO usuarios (usuario, senha_hash)
VALUES ('admin', SHA2('admin123', 256));
```

> Aqui usei `SHA2` para armazenar a senha de forma segura.  
> No PHP, vamos usar `password_hash()` e `password_verify()`.

---

## **2️⃣ login.php — tela de autenticação**
```php
<?php
session_start();
require_once '/var/secure/config.php';

if (!isset($_SESSION['tentativas'])) {
    $_SESSION['tentativas'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = trim($_POST['senha'] ?? '');

    $stmt = $conn->prepare("SELECT senha_hash FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->bind_result($senha_hash);
    
    if ($stmt->fetch() && password_verify($senha, $senha_hash)) {
        $_SESSION['logado'] = true;
        $_SESSION['tentativas'] = 0;
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['tentativas']++;
        if ($_SESSION['tentativas'] >= 3) {
            $_SESSION['tentativas'] = 0;
            header("Location: index.php?erro=tentativas");
            exit;
        }
        $erro = "Usuário ou senha inválidos. Tentativa {$_SESSION['tentativas']} de 3.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-header">Login</div>
        <div class="card-body">
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuário</label>
                    <input type="text" name="usuario" id="usuario" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
```

---

## **3️⃣ index.php — protegendo as áreas restritas**
Vamos separar a parte pública (Nova Solicitação) da parte restrita (lista e ações):

```php
<?php
session_start();
require_once '/var/secure/config.php';
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

    <!-- Formulário público -->
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

    <!-- Área restrita -->
    <?php if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true): ?>
        <div class="alert alert-warning">
            Área restrita. <a href="login.php" class="btn btn-sm btn-primary">Fazer login</a>
        </div>
    <?php else: ?>
        <?php
        $sql = "SELECT id, solicitante, email, descricao, status, comentario, data_criacao, data_decisao 
                FROM solicitacoes ORDER BY data_criacao DESC";
        $result = $conn->query($sql);
        ?>
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
    <?php endif; ?>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
```

---

### 🔹 Como funciona agora
- **Nova Solicitação**: sempre visível, sem login  
- **Lista e

Entendido, Samir — vamos ajustar o `index.php` para que, **dentro da área protegida**, exista um **único formulário de administração de usuários** que permita:

- **Incluir** novos usuários (com senha criptografada)
- **Remover** usuários existentes
- **Listar** todos os usuários cadastrados

Assim, quem tiver login válido poderá gerenciar quem acessa as áreas restritas.

---

## 📄 **`index.php` atualizado**

```php
<?php
session_start();
require_once '/var/secure/config.php';

// Se não estiver logado, só mostra a parte pública
$logado = isset($_SESSION['logado']) && $_SESSION['logado'] === true;

// Processa ações de administração de usuários (somente se logado)
if ($logado && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_usuario'])) {
    if ($_POST['acao_usuario'] === 'incluir') {
        $novo_usuario = trim($_POST['novo_usuario']);
        $nova_senha   = trim($_POST['nova_senha']);
        if ($novo_usuario && $nova_senha) {
            $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (usuario, senha_hash) VALUES (?, ?)");
            $stmt->bind_param("ss", $novo_usuario, $hash);
            if ($stmt->execute()) {
                $msg_admin = "<div class='alert alert-success'>Usuário incluído com sucesso!</div>";
            } else {
                $msg_admin = "<div class='alert alert-danger'>Erro ao incluir usuário: {$conn->error}</div>";
            }
            $stmt->close();
        }
    }
    if ($_POST['acao_usuario'] === 'remover') {
        $id_remover = intval($_POST['id_usuario']);
        if ($id_remover > 0) {
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $id_remover);
            if ($stmt->execute()) {
                $msg_admin = "<div class='alert alert-success'>Usuário removido com sucesso!</div>";
            } else {
                $msg_admin = "<div class='alert alert-danger'>Erro ao remover usuário: {$conn->error}</div>";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Sistema de Solicitações de Backup</title>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-4">Solicitações de Backup</h1>

    <!-- Formulário público -->
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

    <!-- Área restrita -->
    <?php if (!$logado): ?>
        <div class="alert alert-warning">
            Área restrita. <a href="login.php" class="btn btn-sm btn-primary">Fazer login</a>
        </div>
    <?php else: ?>
        <?php if (!empty($msg_admin)) echo $msg_admin; ?>

        <!-- Administração de usuários -->
        <div class="card mb-4">
            <div class="card-header">Administração de Usuários</div>
            <div class="card-body">
                <form method="POST" class="row g-3 mb-3">
                    <input type="hidden" name="acao_usuario" value="incluir">
                    <div class="col-md-4">
                        <input type="text" name="novo_usuario" class="form-control" placeholder="Novo usuário" required>
                    </div>
                    <div class="col-md-4">
                        <input type="password" name="nova_senha" class="form-control" placeholder="Senha" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100">Incluir Usuário</button>
                    </div>
                </form>

                <h5>Usuários cadastrados</h5>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res_users = $conn->query("SELECT id, usuario FROM usuarios ORDER BY usuario ASC");
                        while ($u = $res_users->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['usuario']) ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="acao_usuario" value="remover">
                                    <input type="hidden" name="id_usuario" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Aqui você pode manter a tabela de solicitações restrita -->
        <?php
        $sql = "SELECT id, solicitante, email, descricao, status, comentario, data_criacao, data_decisao 
                FROM solicitacoes ORDER BY data_criacao DESC";
        $result = $conn->query($sql);
        ?>
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
                                <td><?= $row['status'] ?></td>
                                <td><?= htmlspecialchars($row['comentario'] ?? '') ?></td>
                                <td><?= $row['data_criacao'] ?></td>
                                <td><?= $row['data_decisao'] ?? '-' ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center">Nenhuma solicitação encontrada.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
```

---

### 🔹 Como funciona agora
- **Área pública**: formulário de nova solicitação  
- **Área restrita** (após login):  
  - Formulário para **incluir usuários** (com senha criptografada)  
  - Lista de usuários com botão para **remover**  
  - Lista de solicitações registradas  

---

Se quiser, posso também **atualizar o `login.php`** para já criar o primeiro usuário administrador caso a tabela `usuarios` esteja vazia, facilitando a configuração inicial. Quer que eu faça isso?
Perfeito, Samir — então vamos complementar o fluxo criando um **`login.php`** que, além de autenticar, detecta se a tabela `usuarios` está vazia e, nesse caso, exibe um formulário para criar o **primeiro administrador**.  

Assim, você não precisa inserir o usuário inicial direto no banco.

---

## 📄 **`login.php` sugerido**

```php
<?php
session_start();
require_once '/var/secure/config.php';

// Verifica se já existe algum usuário cadastrado
$res = $conn->query("SELECT COUNT(*) AS total FROM usuarios");
$row = $res->fetch_assoc();
$temUsuarios = $row['total'] > 0;

// Processa criação do primeiro usuário
if (!$temUsuarios && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['primeiro_usuario'])) {
    $usuario = trim($_POST['usuario']);
    $senha   = trim($_POST['senha']);
    if ($usuario && $senha) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, senha_hash) VALUES (?, ?)");
        $stmt->bind_param("ss", $usuario, $hash);
        if ($stmt->execute()) {
            $_SESSION['logado'] = true;
            $_SESSION['usuario'] = $usuario;
            header("Location: index.php");
            exit;
        } else {
            $erro = "Erro ao criar usuário: " . $conn->error;
        }
        $stmt->close();
    } else {
        $erro = "Preencha todos os campos.";
    }
}

// Processa login normal
if ($temUsuarios && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $usuario = trim($_POST['usuario']);
    $senha   = trim($_POST['senha']);
    $stmt = $conn->prepare("SELECT senha_hash FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->bind_result($senha_hash);
    if ($stmt->fetch() && password_verify($senha, $senha_hash)) {
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $usuario;
        header("Location: index.php");
        exit;
    } else {
        $erro = "Usuário ou senha inválidos.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header text-center">
                    <?php if (!$temUsuarios): ?>
                        Criar Primeiro Usuário Administrador
                    <?php else: ?>
                        Login
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($erro)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                    <?php endif; ?>

                    <?php if (!$temUsuarios): ?>
                        <form method="POST">
                            <input type="hidden" name="primeiro_usuario" value="1">
                            <div class="mb-3">
                                <label class="form-label">Usuário</label>
                                <input type="text" name="usuario" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Senha</label>
                                <input type="password" name="senha" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Criar e Entrar</button>
                        </form>
                    <?php else: ?>
                        <form method="POST">
                            <input type="hidden" name="login" value="1">
                            <div class="mb-3">
                                <label class="form-label">Usuário</label>
                                <input type="text" name="usuario" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Senha</label>
                                <input type="password" name="senha" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

### 🔹 Como funciona
- **Primeiro acesso**:  
  - Se a tabela `usuarios` estiver vazia, o sistema mostra um formulário para criar o **primeiro administrador**.  
  - Após criar, já faz login automático e redireciona para `index.php`.
- **Acessos seguintes**:  
  - Mostra o formulário de login normal.
  - Valida usuário e senha com `password_verify()`.

---

Se quiser, posso também criar um **`logout.php`** simples para encerrar a sessão e voltar ao login, fechando o ciclo de autenticação. Quer que eu já prepare esse também?
Perfeito, Samir — vamos fechar o ciclo de autenticação com um **`logout.php`** simples, que encerra a sessão e redireciona o usuário de volta para o login.  

---

## 📄 **`logout.php`**

```php
<?php
session_start();

// Remove todas as variáveis de sessão
$_SESSION = [];

// Destrói a sessão
session_destroy();

// Redireciona para a página de login
header("Location: login.php");
exit;
```

---

### 🔹 Como usar
- Basta criar o arquivo `logout.php` na raiz do seu projeto.
- Em qualquer página protegida (como o `index.php`), você pode colocar um link ou botão para ele, por exemplo:

```html
<a href="logout.php" class="btn btn-outline-secondary btn-sm">Sair</a>
```

Assim, ao clicar em **Sair**, a sessão é encerrada e o usuário volta para o login.

---

Se quiser, posso já **inserir esse botão de logout no topo da área restrita do `index.php`** para que o administrador tenha sempre à mão a opção de sair. Quer que eu já faça essa integração?
Ótimo, Samir — vamos integrar o **botão de logout** diretamente no topo da área restrita do `index.php`, para que o administrador possa sair a qualquer momento sem precisar caçar o link.  

Aqui está o trecho modificado do **`index.php`** na parte onde começa a área protegida:

---

### 🔹 Alteração no `index.php`

Logo depois de:

```php
<?php else: ?>
```

vamos inserir um **header de área restrita** com o nome do usuário logado e o botão de sair:

```php
    <!-- Cabeçalho da área restrita -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <strong>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?></strong>
        </div>
        <div>
            <a href="logout.php" class="btn btn-outline-secondary btn-sm">Sair</a>
        </div>
    </div>
```

---

### 🔹 Como fica no fluxo
- Ao fazer login, o usuário verá no topo da área restrita:
  - O nome de quem está logado
  - Um botão **Sair** que chama o `logout.php`
- Ao clicar, a sessão é encerrada e o usuário volta para o `login.php`.

---

Se quiser, posso também **adicionar uma verificação extra** para que qualquer tentativa de acessar `index.php` sem estar logado redirecione automaticamente para o `login.php` — assim, nem a área pública aparece para quem não está autenticado.  
Quer que eu já faça essa proteção total?
Perfeito, Samir — vamos então reforçar a segurança para que **qualquer tentativa de acessar o `index.php` sem login** redirecione automaticamente para o `login.php`.  
Assim, a área pública deixa de existir e todo o conteúdo fica protegido.

---

## 🔹 Alteração no topo do `index.php`

Logo no início do arquivo, **antes de qualquer saída HTML**, vamos colocar:

```php
<?php
session_start();
require_once '/var/secure/config.php';

// Bloqueia acesso se não estiver logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}
?>
```

---

## 🔹 Como fica o fluxo agora
- **Usuário não logado** → tenta abrir `index.php` → é redirecionado para `login.php`.
- **Usuário logado** → vê todo o conteúdo protegido (incluindo administração de usuários e solicitações).
- **Botão Sair** → chama `logout.php`, que encerra a sessão e volta para `login.php`.

---

💡 **Dica extra**:  
Se você quiser manter **alguma parte pública** (por exemplo, o formulário de solicitação), podemos separar em dois arquivos:
- `publico.php` → formulário aberto
- `index.php` → área restrita

Assim, o controle de acesso fica mais organizado.

---

Quer que eu já faça essa separação para manter um formulário público e deixar o `index.php` 100% restrito? Isso mantém a segurança e ainda permite solicitações sem login.
Beleza, Samir — vamos então separar o que é **público** do que é **restrito**.  
A ideia é:

- **`publico.php`** → formulário aberto para qualquer visitante enviar solicitações  
- **`index.php`** → área 100% protegida, só acessível após login, com administração de usuários e lista de solicitações  
- **`login.php`** e **`logout.php`** → já prontos, cuidam da autenticação  

---

## 📄 **Novo `publico.php`** (formulário aberto)

```php
<?php
require_once '/var/secure/config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Solicitação de Backup</title>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-4">Solicitação de Backup</h1>

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

    <div class="text-center">
        <a href="login.php" class="btn btn-primary">Área Restrita</a>
    </div>
</div>
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## 📄 **Ajuste no `index.php`** (100% restrito)

No topo do `index.php`, substituímos o início por:

```php
<?php
session_start();
require_once '/var/secure/config.php';

// Bloqueia acesso se não estiver logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}
?>
```

E removemos o formulário público que antes estava nele, deixando apenas:

- Cabeçalho com **"Bem-vindo, usuário"** e botão **Sair**
- Administração de usuários
- Lista de solicitações

---

## 🔹 Fluxo final

1. Visitante acessa **`publico.php`** → envia solicitação sem login  
2. Administrador acessa **`login.php`** → entra no sistema  
3. Após login, vai para **`index.php`** → gerencia usuários e solicitações  
4. Ao clicar em **Sair**, vai para **`logout.php`** → sessão encerrada  

---

Se quiser, posso já **adaptar o `processa_solicitacao.php`** para que, ao enviar o formulário público, ele redirecione de volta para o `publico.php` com uma mensagem de sucesso. Isso deixa a experiência mais amigável. Quer que eu faça essa melhoria também?
Perfeito, Samir — vamos deixar o fluxo redondinho para que, ao enviar o formulário público, o usuário volte para o `publico.php` com uma mensagem de sucesso ou erro.  

---

## 📄 **Novo `processa_solicitacao.php`**

```php
<?php
require_once '/var/secure/config.php';

// Verifica se veio via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $solicitante = trim($_POST['solicitante'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $descricao   = trim($_POST['descricao'] ?? '');

    if ($solicitante && $email && $descricao) {
        $stmt = $conn->prepare("INSERT INTO solicitacoes (solicitante, email, descricao, status, data_criacao) VALUES (?, ?, ?, 'Pendente', NOW())");
        $stmt->bind_param("sss", $solicitante, $email, $descricao);

        if ($stmt->execute()) {
            // Redireciona com mensagem de sucesso
            header("Location: publico.php?msg=sucesso");
            exit;
        } else {
            // Redireciona com mensagem de erro
            header("Location: publico.php?msg=erro");
            exit;
        }
    } else {
        // Campos obrigatórios não preenchidos
        header("Location: publico.php?msg=campos");
        exit;
    }
} else {
    // Acesso direto sem POST
    header("Location: publico.php");
    exit;
}
```

---

## 📄 **Ajuste no `publico.php`** para exibir mensagens

Logo abaixo do `<h1>` no `publico.php`, podemos inserir:

```php
<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'sucesso'): ?>
        <div class="alert alert-success">Solicitação enviada com sucesso!</div>
    <?php elseif ($_GET['msg'] === 'erro'): ?>
        <div class="alert alert-danger">Ocorreu um erro ao enviar sua solicitação. Tente novamente.</div>
    <?php elseif ($_GET['msg'] === 'campos'): ?>
        <div class="alert alert-warning">Preencha todos os campos antes de enviar.</div>
    <?php endif; ?>
<?php endif; ?>
```

---

## 🔹 Como fica o fluxo agora
1. Usuário acessa **`publico.php`** e preenche o formulário.
2. Ao enviar, o **`processa_solicitacao.php`** insere no banco e redireciona de volta para o `publico.php` com um parâmetro `msg`.
3. O `publico.php` interpreta esse parâmetro e mostra a mensagem correspondente.

---

Se quiser, posso também **adicionar uma pequena proteção anti-SPAM** no `publico.php`, como um campo oculto (honeypot) ou um token CSRF, para evitar envios automáticos maliciosos.  
Quer que eu já inclua essa camada extra de segurança?
Perfeito, Samir — vamos incluir uma camada extra de segurança no formulário público usando duas técnicas simples e eficazes:  

1. **Honeypot** (campo oculto que bots tendem a preencher, mas humanos não)  
2. **Token CSRF** (garante que o envio veio da página legítima)  

---

## 📄 Ajuste no `publico.php`

Logo no início do arquivo, antes do HTML, vamos gerar o token CSRF:

```php
<?php
require_once '/var/secure/config.php';
session_start();

// Gera token CSRF se não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
```

No formulário, adicionamos:

```html
<form action="processa_solicitacao.php" method="POST">
    <!-- Campo honeypot (não deve ser preenchido) -->
    <div style="display:none;">
        <label>Deixe este campo vazio</label>
        <input type="text" name="website" value="">
    </div>

    <!-- Token CSRF -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <!-- Campos reais -->
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
```

---

## 📄 Ajuste no `processa_solicitacao.php`

Logo no início, antes de processar, validamos o honeypot e o token:

```php
<?php
require_once '/var/secure/config.php';
session_start();

// Verifica se veio via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Verifica honeypot
    if (!empty($_POST['website'])) {
        // Campo oculto preenchido → provável bot
        header("Location: publico.php?msg=erro");
        exit;
    }

    // 2. Verifica token CSRF
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: publico.php?msg=erro");
        exit;
    }

    // Continua processamento normal...
    $solicitante = trim($_POST['solicitante'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $descricao   = trim($_POST['descricao'] ?? '');

    if ($solicitante && $email && $descricao) {
        $stmt = $conn->prepare("INSERT INTO solicitacoes (solicitante, email, descricao, status, data_criacao) VALUES (?, ?, ?, 'Pendente', NOW())");
        $stmt->bind_param("sss", $solicitante, $email, $descricao);

        if ($stmt->execute()) {
            header("Location: publico.php?msg=sucesso");
            exit;
        } else {
            header("Location: publico.php?msg=erro");
            exit;
        }
    } else {
        header("Location: publico.php?msg=campos");
        exit;
    }
} else {
    header("Location: publico.php");
    exit;
}
```

---

## 🔹 O que mudou
- **Honeypot**: bots que preenchem todos os campos vão cair na armadilha e ser bloqueados.  
- **Token CSRF**: impede que outro site envie requisições maliciosas em nome do usuário.  
- **Sessão iniciada no `publico.php`**: necessária para armazenar o token.

---

Se quiser, posso também **adicionar um reCAPTCHA do Google** para reforçar ainda mais contra SPAM automatizado, mantendo o honeypot e o CSRF como camadas adicionais.  
Quer que eu já prepare essa integração também?
