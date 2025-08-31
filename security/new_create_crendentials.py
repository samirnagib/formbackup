from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes
from cryptography.hazmat.primitives import padding
from cryptography.hazmat.backends import default_backend
import os, base64

def iv_generate() -> str:
    return os.urandom(16)

def criptografar(data: str, iv_code: str, key: bytes) -> str:
    iv = iv_code
    cipher = Cipher(algorithms.AES(key), modes.CBC(iv), backend=default_backend())
    encryptor = cipher.encryptor()

    padder = padding.PKCS7(128).padder()
    padded_data = padder.update(data.encode()) + padder.finalize()

    ciphertext = encryptor.update(padded_data) + encryptor.finalize()
    return base64.b64encode(iv + ciphertext).decode()

def main():
    menu()


def menu():
    print("Gerador de Credenciais Criptografadas para config.php")
    print("-----------------------------------------------------")
    print("1 - Gerar nova chave AES e criptografar credenciais")
    print("2 - Sair")
    print("\n \n Escolha uma opção:")
    input_op = input("Opção: ").strip()
    if input_op == "1":
        credenciais()
    elif input_op == "2":
        exit_program()
    else:
        print("Opção inválida. Tente novamente.")
        menu()
    
    
def credenciais():   
    print("\n")
    print("Entre com a chave AES de 32 bytes (256 bits) para criptografia, ou dexe em branco para gerar uma nova:")
    user_key = input("Chave (32 bytes): ").strip()
    if user_key == "":
        key = os.urandom(32)
        code_iv = iv_generate()
        print(f"Chave gerada.: {key}") 
        print(f"IV gerado....: {code_iv}")
    elif len(user_key) != 32:
        print("Erro: A chave deve ter exatamente 32 bytes.")
        return
    print("Entre com o Host, User, Pass e DB para criptografar:") 
    host = input("Host: ").strip()
    user = input("User: ").strip() 
    password = input("Pass: ").strip()
    db = input("DB: ").strip()
    
    cHost = criptografar(host, code_iv, key)
    cUser = criptografar(user, code_iv, key)
    cPass = criptografar(password, code_iv, key)
    cDB = criptografar(db, code_iv, key)
    print("\nCole estes valores no config.php:\n")
    print("Host:", cHost)  
    print("User:", cUser)  
    print("Pass:", cPass)
    print("DB:",   cDB)
    print(f"\nIV (16 bytes): \"{code_iv}\"")
    print("\nGuarde esta chave AES de 32 bytes com segurança:")
    print(key)
    print("\n")
    print("Deseja criar o aquivo config.php agora? (s/n)")
    criar_config = input().strip().lower()
    if criar_config == 's':
        with open("config.php", "w") as f:
            f.write("<?php\n")
            f.write("// Credenciais criptografadas geradas automaticamente\n")
            f.write("// config.php \n\n") 
            f.write("// Caminho para a chave de criptografia (fora do /var/www/html)\n")
            f.write(f"define('ENCRYPTION_KEY', {key});\n")
            f.write(f"define('ENCRYPTION_IV', {code_iv});\n")
            f.write(f"$encrypted_host = '{cHost}';\n")
            f.write(f"$encrypted_user = '{cUser}';\n")
            f.write(f"$encrypted_pass = '{cPass}';\n")
            f.write(f"$encrypted_db   = '{cDB}';\n")
            f.write(" // Funcao para descriptografar\n")
            f.write("function decrypt_clean($data) {\n")
            f.write("    $key = ENCRYPTION_KEY;\n")
            f.write("    $iv = ENCRYPTION_IV;\n")
            f.write("    $ciphertext = base64_decode($data);\n")
            f.write("    $decrypted = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);\n")
            f.write("\n")
            f.write("    // Remove caracteres não imprimíveis\n")
            f.write("    $cleaned = preg_replace('/[^\x20-\x7E]/', '', $decrypted);\n")
            f.write("\n")
            f.write("    // Remove prefixo \"DML~$w\" se estiver presente\n")
            f.write("    $cleaned = preg_replace('/^DML~\$w/', '', $cleaned);\n")
            f.write("\n")
            f.write("    // Remove espaços extras no início/fim\n")
            f.write("    return trim($cleaned);\n")
            f.write("}\n")

            f.write("\n")
            f.write("// Credenciais criptografadas (geradas previamente)\n")
            f.write("\n")
            f.write("// Descriptografando para uso\n")
            f.write(f"$db_host = decrypt($encrypted_host);\n")
            f.write(f"$db_user = decrypt($encrypted_user);\n")
            f.write(f"$db_pass = decrypt($encrypted_pass);\n")
            f.write(f"$db_name = decrypt($encrypted_db);\n")
            f.write("\n")
            f.write("// Criando conexao\n")
            f.write("$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);\n")
            f.write("if ($conn->connect_error) {\n")
            f.write("    die("'"Falha na conexao: "'" . $conn->connect_error);\n")
            f.write("}\n")
            f.write("?>\n") 
            f.close()
        print("Arquivo config.php criado com sucesso.")
    elif criar_config == 'n':
        print("Arquivo config.php não foi criado.")
    else:
        print("Opção inválida. Voltando ao menu.")
    
def exit_program():
    print("Saindo...")
    exit()    


main()