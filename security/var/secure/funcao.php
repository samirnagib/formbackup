<?php
// Carrega a biblioteca PHPMailer
require '/path/to/PHPMailer/src/PHPMailer.php';
require '/path/to/PHPMailer/src/SMTP.php';
require '/path/to/PHPMailer/src/Exception.php';

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
        $mail->Username   = 'seu-email@gmail.com'; // Altere para seu e-mail
        $mail->Password   = 'sua-senha-de-app';     // Altere para a senha de app
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8'; // Garante que acentuação funcione

        // Remetente e destinatários
        $mail->setFrom('seu-email@gmail.com', 'Sistema de Backups');
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