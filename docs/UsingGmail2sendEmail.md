Para configurar sua aplicação PHP para enviar e-mails usando uma conta do Gmail, a melhor e mais segura abordagem é usar uma **biblioteca de terceiros** como a **PHPMailer**.

Enviar e-mails diretamente com a função `mail()` do PHP é arriscado e não é compatível com os servidores do Gmail, que exigem autenticação segura (SMTP/SSL/TLS).

Aqui está um guia passo a passo completo.

-----

### Passo 1: Habilitar o SMTP e o "Acesso de aplicativos menos seguros" no Gmail (Método antigo)

**Observação:** O Google descontinuou o "Acesso de aplicativos menos seguros". A partir de 30 de maio de 2022, a opção não está mais disponível.

A alternativa é usar **Senhas de aplicativo**.

-----

### Passo 2: Criar uma Senha de aplicativo (Método recomendado)

Uma **Senha de aplicativo** é uma senha de 16 dígitos que dá a um aplicativo ou dispositivo permissão para acessar sua conta do Google sem que você precise inserir sua senha principal.

1.  **Ative a verificação em duas etapas (2FA)** em sua conta do Google. Sem isso, você não pode criar uma senha de aplicativo.

      * Vá em [Gerenciar sua Conta do Google](https://myaccount.google.com/).
      * No menu à esquerda, clique em **Segurança**.
      * Em "Como fazer login no Google", clique em **Verificação em duas etapas**. Siga as instruções para ativá-la.

2.  **Gere a Senha de aplicativo:**

      * Volte para a página **Segurança** e clique em **Senhas de aplicativo**.
      * Faça login novamente, se solicitado.
      * Em "Selecionar app", escolha **"E-mail"**.
      * Em "Selecionar dispositivo", escolha **"Outro (Nome personalizado)"** e digite algo como `Minha Aplicação PHP`.
      * Clique em **Gerar**.
      * Uma senha de 16 caracteres será exibida. **Copie essa senha imediatamente**, pois ela só será mostrada uma vez.

-----

### Passo 3: Configurar a PHPMailer

1.  **Instale a PHPMailer**

      * A forma mais fácil é usando o `Composer`. Se você não tem, baixe e instale-o.
      * No seu terminal, dentro da pasta do seu projeto, execute:
        ```bash
        composer require phpmailer/phpmailer
        ```
      * Isso irá criar uma pasta `vendor` e o arquivo `composer.json` e `composer.lock`.

2.  **Crie o script PHP**

      * Crie um arquivo PHP (ex: `enviar_email.php`) e use o código abaixo.
      * Lembre-se de substituir os placeholders (`SEU_EMAIL_GMAIL`, `SUA_SENHA_DE_APLICATIVO`, etc.) com suas informações.

<!-- end list -->

```php
<?php
// Inclui o autoloader do Composer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cria uma nova instância da PHPMailer
$mail = new PHPMailer(true);

try {
    // Configurações do Servidor
    $mail->isSMTP(); // Habilita o envio via SMTP
    $mail->Host       = 'smtp.gmail.com'; // Servidor SMTP do Gmail
    $mail->SMTPAuth   = true; // Habilita autenticação SMTP
    $mail->Username   = 'SEU_EMAIL_GMAIL@gmail.com'; // Seu endereço de e-mail do Gmail
    $mail->Password   = 'SUA_SENHA_DE_APLICATIVO'; // A senha de aplicativo que você gerou
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Habilita criptografia SSL/TLS (ou ENCRYPTION_STARTTLS)
    $mail->Port       = 465; // Porta TCP para SSL (ou 587 para TLS)
    $mail->CharSet    = 'UTF-8'; // Garante que caracteres especiais sejam enviados corretamente

    // Configurações do E-mail
    $mail->setFrom('SEU_EMAIL_GMAIL@gmail.com', 'Nome da sua Aplicação');
    $mail->addAddress('DESTINATARIO@exemplo.com'); // Adiciona um destinatário
    $mail->addReplyTo('SEU_EMAIL_GMAIL@gmail.com', 'Nome da sua Aplicação');

    // Conteúdo do E-mail
    $mail->isHTML(true); // Define o formato do e-mail como HTML
    $mail->Subject = 'Assunto do seu E-mail';
    $mail->Body    = 'Este é o corpo da mensagem em <b>HTML</b>';
    $mail->AltBody = 'Este é o corpo da mensagem em texto puro para clientes de e-mail que não suportam HTML';

    $mail->send();
    echo 'Mensagem enviada com sucesso!';
} catch (Exception $e) {
    echo "A mensagem não pôde ser enviada. Erro do Mailer: {$mail->ErrorInfo}";
}

?>
```

-----

### Por que usar a PHPMailer?

  * **Segurança**: Ela lida com a autenticação e criptografia (SSL/TLS) de forma segura, o que é um requisito para serviços como o Gmail.
  * **Confiabilidade**: É uma biblioteca robusta, amplamente testada e mantida pela comunidade, com muito menos chances de falhas de envio em comparação com a função `mail()` nativa.
  * **Funcionalidades completas**: Permite adicionar anexos, destinatários em cópia (CC e BCC), e-mails em formato HTML e muito mais.

Ao seguir esses passos e usar uma biblioteca confiável, você garante que sua aplicação possa enviar e-mails de forma segura e eficiente usando o Gmail.