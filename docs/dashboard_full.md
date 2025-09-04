Entendi, Samir — vamos integrar no **dashboard** a listagem das solicitações criadas pelo seu `publico.php` e implementar o fluxo completo com os quatro botões que você pediu: **Ver**, **Aprovar**, **Rejeitar** e **Finalizar**.  

Vou te entregar os arquivos prontos para colocar no seu ambiente, seguindo o mesmo padrão de segurança que já usamos (`auth.php` + `config.php` + PDO + prepared statements) e com envio de e-mail via `mail()` (você pode trocar por PHPMailer se quiser algo mais robusto).

---

## 📄 dashboard.php (atualizado)
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
        <div class="col-md-6">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white">📋 Auditoria</div>
                <div class="card-body">
                    <p>Visualize e gerencie os logs de auditoria.</p>
                    <a href="auditoria.php" class="btn btn-success">Acessar</a>
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

$sql = "SELECT id, data_solicitacao, NomeRequisitante, EmailRequisitante, TipoBackup, status 
        FROM solicitacoes_backup ORDER BY data_solicitacao DESC";
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
                <th>Data</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Tipo Backup</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitacoes as $s): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($s['data_solicitacao'])) ?></td>
                <td><?= htmlspecialchars($s['NomeRequisitante']) ?></td>
                <td><?= htmlspecialchars($s['EmailRequisitante']) ?></td>
                <td><?= htmlspecialchars($s['TipoBackup']) ?></td>
                <td><?= htmlspecialchars($s['status']) ?></td>
                <td>
                    <a href="ver_solicitacao.php?id=<?= $s['id'] ?>" class="btn btn-primary btn-sm">Ver</a>
                    <a href="acao_solicitacao.php?id=<?= $s['id'] ?>&acao=aprovar" class="btn btn-success btn-sm">Aprovar</a>
                    <a href="acao_solicitacao.php?id=<?= $s['id'] ?>&acao=rejeitar" class="btn btn-warning btn-sm">Rejeitar</a>
                    <a href="acao_solicitacao.php?id=<?= $s['id'] ?>&acao=finalizar" class="btn btn-secondary btn-sm">Finalizar</a>
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

$stmt = $conn->prepare("SELECT * FROM solicitacoes WHERE id = :id");
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
        <?php foreach ($solicitacao as $campo => $valor): ?>
            <li class="list-group-item"><strong><?= htmlspecialchars($campo) ?>:</strong> <?= nl2br(htmlspecialchars($valor)) ?></li>
        <?php endforeach; ?>
    </ul>
    <div class="mt-3">
        <a href="acao_solicitacao.php?id=<?= $solicitacao['id'] ?>&acao=aprovar" class="btn btn-success">Aprovar</a>
        <a href="acao_solicitacao.php?id=<?= $solicitacao['id'] ?>&acao=rejeitar" class="btn btn-warning">Rejeitar</a>
    </div>
    <a href="listar_solicitacoes.php" class="btn btn-secondary mt-3">⬅ Voltar</a>
</div>
</body>
</html>
```

---

## 📄 acao_solicitacao.php
```php
<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

$id = (int)($_GET['id'] ?? 0);
$acao = $_GET['acao'] ?? '';

