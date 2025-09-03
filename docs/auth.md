Perfeito, Samir — vamos fechar o ciclo de autenticação com dois arquivos essenciais:  

1. **logout.php** — para encerrar a sessão com segurança  
2. **auth.php** — um “middleware” simples para proteger páginas restritas, garantindo que só usuários logados acessem  

Assim, qualquer página que precise de login vai incluir o `auth.php` no topo, e o `logout.php` vai cuidar de limpar a sessão e redirecionar.

---

## 📄 logout.php
```php
<?php
session_start();

// Limpa todas as variáveis de sessão
$_SESSION = [];

// Remove o cookie de sessão, se existir
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destrói a sessão
session_destroy();

// Redireciona para a página de login
header('Location: login.php');
exit;
```

---

## 📄 auth.php
```php
<?php
// auth.php — proteção para páginas restritas
session_start();

// Se não estiver logado, redireciona para o login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
```

---

## 🔹 Como usar o `auth.php`
Em qualquer página que precise de login, basta colocar **logo no início**:
```php
require_once '/var/secure/auth.php';
```
Assim, se o usuário tentar acessar sem estar logado, será redirecionado para o `login.php`.

---

💡 **Fluxo completo agora**:
- `login.php` → autentica e cria sessão  
- `auth.php` → protege páginas internas  
- `logout.php` → encerra sessão e volta para login  

Se quiser, posso já te entregar também um **dashboard.php** de exemplo, protegido pelo `auth.php`, para servir como página inicial do sistema depois do login. Quer que eu crie esse também?
