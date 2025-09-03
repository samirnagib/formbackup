Entendi, Samir — vamos organizar isso em três partes para ficar claro e objetivo:  

1. **Alterações necessárias no `index.php` e demais formulários**  
2. **Script SQL atualizado para refletir o novo `publico.php`**  
3. **Sugestão de página inicial pública com botões e indicação de área restrita**  

---

## **1️⃣ Alterações necessárias nos arquivos existentes**

### **index.php (área restrita)**
- **Campos de solicitação**: remover qualquer formulário de criação de solicitação (isso agora é exclusivo do `publico.php`).  
- **Listagem de solicitações**: ajustar colunas para incluir todos os novos campos do `publico.php` (ex.: `CentroCusto`, `Site`, `Projeto`, `Ambiente`, `TipoBackup`, `Recorrencia`, `Armazenamento`, `ObjetoProtegido`, `VcenterCluster`, `CaminhoArquivos`, `ServidorBD`, `InstanciaBD`, `TipoInstanciaBD`, `ListenerBD`, `InfoComplementar`, `Status`).  
- **Administração de usuários**: manter como já implementado.  
- **Botão de logout**: já incluído na versão anterior.  

---

### **processa_solicitacao.php**
- **Bind de parâmetros**: atualizar para inserir todos os campos novos do `publico.php` na tabela `solicitacoes`.  
- **Validação**: incluir `isset()` e `trim()` para todos os campos obrigatórios.  
- **Redirecionamento**: manter retorno para `publico.php` com mensagens (`msg=sucesso`, `msg=erro`, `msg=campos`).  

---

### **processa_aprovacao.php**
- **E-mail**: incluir no corpo do e-mail os novos campos da solicitação para que o solicitante veja todos os detalhes.  
- **Consulta**: ajustar `SELECT` para buscar todos os campos novos.  

---

### **consulta.php** (se existir)
- **Filtros e listagem**: incluir os novos campos na tabela de resultados.  

---

## **2️⃣ Script SQL atualizado**

```sql
CREATE TABLE solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    DataSolicitacao DATETIME NOT NULL,
    NomeRequisitante VARCHAR(150) NOT NULL,
    EmailRequisitante VARCHAR(150) NOT NULL,
    CentroCusto VARCHAR(100) NOT NULL,
    Site VARCHAR(50) NOT NULL,
    Projeto VARCHAR(150) NOT NULL,
    Ambiente VARCHAR(50) NOT NULL,
    TipoBackup VARCHAR(50) NOT NULL,
    Recorrencia VARCHAR(50) NOT NULL,
    Armazenamento VARCHAR(50) NOT NULL,
    ObjetoProtegido TEXT NOT NULL,
    VcenterCluster VARCHAR(150) DEFAULT NULL,
    CaminhoArquivos TEXT DEFAULT NULL,
    ServidorBD VARCHAR(150) DEFAULT NULL,
    InstanciaBD VARCHAR(150) DEFAULT NULL,
    TipoInstanciaBD VARCHAR(50) DEFAULT NULL,
    ListenerBD VARCHAR(150) DEFAULT NULL,
    InfoComplementar TEXT DEFAULT NULL,
    Status ENUM('Aberto','EmAndamento','Concluido','Cancelado') DEFAULT 'Aberto',
    comentario TEXT DEFAULT NULL,
    data_decisao DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

> Observação: `DataSolicitacao` será preenchida no `processa_solicitacao.php` com `NOW()` ou convertendo a data/hora do formulário.

---

## **3️⃣ Página inicial pública sugerida (`home.php`)**

```php
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Portal de Solicitações de Backup</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5 text-center">
    <h1 class="mb-4">📦 Portal de Solicitações de Backup</h1>
    <p class="lead mb-5">Escolha uma das opções abaixo:</p>

    <div class="d-grid gap-3 col-6 mx-auto">
        <a href="publico.php" class="btn btn-success btn-lg">📝 Nova Solicitação</a>
        <a href="login.php" class="btn btn-primary btn-lg">
            🔐 Área Restrita - Consultar / Aprovar Solicitações
            <br><small>(Requer usuário e senha)</small>
        </a>
    </div>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## **Resumo das mudanças**
