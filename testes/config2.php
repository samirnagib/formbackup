<?php
// Credenciais criptografadas geradas automaticamente
// config.php 

// Caminho para a chave de criptografia (fora do /var/www/html)
define('ENCRYPTION_KEY', b'\xd4\x17\x0fQ\xea\xe0\xb4\xb64\xa3WR~8\x1f\xfc\xcc%\xc7\x9b<\xa2\x10\x82\xaaRS\xbc\xcf\xe7\xad\x1e');
define('ENCRYPTION_IV', "r+qh7GNgjL02TF6Lv7dUqw==");
$encrypted_host = 'r+qh7GNgjL02TF6Lv7dUq7MFd7hoLyY9aghganVMFj0=';
$encrypted_user = 'r+qh7GNgjL02TF6Lv7dUq/P1Gqh+INJ+aj4f4+A2H/c=';
$encrypted_pass = 'r+qh7GNgjL02TF6Lv7dUq+yO9Lhg5gt9b52X7u+5ZA8pHFARYq1MYz0nkzb+HR6u';
$encrypted_db   = 'r+qh7GNgjL02TF6Lv7dUq3oBpSLSXJWwi/Jfh0NZWR0=';
 // Funcao para descriptografar
function decrypt($data) {
    $key = ENCRYPTION_KEY;
    $data = base64_decode($data);
    $iv = ENCRYPTION_IV;
    $ciphertext = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
    return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);
}

// Credenciais criptografadas (geradas previamente)

// Descriptografando para uso
$db_host = decrypt($encrypted_host);
$db_user = decrypt($encrypted_user);
$db_pass = decrypt($encrypted_pass);
$db_name = decrypt($encrypted_db);

// Criando conexao
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Falha na conexao: " . $conn->connect_error);
}
?>
