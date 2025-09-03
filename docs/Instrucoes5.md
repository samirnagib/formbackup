Entendi, Samir — você quer unir a segurança do seu `config.php` (com credenciais criptografadas) com a robustez e boas práticas do `conexao.php` que te passei antes (usando **PDO**, charset, tratamento de erros, etc.).  

Segue um **`config.php` unificado** com “o melhor dos dois mundos”:

---

## 📄 config.php
```php
<?php
/**
 * config.php
 * Configuração segura de conexão ao banco de dados usando PDO
 * com credenciais criptografadas.
 */

// 🔐 Chave e IV (fora do /var/www/html)
define('ENCRYPTION_KEY', "\xce\x89\x91E\xc6\x94P \xac\x87\x9a\x92\x14F\xa6\x8c8\xda\x1d\xf1'n\xdb\x82\xe3:\x16\xdc\xa7\x8c\xad\x00");
define('ENCRYPTION_IV', "w\xd0\xd7\xca\xca\xbc\xeck\xd2\x87\x8a\xf7C\x0bq\xa1");

// 🔒 Credenciais criptografadas
$encrypted_host = 'd9DXysq87GvSh4r3QwtxofwWzswfsxwYadGDwn6AanE=';
$encrypted_user = 'd9DXysq87GvSh4r3QwtxoYYOtvKK5mE8Dlw1pa+HQG8=';
$encrypted_pass = 'd9DXysq87GvSh4r3QwtxofAbobhOmAmuSkl02m3LMjWU7/RAJsJ6RDqmnvqUkmLQ';
$encrypted_db   = 'd9DXysq87GvSh4r3QwtxoVAVSGYEhvL6tdZS1U1TEJw=';

/**
 * Descriptografa e limpa a string
 */
function decrypt_clean($data) {
    $key = ENCRYPTION_KEY;
    $iv = ENCRYPTION_IV;
    $ciphertext = base64_decode($data);
    $decrypted = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

    // Remove caracteres não imprimíveis
    $cleaned = preg_replace('/[^\x20-\x7E]/', '', $decrypted);
    // Remove prefixo "DML~$w" se existir
    $cleaned = preg_replace('/^DML~\$w/', '', $cleaned);
    // Remove espaços extras
    return trim($cleaned);
}

// 🔓 Descriptografando credenciais
$db_host = decrypt_clean($encrypted_host);
$db_user = decrypt_clean($encrypted_user);
$db_pass = decrypt_clean($encrypted_pass);
$db_name = decrypt_clean($encrypted_db);
$charset = 'utf8mb4';

// 📡 Criando conexão PDO
$dsn = "mysql:host={$db_host};dbname={$db_name};charset={$charset}";

try {
    $conn = new PDO($dsn, $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Em produção, logar o erro e mostrar mensagem genérica
    die("Erro ao conectar ao banco de dados.");
}
```

---

## 📂 Onde colocar
- Idealmente: **fora da pasta pública** do servidor web, por exemplo:
  ```
  /var/secure/config.php
  ```
- E nos scripts PHP que precisam de conexão:
  ```php
  require_once '/var/secure/config.php';
  ```

---

✅ **O que melhoramos aqui**:
- Mantivemos **criptografia das credenciais** do seu arquivo original.
- Migramos para **PDO** (mais seguro, suporta múltiplos bancos, prepared statements).
- Definimos **charset utf8mb4** para evitar problemas com acentos e emojis.
- Tratamento de erros com `PDO::ERRMODE_EXCEPTION`.
- Código mais limpo e reutilizável.

---

Se quiser, posso já **adaptar o resto do seu sistema** para usar este `config.php` unificado no lugar do `mysqli` antigo, sem quebrar nada na lógica atual.  
Quer que eu já faça essa adaptação?
Perfeito, Samir — vamos fazer essa migração para que todo o seu sistema use o **novo `config.php` unificado** com PDO e credenciais criptografadas, sem quebrar a lógica atual.  

A ideia é simples:  
1. **Remover** qualquer código de conexão duplicado nos arquivos.  
2. **Incluir** o `config.php` no topo de cada script que precisa de acesso ao banco.  
3. **Trocar** chamadas `mysqli_*` para PDO, mantendo a mesma lógica de consultas e laços.  

---

## 🔄 Como adaptar seus arquivos

### 1. Incluindo o `config.php`
No início de cada arquivo PHP que acessa o banco, coloque:
```php
require_once '/var/secure/config.php'; // ajuste o caminho conforme onde salvou
```

---

### 2. Convertendo consultas `mysqli` para PDO

#### Antes (mysqli)
```php
$sql = "SELECT * FROM usuarios WHERE id = $id";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo $row['nome'];
}
```

#### Depois (PDO)
```php
$sql = "SELECT * FROM usuarios WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();

while ($row = $stmt->fetch()) {
    echo $row['nome'];
}
```

---

### 3. Inserções e atualizações

#### Antes (mysqli)
```php
$sql = "INSERT INTO usuarios (nome, email) VALUES ('$nome', '$email')";
$conn->query($sql);
```