- **index.php**: agora só área restrita, listagem e administração de usuários; colunas ajustadas para novos campos.  
- **processa_solicitacao.php**: insere todos os campos do novo `publico.php`.  
- **processa_aprovacao.php**: inclui novos campos no e-mail e consulta.  
- **SQL**: atualizado para refletir todos os campos do formulário revisado.  
- **home.php**: nova página inicial pública com botões claros e indicação de área restrita.  

---

Se quiser, posso já **reescrever o `processa_solicitacao.php` e o `index.php`** com todos esses ajustes prontos para uso, para você só substituir no servidor. Quer que eu já te envie esses dois arquivos completos?
Perfeito, Samir 👍  
Vou te entregar o **`index.php`** (área restrita) e o **`processa_solicitacao.php`** já adaptados para o novo formulário público (`publico.php`) e para o banco de dados atualizado que te passei antes.  

---

## **index.php** (Área restrita – listagem e administração)

```php
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'conexao.php';

// Consulta todas as solicitações
$sql = "SELECT * FROM solicitacoes ORDER BY DataSolicitacao DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Área Restrita - Solicitações</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📋 Solicitações Recebidas</h2>
        <div>
            <a href="logout.php" class="btn btn-danger">Sair</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Requisitante</th>
                    <th>Email</th>
                    <th>Centro Custo</th>
                    <th>Site</th>
                    <th>Projeto</th>
                    <th>Ambiente</th>
                    <th>Tipo Backup</th>
                    <th>Recorrência</th>
                    <th>Armazenamento</th>
                    <th>Objeto Protegido</th>
                    <th>Vcenter/Cluster</th>
                    <th>Caminho Arquivos</th>
                    <th>Servidor BD</th>
                    <th>Instância BD</th>
                    <th>Tipo Instância BD</th>
                    <th>Listener BD</th>
                    <th>Info Complementar</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitacoes as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($s['DataSolicitacao'])) ?></td>
                    <td><?= htmlspecialchars($s['NomeRequisitante']) ?></td>
                    <td><?= htmlspecialchars($s['EmailRequisitante']) ?></td>
                    <td><?= htmlspecialchars($s['CentroCusto']) ?></td>
                    <td><?= htmlspecialchars($s['Site']) ?></td>
                    <td><?= htmlspecialchars($s['Projeto']) ?></td>
                    <td><?= htmlspecialchars($s['Ambiente']) ?></td>
                    <td><?= htmlspecialchars($s['TipoBackup']) ?></td>
                    <td><?= htmlspecialchars($s['Recorrencia']) ?></td>
                    <td><?= htmlspecialchars($s['Armazenamento']) ?></td>
                    <td><?= htmlspecialchars($s['ObjetoProtegido']) ?></td>
                    <td><?= htmlspecialchars($s['VcenterCluster']) ?></td>
                    <td><?= htmlspecialchars($s['CaminhoArquivos']) ?></td>
                    <td><?= htmlspecialchars($s['ServidorBD']) ?></td>
                    <td><?= htmlspecialchars($s['InstanciaBD']) ?></td>
                    <td><?= htmlspecialchars($s['TipoInstanciaBD']) ?></td>
                    <td><?= htmlspecialchars($s['ListenerBD']) ?></td>
                    <td><?= htmlspecialchars($s['InfoComplementar']) ?></td>
                    <td><?= htmlspecialchars($s['Status']) ?></td>
                    <td>
                        <a href="processa_aprovacao.php?id=<?= $s['id'] ?>&acao=aprovar" class="btn btn-success btn-sm">Aprovar</a>
                        <a href="processa_aprovacao.php?id=<?= $s['id'] ?>&acao=rejeitar" class="btn btn-danger btn-sm">Rejeitar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## **processa_solicitacao.php** (recebe dados do `publico.php`)

```php
<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = [
        'NomeRequisitante', 'EmailRequisitante', 'CentroCusto', 'Site', 'Projeto',
        'Ambiente', 'TipoBackup', 'Recorrencia', 'Armazenamento', 'ObjetoProtegido',
        'VcenterCluster', 'CaminhoArquivos', 'ServidorBD', 'InstanciaBD',
        'TipoInstanciaBD', 'ListenerBD', 'InfoComplementar'
    ];

    foreach ($campos as $campo) {
        $$campo = isset($_POST[$campo]) ? trim($_POST[$campo]) : null;
    }

    // Validação básica
    if (empty($NomeRequisitante) || empty($EmailRequisitante) || empty($CentroCusto) || empty($Projeto)) {
        header("Location: publico.php?msg=campos");
        exit();
    }

    try {
        $sql = "INSERT INTO solicitacoes 
        (DataSolicitacao, NomeRequisitante, EmailRequisitante, CentroCusto, Site, Projeto, Ambiente, TipoBackup, Recorrencia, Armazenamento, ObjetoProtegido, VcenterCluster, CaminhoArquivos, ServidorBD, InstanciaBD, TipoInstanciaBD, ListenerBD, InfoComplementar, Status) 
        VALUES (NOW(), :NomeRequisitante, :EmailRequisitante, :CentroCusto, :Site, :Projeto, :Ambiente, :TipoBackup, :Recorrencia, :Armazenamento, :ObjetoProtegido, :VcenterCluster, :CaminhoArquivos, :ServidorBD, :InstanciaBD, :TipoInstanciaBD, :ListenerBD, :InfoComplementar, 'Aberto')";

        $stmt = $conn->prepare($sql);

        foreach ($campos as $campo) {
            $stmt->bindValue(":$campo", $$campo);
        }

        $stmt->execute();

        header("Location: publico.php?msg=sucesso");
        exit();
    } catch (PDOException $e) {
        header("Location: publico.php?msg=erro");
        exit();
    }
} else {
    header("Location: publico.php");
    exit();
}
```

---

💡 **O que já está pronto nesses arquivos**:
- `index.php` agora é **somente área restrita** e lista todos os campos do novo banco.
- `processa_solicitacao.php` insere todos os campos do `publico.php` no banco.
- Ambos estão compatíveis com o **script SQL** que te passei antes.

Se quiser, posso já te entregar também o **`processa_aprovacao.php`** atualizado para enviar e-mail com todos os campos novos e mudar o status no banco.  
Quer que eu já faça isso também para fechar o ciclo do sistema?
Perfeito, Samir — vamos fechar o ciclo com o **`processa_aprovacao.php`** atualizado e já te passo também um exemplo de configuração do **Apache** para que o site abra direto na página pública (`home.php`).  

---

## **processa_aprovacao.php** (com todos os campos no e-mail e atualização de status)

```php
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'conexao.php';

