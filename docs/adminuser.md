Entendi, Samir — se o banco estiver zerado, o `login.php` não vai encontrar nenhum registro e, portanto, ninguém conseguirá entrar.  
Existem três formas seguras de resolver isso:

---

## 1️⃣ Criar um **usuário administrador padrão** na instalação
Você pode rodar um script SQL manualmente logo após criar o banco:

```sql
INSERT INTO usuarios (usuario, nome, email, senha_hash)
VALUES (
    'admin',
    'Administrador',
    'admin@seudominio.com',
    '$2y$10$QqzQnK1FZ0nZp6hGfZkZQeZbZ8ZbZ8ZbZ8ZbZ8ZbZ8ZbZ8ZbZ8Zb' -- hash de uma senha
);
```
> Esse hash é gerado com `password_hash('sua_senha', PASSWORD_DEFAULT)` no PHP.  
> Assim, você já começa com um login funcional (`admin` / `sua_senha`).

---

## 2️⃣ Criar um **script de bootstrap** temporário
Você pode criar um arquivo `criar_admin.php` só para uso inicial:

```php
<?php
require_once '/var/secure/config.php';

$usuario = 'admin';
$nome    = 'Administrador';
$email   = 'admin@seudominio.com';
$senha   = password_hash('senha123', PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (usuario, nome, email, senha_hash)
        VALUES (:usuario, :nome, :email, :senha)";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':usuario', $usuario);
$stmt->bindValue(':nome', $nome);
$stmt->bindValue(':email', $email);
$stmt->bindValue(':senha', $senha);
$stmt->execute();

echo "Usuário admin criado com sucesso!";
```
> Depois de rodar uma vez, **apague o arquivo** para não deixar brecha de segurança.

---

## 3️⃣ Adicionar lógica no `login.php` para detectar banco vazio
Se quiser automatizar, no início do `login.php` você pode verificar:

```php
$total = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch()['total'];
if ($total == 0) {
    header('Location: criar_admin.php');
    exit;
}
```
Assim, se não houver usuários, o sistema redireciona para uma tela de criação do primeiro administrador.

---

💡 **Minha recomendação**:  
O mais seguro é criar o primeiro usuário manualmente via script ou SQL, e depois apagar o script. Isso evita que qualquer pessoa possa criar um admin sem controle.

---

Se quiser, posso já te preparar um **`criar_admin.php`** bem protegido, que só funcione uma vez e depois se auto-apague, para você inicializar o sistema com segurança. Quer que eu faça isso?
