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