if (isset($_GET['id']) && isset($_GET['acao'])) {
    $id = (int) $_GET['id'];
    $acao = $_GET['acao'];

    // Busca dados da solicitação
    $sql = "SELECT * FROM solicitacoes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$solicitacao) {
        header("Location: index.php?msg=naoencontrado");
        exit();
    }

    // Define novo status
    $novoStatus = ($acao === 'aprovar') ? 'Concluido' : 'Cancelado';

    // Atualiza no banco
    $sqlUpdate = "UPDATE solicitacoes SET Status = :status, data_decisao = NOW() WHERE id = :id";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bindValue(':status', $novoStatus);
    $stmtUpdate->bindValue(':id', $id, PDO::PARAM_INT);
    $stmtUpdate->execute();

    // Monta corpo do e-mail
    $assunto = "Solicitação de Backup #{$id} - {$novoStatus}";
    $mensagem = "Segue o resultado da sua solicitação:\n\n";
    foreach ($solicitacao as $campo => $valor) {
        $mensagem .= ucfirst($campo) . ": " . $valor . "\n";
    }
    $mensagem .= "\nStatus final: {$novoStatus}";

    // Envia e-mail
    $headers = "From: sistema@seudominio.com\r\nReply-To: sistema@seudominio.com";
    @mail($solicitacao['EmailRequisitante'], $assunto, $mensagem, $headers);

    header("Location: index.php?msg={$novoStatus}");
    exit();
} else {
    header("Location: index.php?msg=parametros");
    exit();
}
```

---

## **Configuração do Apache** para abrir direto o site público

Se o seu site está em `/var/www/html/seusistema/` e você quer que ao acessar o domínio ele abra o `home.php` (página pública), você pode configurar no **VirtualHost**:

```apache
<VirtualHost *:80>
    ServerName www.seusite.com
    DocumentRoot /var/www/html/seusistema

    <Directory /var/www/html/seusistema>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex home.php
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/seusite_error.log
    CustomLog ${APACHE_LOG_DIR}/seusite_access.log combined
