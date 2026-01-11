<?php
session_start(); 
require_once 'config.php';
require_once 'functions.php'; 

// --- 1. CONTROLO DE ACESSO ---
if (isset($_POST['login'])) {
    // Pegamos nos dados do formulário e limpamos espaços
    $u_form = trim($_POST['u'] ?? '');
    $p_form = trim($_POST['p'] ?? '');

    // Verificação Admin (Convertemos tudo para minúsculas para não falhar)
    if (strtolower($u_form) === strtolower(USER_ADMIN) && $p_form === PASS_ADMIN) {
        $_SESSION['user'] = USER_ADMIN; 
        $_SESSION['role'] = 'admin';
        header("Location: index.php"); 
        exit();
    } 
    // Verificação Convidado
    elseif (strtolower($u_form) === strtolower(USER_GUEST) && $p_form === PASS_GUEST) {
        $_SESSION['user'] = USER_GUEST; 
        $_SESSION['role'] = 'user';
        header("Location: index.php"); 
        exit();
    } else { 
        $erro = "Credenciais inválidas"; 
    }
}

if (isset($_GET['logout'])) { 
    session_destroy(); 
    header("Location: index.php"); 
    exit(); 
}

// Caminho direto para o login na raiz
if (!isset($_SESSION['user'])): 
    include 'login_form.php'; 
    exit(); 
endif; 

$is_admin = isAdmin(); 

// --- 2. PROCESSAMENTO DE ACÇÕES ---
$mes_filtro = filtrar($_GET['mes_filtro'] ?? 'Janeiro');
$todos_meses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

if ($is_admin) {
    if (isset($_POST['editar_movimento'])) {
        db_editar((int)$_POST['id_edit'], filtrar($_POST['desc_edit']), (float)$_POST['val_edit']);
        header("Location: ?mes_filtro=$mes_filtro"); exit();
    }
    if (isset($_GET['apagar'])) {
        db_apagar((int)$_GET['apagar']);
        header("Location: ?mes_filtro=$mes_filtro"); exit();
    }
    if (isset($_POST['lancar_fixos'])) {
        db_sincronizar_fixos($mes_filtro);
        header("Location: ?mes_filtro=$mes_filtro"); exit();
    }
    if (isset($_POST['add_recorrencia'])) {
        db_inserir_fixo('Casa', filtrar($_POST['desc_rec']), (float)$_POST['val_rec']);
        header("Location: ?mes_filtro=$mes_filtro"); exit();
    }
    if (isset($_GET['del_rec'])) {
        db_apagar_fixo((int)$_GET['del_rec']);
        header("Location: ?mes_filtro=$mes_filtro"); exit();
    }
    if (isset($_POST['fechar_mes'])) {
        db_fechar_mes($_POST['mes_fechar'], (float)$_POST['valor_sobra']);
        header("Location: ?mes_filtro=".$_POST['mes_fechar']); exit();
    }
    if (isset($_POST['reabrir_mes'])) {
        db_reabrir_mes((int)$_POST['id_fecho']);
        header("Location: ?mes_filtro=$mes_filtro"); exit();
    }
}

if (isset($_POST['guardar'])) {
    db_inserir($mes_filtro, $_POST['categoria'], filtrar($_POST['descricao']), (float)$_POST['valor']);
    header("Location: ?mes_filtro=$mes_filtro"); exit();
}

// --- 3. DADOS ---
$stats = db_get_stats($mes_filtro);
$saldo_mes = $stats['entradas'] - $stats['gastos'];
$percentagem = min(($stats['poupanca'] / 20000) * 100, 100);
$id_fecho = db_is_mes_fechado($mes_filtro);

// --- 4. INTERFACE ---
include 'header.php'; // Caminho direto para o header na raiz
?>

