Entendido, Samir — vamos ajustar o pacote para que **toda a parte web rode exclusivamente em HTTPS (porta 443)** usando um **certificado interno** (self-signed ou emitido pela sua CA corporativa).  
Isso garante que o formulário, a listagem e as atualizações de status trafeguem criptografados entre o navegador e a VM Web.

---

## 🔒 Alterações no Pacote

A boa notícia é que **o código HTML/PHP não precisa mudar** — a mudança é toda na **configuração do Apache** na VM Web.

---

### **1️⃣ Gerar um certificado interno (self-signed ou CA interna)**

Se for **self-signed**:
```bash
sudo mkdir /etc/ssl/private
sudo openssl req -x509 -nodes -days 365 \
  -newkey rsa:2048 \
  -keyout /etc/ssl/private/backup.key \
  -out /etc/ssl/certs/backup.crt
```
- **Common Name (CN)** → use o FQDN da VM Web (ex.: `backup.intra.local`).
- Se tiver **CA corporativa**, gere um CSR e peça para a CA assinar.

---

### **2️⃣ Instalar módulos SSL no Apache**
```bash
sudo dnf install mod_ssl
```

---

### **3️⃣ Configurar o Apache para usar apenas HTTPS**
Edite o arquivo de configuração SSL (normalmente `/etc/httpd/conf.d/ssl.conf`):

```apache
<VirtualHost *:443>
    ServerName backup.intra.local

    DocumentRoot /var/www/html

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/backup.crt
    SSLCertificateKeyFile /etc/ssl/private/backup.key

    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

### **4️⃣ Redirecionar todo o tráfego HTTP para HTTPS**
No arquivo `/etc/httpd/conf/httpd.conf` ou em um novo `/etc/httpd/conf.d/redirect.conf`:

```apache
<VirtualHost *:80>
    ServerName backup.intra.local
    Redirect permanent / https://backup.intra.local/
</VirtualHost>
```

---

### **5️⃣ Ajustar firewall**
```bash
sudo firewall-cmd --permanent --remove-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

---

### **6️⃣ Testar**
- Acesse `https://backup.intra.local`  
- O navegador pode alertar sobre certificado não confiável (se for self-signed).  
  - Em ambiente corporativo, adicione o certificado da CA interna aos navegadores para evitar o alerta.

---

### **7️⃣ Impacto no Pacote**
- **index.html**, **processa_backup.php**, **listar_backups.php** e **atualiza_status.php** continuam iguais.
- Apenas os **links e iframes** devem usar `https://` no lugar de `http://`.
- O envio de e-mails via Office 365 não muda — continua usando porta 587/TLS.

---

💡 **Sugestão extra**  
Se quiser, posso já te entregar **o pacote com todos os arquivos HTML/PHP ajustados para apontar para `https://backup.intra.local`** e um **guia rápido para importar o certificado no Windows/Linux** dos usuários, para que não apareça o alerta de segurança.  

Quer que eu já prepare essa versão final ajustada para HTTPS com certificado interno?


-------------

Perfeito, Samir — se você vai usar um **certificado emitido por uma Autoridade Certificadora (CA)** confiável (como DigiCert, GlobalSign, Sectigo, Let’s Encrypt, etc.), o processo fica ainda mais seguro e elimina os avisos de “conexão não segura” nos navegadores.

A boa notícia é que o **pacote de páginas HTML/PHP** que montamos não muda — a alteração é toda na **configuração do Apache** na VM Web para usar **apenas a porta 443** com o certificado válido.

---

## 🔐 Passos para configurar HTTPS com certificado de CA

### **1️⃣ Obter o certificado**
- Solicite o certificado para o **FQDN** da sua VM Web (ex.: `backup.empresa.com`).
- A CA vai te fornecer:
  - **Certificado do servidor** (`backup.empresa.com.crt` ou `.pem`)
  - **Chave privada** (`backup.empresa.com.key`)
  - **Cadeia intermediária** (`ca-bundle.crt` ou `.pem`)

> Guarde a chave privada em local seguro e com permissões restritas.

---

### **2️⃣ Instalar módulos SSL no Apache**
```bash
sudo dnf install mod_ssl
```

---

### **3️⃣ Configurar o Apache para usar apenas HTTPS**
Edite o arquivo `/etc/httpd/conf.d/ssl.conf` ou crie um novo `/etc/httpd/conf.d/backup.conf`:

```apache
<VirtualHost *:443>
    ServerName backup.empresa.com

    DocumentRoot /var/www/html

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/backup.empresa.com.crt
    SSLCertificateKeyFile /etc/ssl/private/backup.empresa.com.key
    SSLCertificateChainFile /etc/ssl/certs/ca-bundle.crt

    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

### **4️⃣ Redirecionar todo o tráfego HTTP para HTTPS**
Crie `/etc/httpd/conf.d/redirect.conf`:

```apache
<VirtualHost *:80>
    ServerName backup.empresa.com
    Redirect permanent / https://backup.empresa.com/
</VirtualHost>
```

---

### **5️⃣ Ajustar firewall**
```bash
sudo firewall-cmd --permanent --remove-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

---

### **6️⃣ Testar**
- Acesse `https://backup.empresa.com`
- O navegador deve mostrar o cadeado verde/seguro sem alertas.

---

## 📦 Impacto no pacote que montamos
- **index.html**, **processa_backup.php**, **listar_backups.php** e **atualiza_status.php** continuam iguais.
- Apenas os **links internos** e o `iframe` de listagem devem usar `https://backup.empresa.com/...`.
- O envio de e-mails via Office 365 não muda — continua usando porta 587/TLS.

---

💡 **Sugestão extra**  
Se quiser, posso já te devolver **o pacote final** com:
- Todos os arquivos HTML/PHP ajustados para `https://backup.empresa.com`
- Guia rápido para instalar o certificado da CA no Apache
- Configuração de redirecionamento automático HTTP → HTTPS

Assim você já sobe o sistema na VM Web e ele estará **100% seguro e pronto para produção**.  

Quer que eu já te monte esse pacote final pronto para produção com HTTPS e certificado de CA?
