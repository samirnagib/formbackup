Perfeito, Samir! Vamos integrar a tela de auditoria como mais um item no menu do `dashboard.php`. Aqui está o plano completo:

---

## 🧩 1. Adição ao menu do `dashboard.php`

No trecho onde estão os itens do menu lateral ou superior, adicione:

```php
<li><a href="auditoria.php">📋 Auditoria</a></li>
```

Você pode ajustar o ícone ou estilo conforme o restante do dashboard.

---

## 🖥️ 2. Criação do `auditoria.php`

Aqui está um esqueleto funcional para a tela de auditoria:

```php
<?php
include('conexao.php'); // conexão com o banco
include('verifica_login.php'); // se houver controle de sessão

// Filtro opcional
$filtro = isset($_GET['acao']) ? $_GET['acao'] : '';

$sql = "SELECT * FROM logs_auditoria";
if ($filtro) {
    $sql .= " WHERE acao LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filtro);
} else {
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Auditoria</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ccc; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>📋 Logs de Auditoria</h2>

    <form method="get">
        <input type="text" name="acao" placeholder="Filtrar por ação" value="<?= htmlspecialchars($filtro) ?>">
        <button type="submit">Filtrar</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Ação</th>
            <th>ID Solicitação</th>
            <th>IP</th>
            <th>User Agent</th>
            <th>Data/Hora</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['acao']) ?></td>
            <td><?= $row['id_solicitacao'] ?></td>
            <td><?= $row['ip'] ?></td>
            <td><?= htmlspecialchars($row['user_agent']) ?></td>
            <td><?= $row['data_hora'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
```

---

## 🔐 3. Segurança e performance

- Use `htmlspecialchars()` para evitar XSS.
- Paginação pode ser adicionada se o volume de logs for alto.
- Filtros por data, IP ou ID também podem ser incluídos.

---

Se quiser, posso preparar a versão com filtros avançados, exportação para Excel ou CSV, ou até gráficos de atividade por período.  
Quer que eu evolua para uma versão com esses recursos?
