<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Acesso - Sistema Financeiro</title>
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="card p-4 mx-auto shadow-sm" style="width: 350px; border-top: 4px solid #333;">
        <h4 class="text-center mb-4 fw-bold text-uppercase">SISTEMA FINANCEIRO</h4>
        
        <?php if(isset($erro)): ?>
            <div class='alert alert-danger py-2 small'><?= $erro ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-2">
                <label class="small fw-bold">Utilizador</label>
                <input type="text" name="u" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="small fw-bold">Palavra-passe</label>
                <input type="password" name="p" class="form-control" required>
            </div>
            <button name="login" class="btn btn-dark w-100 fw-bold">ENTRAR</button>
        </form>
    </div>
</body>
</html>