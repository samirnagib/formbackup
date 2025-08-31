Perfeito, Samir — vamos fechar esse pacote de vez.  
Abaixo está **todo o código final** de cada arquivo, já apontando para o **Bootstrap local** na pasta `/bootstrap/` e usando o `config.php` seguro fora da pasta pública.  

---

## **📌 1 — `/var/secure/config.php`**
*(fora do `/var/www/html`, permissões `chmod 600`)*

```php
<?php
// Chave AES de 32 bytes — gere no Python e mantenha segura
define('ENCRYPTION_KEY', 'sua_chave_aleatoria_de_32_bytes_aqui!!');

function decrypt_data($data) {
    $key = ENCRYPTION_KEY;
    $data = base64_decode($data);
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);
    return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
}

// Valores criptografados gerados no Python
$db_host_enc = 'COLE_AQUI_O_HOST_ENCRYPTED';
$db_user_enc = 'COLE_AQUI_O_USER_ENCRYPTED';
$db_pass_enc = 'COLE_AQUI_O_PASS_ENCRYPTED';
$db_name_enc = 'COLE_AQUI_O_DB_ENCRYPTED';

// Descriptografando
$db_host = decrypt_data($db_host_enc);
$db_user = decrypt_data($db_user_enc);
$db_pass = decrypt_data($db_pass_enc);
$db_name = decrypt_data($db_name_enc);

// Conexão
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
```

---

## **📌 2 — `/var/www/html/index.php`**
```php
<?php require_once '/var/secure/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Sistema de Solicitações de Backup</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">📦 Solicitações de Backup</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="nova_solicitacao.php">📝 Nova Solicitação</a></li>
        <li class="nav-item"><a class="nav-link" href="aprovacoes.php">✅ Aprovações Pendentes</a></li>
        <li class="nav-item"><a class="nav-link" href="consulta.php">🔍 Consultar Solicitações</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="alert alert-info">
    Bem-vindo! Escolha uma das opções no menu acima para continuar.
  </div>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## **📌 3 — `/var/www/html/nova_solicitacao.php`**
```php
<?php require_once '/var/secure/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Nova Solicitação</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h2>📝 Nova Solicitação de Backup</h2>
  <form action="processa_solicitacao.php" method="POST" class="mt-3">
    <div class="mb-3">
      <label class="form-label">Solicitante</label>
      <input type="text" name="solicitante" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">E-mail</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Descrição</label>
      <textarea name="descricao" class="form-control" rows="4" required></textarea>
    </div>
    <button type="submit" class="btn btn-success">Enviar Solicitação</button>
    <a href="index.php" class="btn btn-secondary">Voltar</a>
  </form>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## **📌 4 — `/var/www/html/aprovacoes.php`**
```php
<?php require_once '/var/secure/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Aprovações Pendentes</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h2>✅ Aprovações Pendentes</h2>
  <?php
  $result = $conn->query("SELECT * FROM solicitacoes WHERE status='Pendente'");
  if ($result->num_rows > 0) {
      echo "<table class='table table-striped'><tr><th>ID</th><th>Solicitante</th><th>Descrição</th><th>Ações</th></tr>";
      while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['id']}</td>
                  <td>{$row['solicitante']}</td>
                  <td>{$row['descricao']}</td>
                  <td>
                    <a href='processa_aprovacao.php?id={$row['id']}&acao=aprovar' class='btn btn-success btn-sm'>Aprovar</a>
                    <a href='processa_aprovacao.php?id={$row['id']}&acao=rejeitar' class='btn btn-danger btn-sm'>Rejeitar</a>
                  </td>
                </tr>";
      }
      echo "</table>";
  } else {
      echo "<div class='alert alert-info'>Nenhuma solicitação pendente.</div>";
  }
  ?>
  <a href="index.php" class="btn btn-secondary">Voltar</a>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## **📌 5 — `/var/www/html/ver_solicitacao.php`**
```php
<?php require_once '/var/secure/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Detalhes da Solicitação</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
<?php
$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM solicitacoes WHERE id=$id");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<h2>📄 Solicitação #{$row['id']}</h2>";
    echo "<p><strong>Solicitante:</strong> {$row['solicitante']}</p>";
    echo "<p><strong>E-mail:</strong> {$row['email']}</p>";
    echo "<p><strong>Descrição:</strong> {$row['descricao']}</p>";
    echo "<p><strong>Status:</strong> {$row['status']}</p>";
} else {
    echo "<div class='alert alert-danger'>Solicitação não encontrada.</div>";
}
?>
<a href="consulta.php" class="btn btn-secondary">Voltar</a>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## **📌 6 — `/var/www/html/consulta.php`**
```php
<?php require_once '/var/secure/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Consulta de Solicitações</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h2>🔍 Consultar Solicitações</h2>
  <form method="GET" class="mb-3">
    <input type="text" name="busca" class="form-control" placeholder="Digite o nome do solicitante">
  </form>
  <?php
  if (!empty($_GET['busca'])) {
      $busca = $conn->real_escape_string($_GET['busca']);
      $result = $conn->query("SELECT * FROM solicitacoes WHERE solicitante LIKE '%$busca%'");
      if ($result->num_rows > 0) {
          echo "<table class='table table-striped'><tr><th>ID</th><th>Solic
