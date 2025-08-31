<?php
// Credenciais criptografadas geradas automaticamente
// config.php 

// Caminho para a chave de criptografia (fora do /var/www/html)
define('ENCRYPTION_KEY', b'\x82\xbb\x8a\x97\xc2\x98`\xde5=\xfey\xe12D\xdc\x95\x8cK!-\xeb\x98\xd1\xe6\x8d_J\xe9sV\xcb');
define('ENCRYPTION_IV', b'\x10Mw@h\x18\xbb\x13\xec\xf1h\x90\xf0?\xb6\x97');
$encrypted_host = 'EE13QGgYuxPs8WiQ8D+2l49FXfORWcBOP9s/xBz6sfw=';
$encrypted_user = 'EE13QGgYuxPs8WiQ8D+2l2OuiAYRyvnv3bgCJ6daCKw=';
$encrypted_pass = 'EE13QGgYuxPs8WiQ8D+2l5tp41MyB4nW+IsdEeQSf5Rui2jB7oQRjHjThu1iKr49';
$encrypted_db   = 'EE13QGgYuxPs8WiQ8D+2l/RKHjt3jDXEMjCwVKeyiVs=';
 // Funcao para descriptografar
function decrypt_clean($data) {
    $key = ENCRYPTION_KEY;
    $iv = ENCRYPTION_IV;
    $ciphertext = base64_decode($data);
    $decrypted = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

    // Remove caracteres não imprimíveis
    $cleaned = preg_replace('/[^ -~]/', '', $decrypted);

    // Remove prefixo "DML~$w" se estiver presente
    $cleaned = preg_replace('/^DML~\$w/', '', $cleaned);

    // Remove espaços extras no início/fim
    return trim($cleaned);
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
