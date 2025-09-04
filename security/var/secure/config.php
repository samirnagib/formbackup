<?php
/**
 * config.php
 * Configuração segura de conexão ao banco de dados usando PDO
 * com credenciais criptografadas.
 */

// 🔐 Chave e IV (fora do /var/www/html)
define('ENCRYPTION_KEY', "\xce\x89\x91E\xc6\x94P \xac\x87\x9a\x92\x14F\xa6\x8c8\xda\x1d\xf1'n\xdb\x82\xe3:\x16\xdc\xa7\x8c\xad\x00");
define('ENCRYPTION_IV', "w\xd0\xd7\xca\xca\xbc\xeck\xd2\x87\x8a\xf7C\x0bq\xa1");

// 🔒 Credenciais criptografadas
$encrypted_host = 'd9DXysq87GvSh4r3QwtxofwWzswfsxwYadGDwn6AanE=';
$encrypted_user = 'd9DXysq87GvSh4r3QwtxoYYOtvKK5mE8Dlw1pa+HQG8=';
$encrypted_pass = 'd9DXysq87GvSh4r3QwtxofAbobhOmAmuSkl02m3LMjWU7/RAJsJ6RDqmnvqUkmLQ';
$encrypted_db   = 'd9DXysq87GvSh4r3QwtxoVAVSGYEhvL6tdZS1U1TEJw=';

/**
 * Descriptografa e limpa a string
 */
function decrypt_clean($data) {
    $key = ENCRYPTION_KEY;
    $iv = ENCRYPTION_IV;
    $ciphertext = base64_decode($data);
    $decrypted = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

    // Remove caracteres não imprimíveis
    $cleaned = preg_replace('/[^\x20-\x7E]/', '', $decrypted);
    // Remove prefixo "DML~$w" se existir
    $cleaned = preg_replace('/^DML~\$w/', '', $cleaned);
    // Remove espaços extras
    return trim($cleaned);
}

// 🔓 Descriptografando credenciais
$db_host = decrypt_clean($encrypted_host);
$db_user = decrypt_clean($encrypted_user);
$db_pass = decrypt_clean($encrypted_pass);
$db_name = decrypt_clean($encrypted_db);
$charset = 'utf8mb4';

// 📡 Criando conexão PDO
$dsn = "mysql:host={$db_host};dbname={$db_name};charset={$charset}";

try {
    $conn = new PDO($dsn, $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Em produção, logar o erro e mostrar mensagem genérica
    die("Erro ao conectar ao banco de dados.");
}