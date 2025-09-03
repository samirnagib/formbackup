<?php
require_once '/var/secure/config.php';
require '/var/www/html/formbkp/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function redir($msg) {
    header("Location: publico.php?msg={$msg}");
    exit;
}

$camposObrigatorios = [
    'NomeRequisitante', 'EmailRequisitante', 'CentroCusto', 'Site',
    'Projeto', 'Ambiente', 'TipoBackup', 'Recorrencia',
    'Armazenamento', 'ObjetoProtegido'
];

foreach ($camposObrigatorios as $campo) {
    if (empty($_POST[$campo])) {
        redir('campos');
    }
}

$dados = [];
foreach ($_POST as $k => $v) {
    $dados[$k] = trim($v);
}

try {
    // Inserir no banco
    $sql = "INSERT INTO solicitacoes_backup (
                NomeRequisitante, EmailRequisitante, CentroCusto, Site, Projeto,
                Ambiente, TipoBackup, Recorrencia, Armazenamento, ObjetoProtegido,
                VcenterCluster, CaminhoArquivos, ServidorBD, InstanciaBD, TipoInstanciaBD,
                ListenerBD, InfoComplementar, status, data_solicitacao
            ) VALUES (
                :NomeRequisitante, :EmailRequisitante, :CentroCusto, :Site, :Projeto,
                :Ambiente, :TipoBackup, :Recorrencia, :Armazenamento, :ObjetoProtegido,
                :VcenterCluster, :CaminhoArquivos, :ServidorBD, :InstanciaBD, :TipoInstanciaBD,
                :ListenerBD, :InfoComplementar, 'Pendente', NOW()
            )";
    $stmt = $conn->prepare($sql);
    foreach ($dados as $k => $v) {
        $stmt->bindValue(":{$k}", $v);
    }
    $stmt->execute();

    $idSolicitacao = $conn->lastInsertId();

    // Função para enviar e-mail
    function enviarEmail($destinatario, $nomeDest, $assunto, $mensagem, $bcc = null) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'samirnagib.service@gmail.com';
            $mail->Password   = 'zgdaghogmhswtxrp';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('samirnagib.service@gmail.com', 'Sistema de Backups');
            $mail->addAddress($destinatario, $nomeDest);
            if ($bcc) {
                $mail->addBCC($bcc);
            }

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

    // E-mail para requisitante
    $msgRequisitante = "Olá {$dados['NomeRequisitante']},\n\n".
                       "Sua solicitação de backup (#{$idSolicitacao}) foi registrada com sucesso e está com status Pendente.\n".
                       "Em breve nossa equipe entrará em contato.\n\n".
                       "Atenciosamente,\nEquipe de Backups";
    enviarEmail($dados['EmailRequisitante'], $dados['NomeRequisitante'], "Confirmação de Solicitação de Backup", $msgRequisitante);

    // E-mail para equipe interna
    $msgInterno = "Nova solicitação de backup registrada:\n\n".
                  "ID: {$idSolicitacao}\n".
                  "Requisitante: {$dados['NomeRequisitante']} ({$dados['EmailRequisitante']})\n".
                  "Projeto: {$dados['Projeto']}\n".
                  "Ambiente: {$dados['Ambiente']}\n".
                  "Tipo de Backup: {$dados['TipoBackup']}\n".
                  "Recorrência: {$dados['Recorrencia']}\n".
                  "Armazenamento: {$dados['Armazenamento']}\n".
                  "Objeto Protegido: {$dados['ObjetoProtegido']}\n\n".
                  "Acesse o painel para mais detalhes.";
    enviarEmail('suporte@seudominio.com', 'Equipe de Backups', "Nova Solicitação de Backup #{$idSolicitacao}", $msgInterno);

    // Registrar log de auditoria
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'desconhecido';
    $logSql = "INSERT INTO logs_auditoria (acao, id_solicitacao, ip, user_agent, data_hora)
               VALUES (:acao, :id_solicitacao, :ip, :user_agent, NOW())";
    $stmtLog = $conn->prepare($logSql);
    $stmtLog->execute([
        ':acao' => 'Nova solicitação registrada via público',
        ':id_solicitacao' => $idSolicitacao,
        ':ip' => $ip,
        ':user_agent' => $userAgent
    ]);

    redir('sucesso');

} catch (Exception $e) {
    error_log("Erro ao processar solicitação: " . $e->getMessage());
    redir('erro');
}
?>