</VirtualHost>
```

> **Passos para aplicar**:  
> 1. Salve esse bloco em `/etc/apache2/sites-available/seusite.conf`  
> 2. Ative com `sudo a2ensite seusite.conf`  
> 3. Reinicie o Apache: `sudo systemctl restart apache2`  

Assim, ao acessar `http://www.seusite.com`, o Apache já vai carregar o `home.php` como página inicial pública.

---

Se quiser, posso já te **montar o `publico.php`** com todos os campos e validações prontas, para que ele se integre perfeitamente com esse fluxo que acabamos de fechar.  
Quer que eu já te envie esse também para completar o pacote?
Perfeito, Samir!  
Vou te entregar o **`publico.php`** já pronto para funcionar com o `processa_solicitacao.php` e o banco de dados que definimos. Ele será a página pública para abertura de solicitações, com todos os campos novos, validação básica e layout limpo em Bootstrap.  

---

## **publico.php**

```php
<?php
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Nova Solicitação de Backup</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="mb-4">📝 Nova Solicitação de Backup</h2>

    <?php if ($msg === 'sucesso'): ?>
        <div class="alert alert-success">Solicitação enviada com sucesso!</div>
    <?php elseif ($msg === 'erro'): ?>
        <div class="alert alert-danger">Ocorreu um erro ao enviar sua solicitação. Tente novamente.</div>
    <?php elseif ($msg === 'campos'): ?>
        <div class="alert alert-warning">Preencha todos os campos obrigatórios.</div>
    <?php endif; ?>

    <form action="processa_solicitacao.php" method="post" class="row g-3">

        <div class="col-md-6">
            <label class="form-label">Nome do Requisitante *</label>
            <input type="text" name="NomeRequisitante" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">E-mail do Requisitante *</label>
            <input type="email" name="EmailRequisitante" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Centro de Custo *</label>
            <input type="text" name="CentroCusto" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Site *</label>
            <input type="text" name="Site" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Projeto *</label>
            <input type="text" name="Projeto" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Ambiente *</label>
            <select name="Ambiente" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Produção</option>
                <option>Homologação</option>
                <option>Desenvolvimento</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Tipo de Backup *</label>
            <select name="TipoBackup" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Completo</option>
                <option>Diferencial</option>
                <option>Incremental</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Recorrência *</label>
            <select name="Recorrencia" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Diário</option>
                <option>Semanal</option>
                <option>Mensal</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Armazenamento *</label>
            <select name="Armazenamento" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Local</option>
                <option>Nuvem</option>
                <option>Híbrido</option>
            </select>
        </div>

        <div class="col-md-8">
            <label class="form-label">Objeto Protegido *</label>
            <textarea name="ObjetoProtegido" class="form-control" rows="2" required></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">Vcenter / Cluster</label>
            <input type="text" name="VcenterCluster" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Caminho dos Arquivos</label>
            <input type="text" name="CaminhoArquivos" class="form-control">
        </div>

        <div class="col-md-4">
            <label class="form-label">Servidor BD</label>
            <input type="text" name="ServidorBD" class="form-control">
        </div>

        <div class="col-md-4">
            <label class="form-label">Instância BD</label>
            <input type="text" name="InstanciaBD" class="form-control">
        </div>

        <div class="col-md-4">
            <label class="form-label">Tipo Instância BD</label>
            <input type="text" name="TipoInstanciaBD" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Listener BD</label>
            <input type="text" name="ListenerBD" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Informações Complementares</label>
            <textarea name="InfoComplementar" class="form-control" rows="2"></textarea>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-success btn-lg">Enviar Solicitação</button>
            <a href="home.php" class="btn btn-secondary btn-lg">Voltar</a>
        </div>
    </form>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

### 🔹 Como ele se integra
- Ao enviar, chama o `processa_solicitacao.php` que já te passei.
- Campos obrigatórios marcados com `*` e `required`.
- Mensagens de retorno (`sucesso`, `erro`, `campos`) aparecem no topo.

---

Se quiser, posso já **montar o `home.php` com os botões estilizados** e **ajustar o `.htaccess`** para que o Apache redirecione automaticamente para ele, sem precisar mexer no VirtualHost.  
Isso é útil se você não tiver acesso root ao servidor.  

Quer que eu já te envie essa versão com `.htaccess` também?
Perfeito, Samir!  
Vou te entregar o **`home.php`** estilizado com botões claros e também um **`.htaccess`** para que, mesmo sem mexer no VirtualHost, o Apache direcione automaticamente para essa página inicial pública.  

---

## **home.php** (Página inicial pública)

```php
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Portal de Solicitações de Backup</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }
    .btn-lg {
        padding: 1.5rem;
        font-size: 1.2rem;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
</style>
</head>
<body>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">📦 Portal de Solicitações de Backup</h1>
        <p class="lead">Escolha uma das opções abaixo</p>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h4>Nova Solicitação</h4>
                <p>Preencha o formulário para solicitar um novo backup.</p>
                <a href="publico.php" class="btn btn-success btn-lg">📝 Acessar</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h4>Área Restrita</h4>
                <p>Consulta e aprovação de solicitações.<br><small>(Requer login)</small></p>
                <a href="login.php" class="btn btn-primary btn-lg">🔐 Entrar</a>
            </div>
        </div>
    </div>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## **.htaccess** (Redirecionar para `home.php` como página inicial)

Coloque este arquivo **.htaccess** na raiz do seu sistema (mesmo local do `home.php`):

```apache
# Define a página inicial padrão
DirectoryIndex home.php index.php

# Opcional: Forçar acesso via www
# RewriteEngine On
# RewriteCond %{HTTP_HOST} !^www\. [NC]
# RewriteRule ^(.*)$ http://www.%{HTTP_HOST}/$1 [L,R=301]
```

> Com isso, ao acessar `http://seudominio.com/`, o Apache abrirá automaticamente o `home.php` sem precisar alterar o VirtualHost.

---

✅ Agora você tem:
- **home.php** → Página inicial pública com botões para `publico.php` e `login.php`.
- **.htaccess** → Garante que `home.php` seja a página inicial.
- Integração completa com `publico.php`, `processa_solicitacao.php`, `index.php` e `processa_aprovacao.php`.

---

Se quiser, posso já te **montar um diagrama visual** mostrando o fluxo do sistema (público → solicitação → aprovação → conclusão) para facilitar a documentação e treinamento da equipe.  
Quer que eu prepare esse diagrama também?
