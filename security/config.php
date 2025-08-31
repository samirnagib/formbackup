<?php
// Chave AES de 32 bytes — mesma usada no Python
define('ENCRYPTION_KEY', 'sua_chave_aleatoria_de_32_bytes_aqui!!');

function decrypt_data($data) {
    $key = ENCRYPTION_KEY;
    $data = base64_decode($data);
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);
    return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
}

// Valores criptografados gerados pelo Python
$db_host_enc = 'COLE_AQUI_O_HOST_ENCRYPTED';
$db_user_enc = 'COLE_AQUI_O_USER_ENCRYPTED';
$db_pass_enc = 'COLE_AQUI_O_PASS_ENCRYPTED';
$db_name_enc = 'COLE_AQUI_O_DB_ENCRYPTED';

// Descriptografando
$db_host = decrypt_data($db_host_enc);
$db_user = decrypt_data($db_user_enc);
$db_pass = decrypt_data($db_pass_enc);
$db_name = decrypt_data($db_name_enc);

// Criando conexão
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>