<div class="container pb-5">
    <div class="card p-4 mb-4 bg-white shadow-sm border-0">
        <div class="row align-items-center">
            <div class="col-md-4 text-center border-end">
                <small class="text-uppercase fw-bold text-muted small">Poupança Acumulada</small>
                <h2 class="fw-bold mb-0 text-navy"><?= number_format($stats['poupanca'], 2) ?>€</h2>
            </div>
            <div class="col-md-8 px-4">
                <div class="d-flex justify-content-between mb-1 small fw-bold">
                    <span>Progresso da Meta (20.000€)</span>
                    <span><?= round($percentagem, 1) ?>%</span>
                </div>
                <div class="progress" style="height: 12px;">
                    <div class="progress-bar" style="width: <?= $percentagem ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            
            <?php if($is_admin): ?>
            <div class="card p-4 mb-4 bg-white shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold m-0 text-uppercase small">Lista Mestra (Fixos)</h6>
                    <form method="POST">
                        <button name="lancar_fixos" class="btn btn-xs btn-outline-dark fw-bold py-0" style="font-size:0.7rem">SINCRONIZAR</button>
                    </form>
                </div>
                
                <div class="list-group list-group-flush mb-3" style="max-height: 200px; overflow-y: auto;">
                    <?php 
                    $recs = $conn->query("SELECT * FROM recorrencias ORDER BY descricao ASC"); 
                    while($r = $recs->fetch_assoc()): 
                    ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-bottom-0 py-1">
                            <span class="small fw-semibold"><?= htmlspecialchars($r['descricao']) ?> (<?= number_format($r['valor'], 2) ?>€)</span>
                            <a href="?del_rec=<?= $r['id'] ?>&mes_filtro=<?= $mes_filtro ?>" class="text-danger fw-bold text-decoration-none small" style="font-size:0.7rem">Remover</a>
                        </div>
                    <?php endwhile; ?>
                </div>

                <form method="POST" class="input-group input-group-sm">
                    <input type="text" name="desc_rec" class="form-control" placeholder="Descrição" required>
                    <input type="number" step="0.01" name="val_rec" class="form-control" placeholder="€" required>
                    <button name="add_recorrencia" class="btn btn-dark">+</button>
                </form>
            </div>
            <?php endif; ?>

            <div class="card p-4 bg-white shadow-sm border-0">
                <h6 class="fw-bold mb-3 text-uppercase small">Novo Movimento: <?= $mes_filtro ?></h6>
                <form method="POST">
                    <select name="categoria" class="form-select form-select-sm mb-2">
                        <option value="Ordenado">Receita: Ordenado</option>
                        <option value="Alimentação">Despesa: Alimentação</option>
                        <option value="Casa">Despesa: Casa</option>
                        <option value="Lazer">Despesa: Lazer</option>
                    </select>
                    <input type="text" name="descricao" class="form-control form-control-sm mb-2" placeholder="Descrição">
                    <input type="number" step="0.01" name="valor" class="form-control form-control-sm mb-3" placeholder="Valor €" required>
                    <button name="guardar" class="btn btn-primary btn-sm w-100 fw-bold">ADICIONAR</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card p-4 bg-white shadow-sm border-0 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold m-0 text-uppercase small text-muted">Movimentações Mensais</h6>
                    <form method="GET">
                        <select name="mes_filtro" onchange="this.form.submit()" class="form-select form-select-sm fw-bold border-0 bg-light">
                            <?php foreach($todos_meses as $m): ?>
                                <option value="<?= $m ?>" <?= ($m == $mes_filtro) ? 'selected' : '' ?>><?= $m ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle text-center small">
                        <thead>
                            <tr class="text-muted small">
                                <th>DESCRIÇÃO</th><th>VALOR</th><th style="width:120px;">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $res = $conn->query("SELECT * FROM despesas WHERE mes = '$mes_filtro' ORDER BY id DESC");
                            while($row = $res->fetch_assoc()): 
                                $is_fixo = ($row['eh_fixo'] == 1);
                                $classe = ($row['categoria'] == 'Ordenado') ? 'table-success opacity-75' : '';
                            ?>
                            <tr class="<?= $classe ?>" id="row-<?= $row['id'] ?>">
                                <td class="text-start">
                                    <?= htmlspecialchars($row['descricao']) ?>
                                    <?= $is_fixo ? '<span class="badge-fixo">Fixo</span>' : '' ?>
                                </td>
                                <td class="fw-bold"><?= number_format($row['valor'], 2) ?>€</td>
                                <td>
                                    <?php if($is_admin): ?>
                                        <button onclick="ativarEdicao(<?= $row['id'] ?>)" class="btn btn-link-dark p-0 me-2 small">Editar</button>
                                        <a href="?apagar=<?= $row['id'] ?>&mes_filtro=<?= $mes_filtro ?>" class="text-danger fw-bold text-decoration-none small" onclick="return confirm('Apagar?')">Eliminar</a>
                                    <?php else: ?> - <?php endif; ?>
                                </td>
                            </tr>
                            
                            <?php if($is_admin): ?>
                            <tr id="edit-form-<?= $row['id'] ?>" style="display:none;" class="bg-light">
                                <form method="POST">
                                    <input type="hidden" name="id_edit" value="<?= $row['id'] ?>">
                                    <td><input type="text" name="desc_edit" class="form-control form-control-sm" value="<?= htmlspecialchars($row['descricao']) ?>"></td>
                                    <td><input type="number" step="0.01" name="val_edit" class="form-control form-control-sm" value="<?= $row['valor'] ?>"></td>
                                    <td><button name="editar_movimento" class="btn btn-sm btn-success py-0">OK</button></td>
                                </form>
                            </tr>
                            <?php endif; ?>

                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <?php if($id_fecho): ?>
                        <div class="alert alert-secondary d-flex justify-content-between align-items-center py-2">
                            <span class="small fw-bold text-uppercase">Contabilização Fechada</span>
                            <?php if($is_admin): ?>
                            <form method="POST">
                                <input type="hidden" name="id_fecho" value="<?= $id_fecho ?>">
                                <button name="reabrir_mes" class="btn btn-sm btn-outline-danger py-0 fw-bold">REABRIR MÊS</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    <?php elseif($saldo_mes > 0 && $is_admin): ?>
                        <form method="POST">
                            <input type="hidden" name="mes_fechar" value="<?= $mes_filtro ?>">
                            <input type="hidden" name="valor_sobra" value="<?= $saldo_mes ?>">
                            <button name="fechar_mes" class="btn btn-outline-success w-100 fw-bold btn-sm">FECHAR MÊS (+<?= number_format($saldo_mes, 2) ?>€)</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function ativarEdicao(id) {
    document.getElementById('row-' + id).style.display = 'none';
    document.getElementById('edit-form-' + id).style.display = 'table-row';
}
</script>
</body>
</html>