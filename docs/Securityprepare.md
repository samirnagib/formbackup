Ótimo, Samir — então vamos fechar o **kit completo** para você gerar as credenciais criptografadas em **Python** e depois usá‑las no **PHP** com descriptografia automática no `config.php`.  

A ideia é:  
1. **Gerar as credenciais criptografadas** com um script Python (rodado localmente, fora do servidor).  
2. **Colar os valores criptografados** no `config.php`.  
3. **Descriptografar no PHP** usando a mesma chave e algoritmo.  
4. **Incluir `config.php`** em todos os arquivos que precisam de conexão ao banco.

---

## **1️⃣ Script Python para gerar credenciais criptografadas**

> Salve como `gerar_credenciais.py` e rode **localmente** (não no servidor de produção).

```python
from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes
from cryptography.hazmat.primitives import padding
from cryptography.hazmat.backends import default_backend
import os, base64

# Chave AES de 32 bytes (256 bits) — guarde em local seguro
KEY = b'sua_chave_aleatoria_de_32_bytes_aqui!!'  # Ex: use os.urandom(32) para gerar

def encrypt(data: str, key: bytes) -> str:
    iv = os.urandom(16)
    cipher = Cipher(algorithms.AES(key), modes.CBC(iv), backend=default_backend())
    encryptor = cipher.encryptor()

    padder = padding.PKCS7(128).padder()
    padded_data = padder.update(data.encode()) + padder.finalize()

    ciphertext = encryptor.update(padded_data) + encryptor.finalize()
    return base64.b64encode(iv + ciphertext).decode()

print("Cole estes valores no config.php:\n")
print("Host:", encrypt('IP_DA_VM_BANCO', KEY))
print("User:", encrypt('usuario', KEY))
print("Pass:", encrypt('senha', KEY))
print("DB:",   encrypt('seu_banco', KEY))

print("\nGuarde esta chave AES de 32 bytes com segurança:")
print(KEY)
```

💡 **Dica:**  
- Para gerar uma chave segura:  
  ```python
  import os
  print(os.urandom(32))
  ```
- Essa chave **não** deve ficar no repositório público nem no `/var/www/html`.

---

## **2️⃣ Arquivo `config.php` com descriptografia**

> Salve **fora** da pasta pública do Apache, por exemplo em `/var/secure/config.php`, e dê permissão restrita:  
> `chmod 600 /var/secure/config.php`

```php
<?php
// config.php

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
```

---

## **3️⃣ Como usar nos outros arquivos**

Em qualquer arquivo PHP que precise do banco:

```php
<?php
require_once '/var/secure/config.php';

// Agora $conn já está pronto para uso
$result = $conn->query("SELECT * FROM tabela");
```

---

## **4️⃣ Segurança extra**
- **Nunca** suba a chave AES para repositórios públicos.
- Coloque `config.php` **fora** do `/var/www/html`.
- Restrinja permissões: `chmod 600 config.php` e `chown www-data:www-data config.php`.
- Se possível, armazene a chave AES em **variável de ambiente** e leia no PHP com `getenv()`.

---

Se quiser, posso já **reescrever todos os seus arquivos PHP** (`index.php`, `nova_solicitacao.php`, `aprovacoes.php`, `ver_solicitacao.php`, `consulta.php`, `processa_solicitacao.php`, `processa_aprovacao.php`) para:
- Usarem `require_once '/var/secure/config.php'`
- Removerem credenciais hardcoded
- Garantirem que a conexão seja fechada corretamente

Quer que eu já te envie **o pacote final com Bootstrap + conexão centralizada e criptografada** pronto para subir na sua VM?
