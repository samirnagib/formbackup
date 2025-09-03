<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Portal de Solicitações de Backup</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }
    .btn-lg {
        padding: 1.5rem;
        font-size: 1.2rem;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
</style>
</head>
<body>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">📦 Portal de Solicitações de Backup</h1>
        <p class="lead">Escolha uma das opções abaixo</p>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h4>Nova Solicitação</h4>
                <p>Preencha o formulário para solicitar um novo backup.</p>
                <a href="publico.php" class="btn btn-success btn-lg">📝 Acessar</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h4>Área Restrita</h4>
                <p>Consulta e aprovação de solicitações.<br><small>(Requer login)</small></p>
                <a href="login.php" class="btn btn-primary btn-lg">🔐 Entrar</a>
            </div>
        </div>
    </div>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>