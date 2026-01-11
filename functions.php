<?php
require_once 'config.php';

function filtrar($dados) {
    global $conn;
    return $conn->real_escape_string(trim($dados));
}

// Inserir movimento com proteção
function db_inserir($mes, $cat, $desc, $val) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO despesas (mes, categoria, descricao, valor) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $mes, $cat, $desc, $val);
    $stmt->execute();
}

// Editar com proteção
function db_editar($id, $desc, $val) {
    global $conn;
    $stmt = $conn->prepare("UPDATE despesas SET descricao = ?, valor = ? WHERE id = ?");
    $stmt->bind_param("sdi", $desc, $val, $id);
    $stmt->execute();
}

function db_apagar($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM despesas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

function db_get_stats($mes) {
    global $conn;
    $stats = ['entradas' => 0, 'gastos' => 0, 'poupanca' => 0];
    
    // Entradas
    $res = $conn->query("SELECT SUM(valor) as total FROM despesas WHERE mes = '$mes' AND categoria = 'Ordenado'");
    $stats['entradas'] = $res->fetch_assoc()['total'] ?? 0;
    
    // Gastos (Tudo o que não é Ordenado nem Poupança)
    $res = $conn->query("SELECT SUM(valor) as total FROM despesas WHERE mes = '$mes' AND categoria NOT IN ('Ordenado', 'Poupança')");
    $stats['gastos'] = $res->fetch_assoc()['total'] ?? 0;
    
    // Poupança Total (Acumulado de todos os meses)
    $res = $conn->query("SELECT SUM(valor) as total FROM despesas WHERE categoria = 'Poupança'");
    $stats['poupanca'] = $res->fetch_assoc()['total'] ?? 0;
    
    return $stats;
}

function db_is_mes_fechado($mes) {
    global $conn;
    $res = $conn->query("SELECT id FROM fechos_mes WHERE mes = '$mes' LIMIT 1");
    if($res->num_rows > 0) return $res->fetch_assoc()['id'];
    return false;
}

function db_fechar_mes($mes, $valor) {
    global $conn;
    // 1. Regista o fecho
    $stmt = $conn->prepare("INSERT INTO fechos_mes (mes, valor_sobra) VALUES (?, ?)");
    $stmt->bind_param("sd", $mes, $valor);
    $stmt->execute();
    
    // 2. Cria o movimento de poupança automático
    $desc_poupanca = "Sobra Final de " . $mes;
    db_inserir($mes, 'Poupança', $desc_poupanca, $valor);
}

function db_reabrir_mes($id_fecho) {
    global $conn;
    // Procurar o nome do mês antes de apagar o fecho
    $res = $conn->query("SELECT mes FROM fechos_mes WHERE id = $id_fecho");
    $dados = $res->fetch_assoc();
    $mes = $dados['mes'];
    
    // 1. Apaga o registo de fecho
    $conn->query("DELETE FROM fechos_mes WHERE id = $id_fecho");
    
    // 2. Apaga o movimento de poupança gerado (para não duplicar saldo)
    $desc_busca = "Sobra Final de " . $mes;
    $conn->query("DELETE FROM despesas WHERE categoria = 'Poupança' AND descricao = '$desc_busca'");
}

function db_inserir_fixo($cat, $desc, $val) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO recorrencias (categoria, descricao, valor) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $cat, $desc, $val);
    $stmt->execute();
}

function db_sincronizar_fixos($mes) {
    global $conn;
    $fixos = $conn->query("SELECT * FROM recorrencias");
    while($f = $fixos->fetch_assoc()) {
        $desc = $f['descricao'];
        $val = $f['valor'];
        $cat = $f['categoria'];
        // Verifica se já existe para não duplicar
        $check = $conn->query("SELECT id FROM despesas WHERE mes='$mes' AND descricao='$desc' AND eh_fixo=1");
        if($check->num_rows == 0) {
            $conn->query("INSERT INTO despesas (mes, categoria, descricao, valor, eh_fixo) VALUES ('$mes', '$cat', '$desc', $val, 1)");
        }
    }
}

function db_apagar_fixo($id) {
    global $conn;
    $conn->query("DELETE FROM recorrencias WHERE id = $id");
}
?>