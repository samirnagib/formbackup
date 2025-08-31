<?php
// config.php

// Caminho para a chave de criptografia (fora do /var/www/html)
define('ENCRYPTION_KEY', 'coloque_aqui_uma_chave_aleatoria_grande');

// Função para descriptografar
function decrypt($data) {
    $key = ENCRYPTION_KEY;
    $data = base64_decode($data);
    $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
    $ciphertext = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
    return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);
}

// Credenciais criptografadas (geradas previamente)
$db_host_enc = 'ENCRYPTED_HOST';
$db_user_enc = 'ENCRYPTED_USER';
$db_pass_enc = 'ENCRYPTED_PASS';
$db_name_enc = 'ENCRYPTED_DB';

// Descriptografando para uso
$db_host = decrypt($db_host_enc);
$db_user = decrypt($db_user_enc);
$db_pass = decrypt($db_pass_enc);
$db_name = decrypt($db_name_enc);

// Criando conexão
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>