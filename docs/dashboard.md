Perfeito, Samir — vamos fechar o conjunto com um **dashboard.php** simples, já protegido pelo `auth.php`, para servir como página inicial do sistema depois do login.  

Ele vai:  
- Garantir que só usuários logados acessem (via `auth.php`)  
- Mostrar uma mensagem de boas-vindas com o nome do usuário  
- Ter links rápidos para as funções principais (listar, adicionar, sair)  

---

## 📄 dashboard.php
```php
<?php
// Protege a página — só acessa se estiver logado
require_once '/var/secure/auth.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        header { background: #007BFF; color: #fff; padding: 15px; }
        header h1 { margin: 0; font-size: 20px; }
        nav { background: #e9ecef; padding: 10px; }
        nav a {
            margin-right: 15px; text-decoration: none; color: #007BFF;
            font-weight: bold;
        }
        nav a:hover { text-decoration: underline; }
        main { padding: 20px; }
    </style>
</head>
<body>
<header>
    <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</h1>
</header>
<nav>
    <a href="listar_usuarios.php">📋 Listar Usuários</a>
    <a href="adicionar_usuario.php">➕ Adicionar Usuário</a>
    <a href="logout.php">🚪 Sair</a>
</nav>
<main>
    <h2>Painel de Controle</h2>
    <p>Escolha uma das opções acima para gerenciar o sistema.</p>
</main>
</body>
</html>
```

---

### 🔹 Como ele se encaixa no fluxo
1. **login.php** → autentica e cria sessão  
2. **dashboard.php** → página inicial protegida pelo `auth.php`  
3. **listar_usuarios.php / adicionar_usuario.php / editar_usuario.php / excluir_usuario.php** → funções de manutenção  
4. **logout.php** → encerra sessão  

---

Se quiser, posso já te entregar **todos os arquivos finais juntos** (login, logout, auth, dashboard, listar, adicionar, editar, excluir) já no padrão PDO e prontos para uso, para você só colocar no servidor e rodar.  
Quer que eu já monte esse pacote completo para você?
