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