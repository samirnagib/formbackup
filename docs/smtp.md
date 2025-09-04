        $mail->isSMTP();
        $mail->Host       = 'smtp.seudominio.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seu_email@seudominio.com';
        $mail->Password   = 'sua_senha_ou_senha_app';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;



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