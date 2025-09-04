<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';
require '/var/www/html/formbkp/vendor/autoload.php';
require '/var/secure/funcao.php';

// Carrega o autoloader do Composer para o PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$id   = (int)($_GET['id'] ?? 0);
$acao = $_GET['acao'] ?? '';

if ($id <= 0 || !in_array($acao, ['aprovar', 'rejeitar', 'finalizar'])) {
    die("Parâmetros inválidos.");
}

// Busca dados da solicitação
$stmt = $conn->prepare("SELECT NomeRequisitante, EmailRequisitante, Status FROM solicitacoes WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$solicitacao = $stmt->fetch();

if (!$solicitacao) {
    die("Solicitação não encontrada.");
}

$nome  = $solicitacao['NomeRequisitante'];
$email = $solicitacao['EmailRequisitante'];
$status = $solicitacao['Status'];

// Define se os botões devem ser desabilitados
$desabilitarBotoes = in_array($status, ['EmAndamento', 'Cancelado', 'Completo']);


// Ação: Aprovar
if ($acao === 'aprovar') {
     if ($desabilitarBotoes) {
        die("Ação não permitida para o status atual da solicitação.");
    }
    $conn->prepare("UPDATE solicitacoes SET Status='EmAndamento' WHERE id=:id")
         ->execute([':id' => $id]);

    $mensagem = "Olá {$nome},\n\nA requisição número {$id} foi aprovada e está agora em andamento.";
    enviarEmail($email, "Solicitação #{$id} Aprovada", $mensagem,"samirnagib.service@gmail.com");
    registra_log($conn, $id, 'Solicitação aprovada pelo administrador.');

    header("Location: listar_solicitacoes.php");
    exit;
}

// Ação: Rejeitar (formulário de motivo)
if ($acao === 'rejeitar' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($desabilitarBotoes) {
        die("Ação não permitida para o status atual da solicitação.");
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
}

// Processa rejeição
if ($acao === 'rejeitar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $motivo = trim($_POST['motivo']);
    $conn->prepare("UPDATE solicitacoes SET Status='Cancelado' WHERE id=:id")
         ->execute([':id' => $id]);

    $mensagem = "Olá {$nome},\n\nA requisição número {$id} foi rejeitada pelo motivo: {$motivo}";
    enviarEmail($email, "Solicitação #{$id} Rejeitada", $mensagem,"samirnagib.service@gmail.com");
    registra_log($conn, $id, "Usuário: {$_SESSION['usuario']} rejeitou a requisição número {$id} pelo motivo: {$motivo}");
    header("Location: listar_solicitacoes.php");
    exit;
}

// Ação: Finalizar
if ($acao === 'finalizar') {
    if ($desabilitarBotoes) {
        die("Ação não permitida para o status atual da solicitação.");
    }
    $conn->prepare("UPDATE solicitacoes SET Status='Completo' WHERE id=:id")
         ->execute([':id' => $id]);

    $mensagem = "Olá {$nome},\n\nA solicitação número {$id} foi finalizada com sucesso.";
    enviarEmail($email, "Solicitação #{$id} Finalizada", $mensagem,"samirnagib.service@gmail.com");
    registra_log($conn, $id, "Usuário: {$_SESSION['usuario']} finalizou a solicitação número {$id} com sucesso.");
    header("Location: listar_solicitacoes.php");
    exit;
}