#### Depois (PDO)
```php
$sql = "INSERT INTO usuarios (nome, email) VALUES (:nome, :email)";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':nome', $nome);
$stmt->bindValue(':email', $email);
$stmt->execute();
```

---

### 4. Contagem de registros

#### Antes
```php
$result = $conn->query("SELECT COUNT(*) AS total FROM usuarios");
$row = $result->fetch_assoc();
echo $row['total'];
```

#### Depois
```php
$stmt = $conn->query("SELECT COUNT(*) AS total FROM usuarios");
$row = $stmt->fetch();
echo $row['total'];
```

---

### 5. Tratamento de erros
Com PDO, qualquer erro de consulta já vai gerar uma **exceção** por causa do:
```php
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```
Você pode capturar assim:
```php
try {
    $stmt = $conn->query("SELECT * FROM tabela_inexistente");
} catch (PDOException $e) {
    error_log("Erro SQL: " . $e->getMessage());
    echo "Ocorreu um erro ao buscar os dados.";
}
```

---

💡 **Vantagem**: Agora todas as conexões usam **uma única fonte de configuração** (`config.php`), com credenciais criptografadas e PDO, o que facilita manutenção e aumenta a segurança.

---

Se quiser, posso já **pegar um dos seus arquivos reais** (por exemplo, `login.php` ou `processa_solicitacao.php`) e te devolver **já convertido para PDO** usando esse `config.php` novo, para você ver na prática como fica.  

Quer que eu faça isso com um arquivo seu agora?
Entendi, Samir — a ideia é que **todo o seu sistema** passe a usar o `config.php` unificado com PDO e credenciais criptografadas, inclusive as telas e scripts de manutenção de usuários.  
Como não tenho aqui o conteúdo exato de cada arquivo, vou te entregar um **modelo de conversão** que você pode aplicar a todos eles, e já te mostro exemplos prontos para os tipos mais comuns de script que costumam existir num sistema como o seu.  

---

## 📂 Estrutura típica de arquivos que usam banco
Normalmente, num sistema de gestão com usuários, você teria algo como:

1. `login.php` — autenticação de usuários  
2. `listar_usuarios.php` — listagem com paginação  
3. `adicionar_usuario.php` — formulário + inserção  
4. `editar_usuario.php` — formulário + update  
5. `excluir_usuario.php` — exclusão  
6. Outros scripts que fazem consultas ou relatórios

Todos eles vão começar com:
```php
require_once '/var/secure/config.php';
```
E usar **PDO** para consultas.

---

## 1️⃣ login.php (exemplo convertido)
```php
<?php
require_once '/var/secure/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = trim($_POST['senha'] ?? '');

    $sql = "SELECT id, senha_hash FROM usuarios WHERE usuario = :usuario LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':usuario', $usuario);
    $stmt->execute();

    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha_hash'])) {
        session_start();
        $_SESSION['usuario_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $erro = "Usuário ou senha inválidos.";
    }
}
?>
<!-- HTML do formulário -->
```

---

## 2️⃣ listar_usuarios.php
```php
<?php
require_once '/var/secure/config.php';

$sql = "SELECT id, usuario, nome, email FROM usuarios ORDER BY nome ASC";
$stmt = $conn->query($sql);
$usuarios = $stmt->fetchAll();
?>
<table>
    <tr><th>ID</th><th>Usuário</th><th>Nome</th><th>Email</th></tr>
    <?php foreach ($usuarios as $u): ?>
    <tr>
        <td><?= htmlspecialchars($u['id']) ?></td>
        <td><?= htmlspecialchars($u['usuario']) ?></td>
        <td><?= htmlspecialchars($u['nome']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
```

---

## 3️⃣ adicionar_usuario.php
```php
<?php
require_once '/var/secure/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $nome    = trim($_POST['nome']);
    $email   = trim($_POST['email']);
    $senha   = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (usuario, nome, email, senha_hash) 
            VALUES (:usuario, :nome, :email, :senha)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':usuario', $usuario);
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':senha', $senha);
    $stmt->execute();

    header('Location: listar_usuarios.php');
    exit;
}
?>
<!-- HTML do formulário -->
```

---

## 4️⃣ editar_usuario.php
```php
<?php
require_once '/var/secure/config.php';

$id = (int)($_GET['id'] ?? 0);

// Buscar dados atuais
$stmt = $conn->prepare("SELECT usuario, nome, email FROM usuarios WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch();

if (!$usuario) {
    die("Usuário não encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);

    $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: listar_usuarios.php');
    exit;
}
?>
<!-- HTML do formulário -->
```

---

## 5️⃣ excluir_usuario.php
```php
<?php
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

💡 **O que mudou em todos eles**:
- Incluem o `config.php` unificado no topo.
- Usam **PDO** com `prepare()` e `bindValue()` para evitar SQL Injection.
- Charset e tratamento de erros já vêm do `config.php`.
- Senhas armazenadas com `password_hash()` e verificadas com `password_verify()`.

---

Se você quiser, posso **pegar o seu pacote de arquivos atual** e já te devolver **todos convertidos** para PDO com esse padrão, prontos para substituir os antigos.  
Você quer me enviar o conteúdo dos arquivos originais para eu já te devolver a versão final adaptada?
