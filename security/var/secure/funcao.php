<?php
// Carrega a biblioteca PHPMailer
require '/var/www/html/formbkp/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Envia um e-mail usando PHPMailer.
 *
 * @param string $destinatario Endereço de e-mail do destinatário.
 * @param string $nomeDest Nome do destinatário.
 * @param string $assunto Assunto do e-mail.
 * @param string $mensagem Conteúdo da mensagem.
 * @param string|null $bcc Endereço de e-mail com cópia oculta (opcional).
 * @return bool Retorna true em caso de sucesso, false em caso de falha.
 */
function enviarEmail($destinatario, $nomeDest, $assunto, $mensagem, $bcc = null) {
    $mail = new PHPMailer(true);
    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'samirnagib.service@gmail.com'; // Altere para seu e-mail
        $mail->Password   = 'zgdaghogmhswtxrp';     // Altere para a senha de app
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8'; // Garante que acentuação funcione

        // Remetente e destinatários
        $mail->setFrom('samirnagib.service@gmail.com', 'Sistema de Backups');
        $mail->addAddress($destinatario, $nomeDest);
        if ($bcc) {
            $mail->addBCC($bcc);
        }

        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = nl2br($mensagem);
        $mail->AltBody = strip_tags($mensagem); // Versão em texto puro

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Loga o erro, mas não o mostra para o usuário final
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        return false;
    }
}

function registra_log($conn, $idSolicitacao, $acao) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'desconhecido';

    $logSql = "INSERT INTO logs_auditoria (acao, id_solicitacao, ip, user_agent, data_hora)
               VALUES (:acao, :id_solicitacao, :ip, :user_agent, NOW())";

    $stmtLog = $conn->prepare($logSql);

    $stmtLog->execute([
        ':acao' => $acao,
        ':id_solicitacao' => $idSolicitacao,
        ':ip' => $ip,
        ':user_agent' => $userAgent
    ]);
}