$stmt = $conn->prepare("SELECT EmailRequisitante FROM solicitacoes_backup WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$solicitacao = $stmt->fetch();

if (!$solicitacao) {
    die("Solicitação não encontrada.");
}

$email = $solicitacao['EmailRequisitante'];

if ($acao === 'aprovar') {
    $conn->prepare("UPDATE solicitacoes_backup SET status='Em andamento' WHERE id=:id")
         ->execute([':id' => $id]);
    mail($email, "Solicitação Aprovada", "A requisição número $id foi aprovada.");
    header("Location: listar_solicitacoes.php");
    exit;

} elseif ($acao === 'rejeitar') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $motivo = trim($_POST['motivo']);
        $conn->prepare("UPDATE solicitacoes_backup SET status='Cancelado' WHERE id=:id")
             ->execute([':id' => $id]);
        mail($email, "Solicitação Rejeitada", "A requisição número $id foi rejeitada pelo motivo: $motivo");
        header("Location: listar_solicitacoes.php");
        exit;
    }
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
    <meta charset="UTF-8">
    <title>Rejeitar Solicitação</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
    <div class="container py-4">
        <h2>Rejeitar Solicitação #<?= $id ?></h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Motivo da Rejeição</label>
                <textarea name="motivo" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-warning">Confirmar Rejeição</button>
            <a href="listar_solicitacoes.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    </body>
    </html>
    <?php
    exit;

} elseif ($acao === 'finalizar') {
    $conn->prepare("UPDATE solicitacoes_backup SET status='Completo' WHERE id=:id")
         ->execute([':id' => $
====
Entendido, Samir — vamos refazer o **`acao_solicitacao.php`** completo, já usando **PHPMailer** para envio de e-mails e cobrindo os três fluxos que você pediu: **Aprovar**, **Rejeitar** (com motivo) e **Finalizar**.  

O script vai:  
- Proteger o acesso com `auth.php`  
- Buscar o e-mail do solicitante no banco  
- Atualizar o status conforme a ação  
- Enviar o e-mail com a mensagem apropriada  
- No caso de rejeição, exibir um formulário para informar o motivo antes de enviar  

---

## 📄 acao_solicitacao.php
```php
<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

// Carrega o autoloader do Composer para o PHPMailer
require '/var/www/html/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$id   = (int)($_GET['id'] ?? 0);
$acao = $_GET['acao'] ?? '';

if ($id <= 0 || !in_array($acao, ['aprovar', 'rejeitar', 'finalizar'])) {
    die("Parâmetros inválidos.");
}

// Busca dados da solicitação
$stmt = $conn->prepare("SELECT NomeRequisitante, EmailRequisitante FROM solicitacoes_backup WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$solicitacao = $stmt->fetch();

if (!$solicitacao) {
    die("Solicitação não encontrada.");
}

$nome  = $solicitacao['NomeRequisitante'];
$email = $solicitacao['EmailRequisitante'];

// Função para enviar e-mail
function enviarEmail($destinatario, $assunto, $mensagem) {
    $mail = new PHPMailer(true);
    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.seudominio.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seu_email@seudominio.com';
        $mail->Password   = 'sua_senha_ou_senha_app';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Remetente e destinatário
        $mail->setFrom('seu_email@seudominio.com', 'Sistema de Backups');
        $mail->addAddress($destinatario);

        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = nl2br($mensagem);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        return false;
    }
}

// Ação: Aprovar
if ($acao === 'aprovar') {
    $conn->prepare("UPDATE solicitacoes_backup SET status='Em andamento' WHERE id=:id")
         ->execute([':id' => $id]);

    $mensagem = "Olá {$nome},\n\nA requisição número {$id} foi aprovada e está agora em andamento.";
    enviarEmail($email, "Solicitação #{$id} Aprovada", $mensagem);

    header("Location: listar_solicitacoes.php");
    exit;
}

// Ação: Rejeitar (formulário de motivo)
if ($acao === 'rejeitar' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
    <meta charset="UTF-8">
    <title>Rejeitar Solicitação</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
    <div class="container py-4">
        <h2>Rejeitar Solicitação #<?= $id ?></h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Motivo da Rejeição</label>
                <textarea name="motivo" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-warning">Confirmar Rejeição</button>
            <a href="listar_solicitacoes.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// Processa rejeição
if ($acao === 'rejeitar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $motivo = trim($_POST['motivo']);
    $conn->prepare("UPDATE solicitacoes_backup SET status='Cancelado' WHERE id=:id")
         ->execute([':id' => $id]);

    $mensagem = "Olá {$nome},\n\nA requisição número {$id} foi rejeitada pelo motivo: {$motivo}";
    enviarEmail($email, "Solicitação #{$id} Rejeitada", $mensagem);

    header("Location: listar_solicitacoes.php");
    exit;
}

// Ação: Finalizar
if ($acao === 'finalizar') {
    $conn->prepare("UPDATE solicitacoes_backup SET status='Completo' WHERE id=:id")
         ->execute([':id' => $id]);

    $mensagem = "Olá {$nome},\n\nA solicitação número {$id} foi finalizada com sucesso.";
    enviarEmail($email, "Solicitação #{$id} Finalizada", $mensagem);

    header("Location: listar_solicitacoes.php");
    exit;
}
```

---

### 🔹 Como funciona
- **Aprovar** → muda status para *Em andamento* e envia e-mail de aprovação.  
- **Rejeitar** → abre formulário para informar motivo; ao enviar, muda status para *Cancelado* e envia e-mail com o motivo.  
- **Finalizar** → muda status para *Completo* e envia e-mail de finalização.  

---

Se quiser, posso já **adaptar o `listar_solicitacoes.php`** para que os botões chamem exatamente este `acao_solicitacao.php` e fiquem com ícones e cores consistentes.  
Quer que eu já faça essa integração visual e funcional?
**Integrando e ajustando**

Estou trabalhando na integração de Bootstrap e ajustando ações de redireção para links relativos permitidos, garantindo uma navegação segura e controlada, além de manter a navegabilidade genuína e fluída.
Perfeito — vamos incluir o botão Ver com uma página de detalhes que permite Aprovar, Rejeitar (com motivo) e Finalizar, além de paginação e busca na listagem.

---

## Listagem com busca, filtros e paginação

```php
<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

// Parâmetros
$q        = trim($_GET['q'] ?? '');
$status   = $_GET['status'] ?? 'Todos';
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = min(100, max(5, (int)($_GET['per_page'] ?? 10)));
$offset   = ($page - 1) * $perPage;

// Construção do filtro
$where  = [];
$params = [];

// Busca por ID exato (se número) ou por nome/e-mail
if ($q !== '') {
    if (ctype_digit($q)) {
        $where[]         = '(id = :idExato OR NomeRequisitante LIKE :q OR EmailRequisitante LIKE :q)';
        $params[':idExato'] = (int)$q;
    } else {
        $where[] = '(NomeRequisitante LIKE :q OR EmailRequisitante LIKE :q)';
    }
    $params[':q'] = "%{$q}%";
}

if ($status !== 'Todos' && $status !== '') {
    $where[] = 'status = :status';
    $params[':status'] = $status;
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Total para paginação
$sqlCount = "SELECT COUNT(*) FROM solicitacoes_backup {$whereSql}";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->execute($params);
$totalRows = (int)$stmtCount->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $perPage));

// Dados
$sql = "SELECT id, NomeRequisitante, EmailRequisitante, status, data_solicitacao
        FROM solicitacoes_backup
        {$whereSql}
        ORDER BY data_solicitacao DESC
        LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preservar query ao retornar da página de detalhes
function qs(array $extra = []): string {
    $base = $_GET;
    foreach ($extra as $k => $v) {
        if ($v === null) unset($base[$k]); else $base[$k] = $v;
    }
    return http_build_query($base);
}
$return = 'listar_solicitacoes.php?' . qs(); // para o ver_solicitacao.php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Solicitações de Backup</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">Solicitações de Backup</h2>

    <form class="row g-2 mb-3" method="get">
        <div class="col-md-4">
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" class="form-control" placeholder="Buscar por ID, nome ou e-mail">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <?php
                $statuses = ['Todos','Pendente','Em andamento','Completo','Cancelado'];
                foreach ($statuses as $st):
                ?>
                <option value="<?= $st ?>" <?= $status===$st?'selected':'' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="per_page" class="form-select">
                <?php foreach ([10,20,50,100] as $pp): ?>
                <option value="<?= $pp ?>" <?= $perPage===$pp?'selected':'' ?>><?= $pp ?> por página</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button>
            <a href="listar_solicitacoes.php" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Limpar</a>
        </div>
    </form>

    <div class="small text-muted mb-2">
        Exibindo <?= count($solicitacoes) ?> de <?= $totalRows ?> resultado(s) — página <?= $page ?> de <?= $totalPages ?>
    </div>

    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Requisitante</th>
                <th>E-mail</th>
                <th>Status</th>
                <th>Data</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!$solicitacoes): ?>
            <tr><td colspan="6" class="text-center text-muted">Nenhuma solicitação encontrada.</td></tr>
        <?php endif; ?>
        <?php foreach ($solicitacoes as $s): ?>
            <?php
                $badgeClass = match($s['status']) {
                    'Pendente'     => 'secondary',
                    'Em andamento' => 'warning',
                    'Completo'     => 'success',
                    'Cancelado'    => 'danger',
                    default        => 'secondary'
                };
                $returnParam = urlencode($return);
                $verUrl = "ver_solicitacao.php?id={$s['id']}&return={$returnParam}";
            ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= htmlspecialchars($s['NomeRequisitante']) ?></td>
                <td><?= htmlspecialchars($s['EmailRequisitante']) ?></td>
                <td><span class="badge bg-<?= $badgeClass ?>"><?= $s['status'] ?></span></td>
                <td><?= $s['data_solicitacao'] ? date('d/m/Y H:i', strtotime($s['data_solicitacao'])) : '—' ?></td>
                <td class="text-center d-flex gap-2 justify-content-center">
                    <a href="<?= $verUrl ?>" class="btn btn-outline-dark btn-sm" title="Ver detalhes">
                        <i class="bi bi-eye"></i>
                    </a>

                    <?php if ($s['status'] === 'Pendente'): ?>
                        <a href="acao_solicitacao.php?id=<?= $s['id'] ?>&acao=aprovar" 
                           class="btn btn-success btn-sm" title="Aprovar">
                           <i class="bi bi-check-circle"></i>
                        </a>
                        <a href="acao_solicitacao.php?id=<?= $s['id'] ?>&acao=rejeitar" 
                           class="btn btn-danger btn-sm" title="Rejeitar">
                           <i class="bi bi-x-circle"></i>
                        </a>
                    <?php elseif ($s['status'] === 'Em andamento'): ?>
                        <a href="acao_solicitacao.php?id=<?= $s['id'] ?>&acao=finalizar" 
                           class="btn btn-primary btn-sm" title="Finalizar">
                           <i class="bi bi-flag-checkered"></i>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <nav aria-label="Paginação">
        <ul class="pagination">
            <?php
            // helper para links de paginação preservando filtros
            function pageLink($p, $label, $disabled = false, $active = false) {
                $qs = $_GET; $qs['page'] = $p;
                $url = 'listar_solicitacoes.php?' . http_build_query($qs);
                $cls = 'page-item';
                if ($disabled) $cls .= ' disabled';
                if ($active) $cls .= ' active';
                echo "<li class=\"$cls\"><a class=\"page-link\" href=\"$url\">$label</a></li>";
            }
            pageLink(max(1, $page-1), '«', $page===1);
            for ($p = 1; $p <= $totalPages; $p++) {
                // janela de páginas
                if ($p === 1 || $p === $totalPages || abs($p - $page) <= 2) {
                    pageLink($p, (string)$p, false, $p===$page);
                } elseif (abs($p - $page) === 3) {
                    echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                }
            }
            pageLink(min($totalPages, $page+1), '»', $page===$totalPages);
            ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>
</body>
</html>
```

---

## Página de detalhes com ações (ver_solicitacao.php)

```php
<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit('ID inválido'); }

// URL de retorno (lista com filtros), se houver
$return = $_GET['return'] ?? '';
if ($return && !preg_match('#^listar_solicitacoes\.php#', urldecode($return))) {
    $return = ''; // evita open redirect
}

// Busca completa
$stmt = $conn->prepare("SELECT * FROM solicitacoes_backup WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) { http_response_code(404); exit('Solicitação não encontrada'); }

$status = $row['status'] ?? 'Pendente';
$badgeClass = match($status) {
    'Pendente'     => 'secondary',
    'Em andamento' => 'warning',
    'Completo'     => 'success',
    'Cancelado'    => 'danger',
    default        => 'secondary'
};

// Redirect de volta a esta página após ação
$redirectAfter = 'ver_solicitacao.php?id='.$id . ($return ? '&return='.urlencode($return) : '');
$redirectParam = '&redirect=' . urlencode($redirectAfter);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Detalhes da Solicitação #<?= $id ?></title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Solicitação #<?= $id ?> <span class="badge bg-<?= $badgeClass ?> align-middle"><?= htmlspecialchars($status) ?></span></h3>
        <div class="d-flex gap-2">
            <?php if ($status === 'Pendente'): ?>
                <a href="acao_solicitacao.php?id=<?= $id ?>&acao=aprovar<?= $redirectParam ?>" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Aprovar
                </a>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalRejeitar">
                    <i class="bi bi-x-circle"></i> Rejeitar
                </button>
            <?php elseif ($status === 'Em andamento'): ?>
                <a href="acao_solicitacao.php?id=<?= $id ?>&acao=finalizar<?= $redirectParam ?>" class="btn btn-primary">
                    <i class="bi bi-flag-checkered"></i> Finalizar
                </a>
            <?php endif; ?>
            <a href="<?= $return ? urldecode($return) : 'listar_solicitacoes.php' ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Detalhes</div>
        <div class="card-body">
            <div class="row g-3">
                <?php
                // Renderiza pares chave/valor
                foreach ($row as $col => $val):
                    $label = htmlspecialchars($col);
                    $value = $val;

                    // Formatações simples
                    if ($col === 'data_solicitacao' && $val) {
                        $value = date('d/m/Y H:i', strtotime($val));
                    } else {
                        $value = htmlspecialchars((string)$val);
                        // Quebra de linha para textos longos
                        if (strlen($value) > 120) {
                            $value = '<div class="text-break">'.nl2br($value).'</div>';
                        }
                    }
                ?>
                <div class="col-md-6">
                    <div class="border rounded p-2 h-100">
                        <div class="text-muted small mb-1"><?= $label ?></div>
                        <div class="fw-semibold"><?= $value === '' ? '—' : $value ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rejeitar -->
<div class="modal fade" id="modalRejeitar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="acao_solicitacao.php?id=<?= $id ?>&acao=rejeitar<?= $redirectParam ?>">
      <div class="modal-header">
        <h5 class="modal-title">Rejeitar Solicitação #<?= $id ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Motivo da rejeição</label>
            <textarea name="motivo" class="form-control" rows="4" required></textarea>
        </div>
        <div class="alert alert-warning small mb-0">
            Esta ação definirá o status como <strong>Cancelado</strong> e notificará o requisitante.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle"></i> Confirmar rejeição</button>
      </div>
    </form>
  </div>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## Ajuste opcional em acao_solicitacao.php (redirect pós-ação)

Adicione um redirect seguro para voltar à página de detalhes quando a ação partir dela.

```php
// após obter $id e $acao
$redirect = $_GET['redirect'] ?? '';
// whitelist simples
$allow = ['listar_solicitacoes.php','ver_solicitacao.php'];
$go = 'listar_solicitacoes.php';
if ($redirect) {
    $decoded = urldecode($redirect);
    foreach ($allow as $ok) {
        if (str_starts_with($decoded, $ok)) { $go = $decoded; break; }
    }
}

// ... após cada ação bem-sucedida:
header("Location: " . $go);
exit;
```

Use o mesmo padrão em todos os blocos de ação (aprovar, rejeitar POST, finalizar) substituindo o redirect fixo anterior.

---

### O que foi adicionado
- **Busca e filtros:** por ID, nome, e-mail e por status.
- **Paginação:** controlada por per_page e page, preservando filtros.
- **Botão Ver:** abre detalhes completos da solicitação.
- **Ações no detalhe:** Aprovar, Rejeitar (com modal e motivo) e Finalizar.
- **Redirect inteligente:** volta para a página de onde a ação foi disparada.

Quer que eu inclua também um campo de comentário interno e histórico de atividades para cada solicitação? Isso ajuda a rastrear decisões e facilita auditoria.
