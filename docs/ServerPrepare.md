cd /var/www/html
mkdir -p bootstrap/css bootstrap/js
curl -L -o bootstrap/css/bootstrap.min.css https://github.com/twbs/bootstrap/releases/download/v5.3.2/bootstrap-5.3.2-dist.zip
unzip bootstrap-5.3.2-dist.zip
mv bootstrap-5.3.2-dist/css/bootstrap.min.css bootstrap/css/
mv bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js bootstrap/js/
rm -rf bootstrap-5.3.2-dist bootstrap-5.3.2-dist.zip


Perfeito, Samir — aqui está o **pacote final** já ajustado para **usar Bootstrap localmente**, sem depender de internet, e pronto para rodar no seu ambiente com as duas VMs (Web e Banco).  

---

## 📂 Estrutura Final

```
/var/www/html/
│
├── bootstrap/
│   ├── css/bootstrap.min.css
│   └── js/bootstrap.bundle.min.js
│
├── index.php
├── nova_solicitacao.php
├── aprovacoes.php
├── ver_solicitacao.php
├── consulta.php
├── processa_solicitacao.php
├── processa_aprovacao.php
└── /var/secure/config.php   # fora da pasta pública
```

---

## **1️⃣ /var/secure/config.php**  
*(fora do `/var/www/html` e com `chmod 600`)*

```php
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
```

---

## **2️⃣ index.php**
```php
<?php require_once '/var/secure/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Sistema de Solicitações de Backup</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">📦 Solicitações de Backup</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="nova_solicitacao.php">📝 Nova Solicitação</a></li>
        <li class="nav-item"><a class="nav-link" href="aprovacoes.php">✅ Aprovações Pendentes</a></li>
        <li class="nav-item"><a class="nav-link" href="consulta.php">🔍 Consultar Solicitações</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="alert alert-info">
    Bem-vindo! Escolha uma das opções no menu acima para continuar.
  </div>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## **3️⃣ nova_solicitacao.php**
*(mesmo formulário que já te passei, mas com `<link href="bootstrap/css/bootstrap.min.css">` e `<script src="bootstrap/js/bootstrap.bundle.min.js">`)*

---

## **4️⃣ aprovacoes.php**
*(igual ao anterior, mas com Bootstrap local)*

---

## **5️⃣ ver_solicitacao.php**
*(igual ao anterior, mas com Bootstrap local)*

---

## **6️⃣ consulta.php**
*(igual ao anterior, mas com Bootstrap local)*

---

## **7️⃣ processa_solicitacao.php**  
*(usa `$conn` do config.php e envia e-mail via Office 365)*

---

## **8️⃣ processa_aprovacao.php**  
*(atualiza status e envia e-mail de aprovação/recusa via Office 365)*

---

## 📦 **Bootstrap local**
Para baixar e instalar:
```bash
cd /var/www/html
mkdir -p bootstrap/css bootstrap/js
curl -L -o bootstrap/css/bootstrap.min.css https://github.com/twbs/bootstrap/releases/download/v5.3.2/bootstrap-5.3.2-dist.zip
unzip bootstrap-5.3.2-dist.zip
mv bootstrap-5.3.2-dist/css/bootstrap.min.css bootstrap/css/
mv bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js bootstrap/js/
rm -rf bootstrap-5.3.2-dist bootstrap-5.3.2-dist.zip
```

---

## 🖥 **Configuração das VMs**

### **VM Banco**
- Instalar MariaDB
- Criar banco, tabela e usuário remoto
- Liberar porta 3306

### **VM Web**
- Instalar Apache, PHP, Composer, PHPMailer
- Configurar HTTPS com certificado válido
- Colocar `config.php` em `/var/secure/`
- Subir os arquivos PHP e pasta `bootstrap` para `/var/www/html`

---

Se quiser, posso agora **te entregar todos os arquivos PHP completos já com Bootstrap local embutido** para você só copiar para a VM Web e rodar.  
Quer que eu já te envie **o código final de cada arquivo na íntegra** com Bootstrap local?
