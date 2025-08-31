Claro, Samir — vamos organizar tudo o que fizemos até aqui e já ajustar o projeto para **usar o Bootstrap localmente**, sem depender de acesso externo.  

---

## 📜 **Resumo da nossa conversa e do projeto**

1. **Objetivo**  
   Criar um sistema web para gerenciar solicitações de backup, com:
   - Formulário de entrada
   - Página de aprovações (aprovar/recusar com envio de e-mail ao solicitante)
   - Página de consulta
   - Layout responsivo com Bootstrap
   - Conexão centralizada ao banco de dados com credenciais criptografadas
   - Envio de e-mails via Office 365 SMTP

2. **Estrutura do sistema**
   ```
   /var/www/html/
   ├── index.php
   ├── nova_solicitacao.php
   ├── aprovacoes.php
   ├── ver_solicitacao.php
   ├── consulta.php
   ├── processa_solicitacao.php
   ├── processa_aprovacao.php
   ├── /bootstrap/ (arquivos locais do Bootstrap)
   /var/secure/config.php  (fora da pasta pública)
   ```

3. **Segurança**
   - Credenciais do banco criptografadas com AES-256-CBC
   - Chave AES guardada fora do diretório público
   - `config.php` com permissões restritas (`chmod 600`)

4. **Fluxo**
   - Usuário cadastra solicitação → registro no MySQL + e-mail de confirmação
   - Página de aprovações lista pendentes → abrir → aprovar/recusar → e-mail automático
   - Página de consulta lista todas as solicitações

---

## 🖥 **Configuração das VMs (Oracle Linux 9)**

### **VM Banco (MySQL/MariaDB)**
```bash
sudo dnf install mariadb-server mariadb
sudo systemctl enable --now mariadb
sudo mysql_secure_installation
```
- Criar banco e tabela:
```sql
CREATE DATABASE seu_banco;
-- Executar o script CREATE TABLE que definimos
```
- Criar usuário remoto:
```sql
CREATE USER 'usuario'@'IP_DA_VM_WEB' IDENTIFIED BY 'senha';
GRANT ALL PRIVILEGES ON seu_banco.* TO 'usuario'@'IP_DA_VM_WEB';
FLUSH PRIVILEGES;
```
- Liberar porta:
```bash
sudo firewall-cmd --permanent --add-service=mysql
sudo firewall-cmd --reload
```

---

### **VM Web (Apache + PHP + PHPMailer + Bootstrap local)**
```bash
sudo dnf -y install httpd php php-mysqli php-cli php-json php-mbstring unzip
sudo systemctl enable --now httpd
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```
- Instalar Composer e PHPMailer:
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
cd /var/www/html
composer require phpmailer/phpmailer
```
- Colocar `config.php` em `/var/secure/` com `chmod 600`
- Configurar Apache para HTTPS com certificado válido (CA pública ou interna)

---

## 🎨 **Alterando para Bootstrap local**

Como você não tem acesso externo, vamos baixar o Bootstrap e servir localmente:

1. **Baixar Bootstrap**
   ```bash
   cd /var/www/html
   mkdir bootstrap
   cd bootstrap
   curl -O https://github.com/twbs/bootstrap/releases/download/v5.3.2/bootstrap-5.3.2-dist.zip
   unzip bootstrap-5.3.2-dist.zip
   rm bootstrap-5.3.2-dist.zip
   ```
   Isso criará:
   ```
   /var/www/html/bootstrap/css/bootstrap.min.css
   /var/www/html/bootstrap/js/bootstrap.bundle.min.js
   ```

2. **Alterar links nos arquivos PHP**
   Onde antes tínhamos:
   ```html
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
   ```
   Agora será:
   ```html
   <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
   <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
   ```

3. **Aplicar essa alteração em todos os arquivos** (`index.php`, `nova_solicitacao.php`, `aprovacoes.php`, `ver_solicitacao.php`, `consulta.php`).

---

## ✅ **Próximos passos**
- Gerar credenciais criptografadas com o script Python que te passei
- Colar no `config.php`
- Subir todos os arquivos para `/var/www/html` (exceto `config.php`, que vai em `/var/secure/`)
- Testar o fluxo completo em HTTPS

---

Se quiser, posso já **te devolver todos os arquivos PHP com o Bootstrap local configurado** e prontos para colocar na VM Web.  
Assim você só precisa subir a pasta e rodar.  

Quer que eu já te envie essa versão final com Bootstrap local?
