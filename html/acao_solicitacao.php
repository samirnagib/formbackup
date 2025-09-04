<?php
require_once '/var/secure/auth.php';
require_once '/var/secure/config.php';

// Carrega o autoloader do Composer para o PHPMailer
require '/var/www/html/formbkp/vendor/autoload.php';

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
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'samirnagib.service@gmail.com';
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
