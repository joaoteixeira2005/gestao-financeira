<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Ativos 2026</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Estilos Personalizados do Sistema */
        body { 
            background-color: #f4f7f6; 
            color: #333; 
            font-family: 'Inter', sans-serif; 
        }

        .card { 
            border-radius: 8px; 
            border: 1px solid #e0e0e0; 
            transition: transform 0.2s;
        }

        /* Cores Navy e Profissionais */
        .bg-navy { background-color: #1a2a44; color: white; }
        
        .progress { background-color: #e9ecef; border-radius: 10px; }
        .progress-bar { background-color: #2c3e50; border-radius: 10px; }

        /* Estilo da Etiqueta "FIXO" */
        .badge-fixo {
            font-size: 0.65rem; 
            padding: 3px 10px; 
            border-radius: 50px;
            background-color: #ffffff; 
            color: #555; 
            font-weight: 800;
            text-transform: uppercase; 
            border: 1.5px solid #ddd;
            margin-left: 10px; 
            letter-spacing: 0.5px;
            display: inline-block;
            vertical-align: middle;
        }

        /* Tabelas */
        .table { font-size: 0.9rem; }
        .table thead { background-color: #f8f9fa; color: #666; text-transform: uppercase; font-size: 0.75rem; }
        
        /* Links e Botões */
        .btn-link-dark { color: #555; text-decoration: none; font-weight: 600; }
        .btn-link-dark:hover { color: #000; text-decoration: underline; }
        
        /* Navbar */
        .navbar { border-bottom: 3px solid #eee; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4 py-3 shadow-sm">
    <div class="container">
        <span class="navbar-brand mb-0 h1 small fw-bold text-uppercase tracking-wider">
            SISTEMA DE GESTÃO FINANCEIRA
        </span>
        <div class="d-flex align-items-center">
            <span class="text-light small me-3 opacity-75">
                Utilizador: <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>
            </span>
            <a href="?logout=1" class="btn btn-sm btn-danger px-3 fw-bold shadow-sm">SAIR</a>
        </div>
    </div>
</nav>