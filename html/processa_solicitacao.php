<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

$conn = new mysqli("IP_DA_VM_BANCO", "usuario", "senha", "seu_banco");

$id = intval($_POST['ID']);
$status = $_POST['Status'];
$email = $_POST['EmailRequisitante'];
$obs = $_POST['Observacoes'];

$conn->query("UPDATE `SolicitaçõesBackup` SET Status='$status' WHERE ID=$id");

// Envia e-mail
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.office365.com';
$mail->SMTPAuth = true;
$mail->Username = 'seu_email@seudominio.com';
$mail->Password = 'sua_senha_ou_senha_app';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('seu_email@seudominio.com', 'Sistema de Backup');
$mail->addAddress($email);

$mail->isHTML(true);
$mail->Subject = "Resultado da Solicitação #$id";
$mail->Body = "<p>Seu pedido foi <strong>$status</strong>.</p><p>$obs</p>";

$mail->send();

echo "Decisão registrada e e-mail enviado.";
$conn->close();
?>