<?php
session_start();
include('../php/conexao.php');
 
// Verificar o segmento do usuário
$segmento = $_SESSION['segmento'];
$isPCP = ($segmento == 'PCP'); // Flag para verificar se é PCP
 
$id_op = $_GET['id'] ?? null;
if (!$id_op) {
    echo "ID da OP não informado!";
    exit;
}
 
// Buscar dados da OP
$sql = "SELECT * FROM tab_op WHERE OP_ID = $id_op";
$result = $conn->query($sql);
$op = $result->fetch_assoc();
 
if (!$op) {
    echo "OP não encontrada!";
    exit;
}
 
// Buscar descrição do produto final
$id_produto_final = $op['OP_ID_PRODUTO'];
$sql_produto = "SELECT PROD_DESC FROM tab_produtos WHERE PROD_ID = $id_produto_final";
$result_produto = $conn->query($sql_produto);
$produto = $result_produto->fetch_assoc();
$descricao_produto = $produto ? $produto['PROD_DESC'] : "Descrição não encontrada";
 
// Buscar estrutura do produto
$sql_estrutura = "SELECT ep.ESTRUTURA_ID, ep.ESTRUTURA_ID_NECESSARIO, p.PROD_DESC
                 FROM TAB_ESTRUTURA_PROD ep
                 INNER JOIN TAB_PRODUTOS p ON ep.ESTRUTURA_ID_NECESSARIO = p.PROD_ID
                 WHERE ep.ESTRUTURA_ID_FINAL = $id_produto_final";
$result_estrutura = $conn->query($sql_estrutura);
$estrutura = [];
if ($result_estrutura) {
    while ($row = $result_estrutura->fetch_assoc()) {
        $estrutura[] = $row;
    }
}
 
// Processar formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isPCP) {
    // Apontar Refugo
    if (isset($_POST['qtd_refugo'], $_POST['tipo_refugo'], $_POST['refugo_unidade'])) {
        $qtd_refugo = $conn->real_escape_string($_POST['qtd_refugo']);
        $tipo_refugo = $conn->real_escape_string($_POST['tipo_refugo']);
        $refugo_unidade = $conn->real_escape_string($_POST['refugo_unidade']);
        $sql_refugo = "INSERT INTO tab_refugo (REFUGO_ID_OP, REFUGO_QTD, REFUGO_TIPO, REFUGO_UNIDADE) VALUES ($id_op, $qtd_refugo, '$tipo_refugo', '$refugo_unidade')";
        if ($conn->query($sql_refugo)) {
            echo "<script>alert('Refugo apontado com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao apontar refugo: " . addslashes($conn->error) . "');</script>";
        }
    }
 
    // Apontar Parada
    if (isset($_POST['inicio'], $_POST['fim'], $_POST['tipo_parada'])) {
        $inicio = $conn->real_escape_string($_POST['inicio']);
        $fim = $conn->real_escape_string($_POST['fim']);
        $tipo_parada = $conn->real_escape_string($_POST['tipo_parada']);
        $sql_parada = "INSERT INTO tab_paradas_produtivas (PARADA_ID_OP, PARADA_INICIO, PARADA_FIM, PARADA_TIPO) VALUES ($id_op, '$inicio', '$fim', '$tipo_parada')";
        if ($conn->query($sql_parada)) {
            echo "<script>alert('Parada apontada com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao apontar parada: " . addslashes($conn->error) . "');</script>";
        }
    }
 
    // Apontar Lote
    if (isset($_POST['lotes']) && is_array($_POST['lotes'])) {
        foreach ($_POST['lotes'] as $estrutura_id => $lote_apontado) {
            if (!empty($lote_apontado)) {
                $sql_lote = "INSERT INTO tab_lote_op_produtos (LOTE_ID_OP, LOTE_ID_PROD_NECESSARIO, LOTE_APONTADO)
                             VALUES ($id_op, $estrutura_id, '$lote_apontado')";
                if (!$conn->query($sql_lote)) {
                    echo "Erro ao apontar lote: " . $conn->error;
                }
            }
        }
        echo "Lotes apontados com sucesso!";
    }
 
    // Atualizar dados da OP
    if (isset($_POST['op_recebedor'], $_POST['op_status'], $_POST['op_hora_inicio'])) {
        $op_recebedor = $conn->real_escape_string($_POST['op_recebedor']);
        $op_status = $conn->real_escape_string($_POST['op_status']);
        $op_hora_inicio = $conn->real_escape_string($_POST['op_hora_inicio']);
       
        // Campos opcionais
        $op_hora_fim = isset($_POST['op_hora_fim']) && !empty($_POST['op_hora_fim'])
                     ? "'".$conn->real_escape_string($_POST['op_hora_fim'])."'"
                     : "NULL";
       
        $op_qtd_real = isset($_POST['op_qtd_real']) && !empty($_POST['op_qtd_real'])
                     ? $conn->real_escape_string($_POST['op_qtd_real'])
                     : "NULL";
       
        $op_diario_bordo = isset($_POST['op_diario_bordo']) && !empty($_POST['op_diario_bordo'])
                         ? "'".$conn->real_escape_string($_POST['op_diario_bordo'])."'"
                         : "NULL";
 
        // Validação para status Finalizado (diário de bordo não é obrigatório)
        if ($op_status === 'Finalizado' && (empty($_POST['op_hora_fim']) || empty($_POST['op_qtd_real']))) {
            echo "<script>alert('Para finalizar a OP, preencha a Hora Fim e Quantidade Real!');</script>";
        } else {
            $sql_atualizar = "UPDATE tab_op SET
                            OP_RECEBEDOR = '$op_recebedor',
                            OP_STATUS = '$op_status',
                            OP_HORA_INICIO = '$op_hora_inicio',
                            OP_HORA_FIM = $op_hora_fim,
                            OP_QTD_REAL = $op_qtd_real,
                            OP_DIARIO_BORDO = $op_diario_bordo
                            WHERE OP_ID = $id_op";
           
            if ($conn->query($sql_atualizar)) {
                echo "<script>alert('Dados da OP atualizados com sucesso!');</script>";
                // Atualiza os dados exibidos
                $sql = "SELECT * FROM tab_op WHERE OP_ID = $id_op";
                $result = $conn->query($sql);
                $op = $result->fetch_assoc();
            } else {
                echo "<script>alert('Erro ao atualizar dados da OP: " . addslashes($conn->error) . "');</script>";
            }
        }
    }
}
 
// Buscar históricos
$sql_historico = "
    SELECT p.PROD_DESC, l.LOTE_APONTADO
    FROM tab_lote_op_produtos l
    LEFT JOIN tab_estrutura_prod e ON l.LOTE_ID_PROD_NECESSARIO = e.ESTRUTURA_ID
    LEFT JOIN tab_produtos p ON e.ESTRUTURA_ID_NECESSARIO = p.PROD_ID
    WHERE l.LOTE_ID_OP = $id_op
";

$result_historico = $conn->query($sql_historico);
$historico_lotes = [];

if ($result_historico) {
    while ($row = $result_historico->fetch_assoc()) {
        $historico_lotes[] = $row;
    }
}
 
$sql_historico_refugo = "SELECT REFUGO_QTD, REFUGO_TIPO, REFUGO_UNIDADE FROM tab_refugo WHERE REFUGO_ID_OP = $id_op";
$result_historico_refugo = $conn->query($sql_historico_refugo);
$historico_refugos = [];
if ($result_historico_refugo) {
    while ($row = $result_historico_refugo->fetch_assoc()) {
        $historico_refugos[] = $row;
    }
}
 
$sql_historico_parada = "SELECT PARADA_INICIO, PARADA_FIM, PARADA_TIPO FROM tab_paradas_produtivas WHERE PARADA_ID_OP = $id_op";
$result_historico_parada = $conn->query($sql_historico_parada);
$historico_paradas = [];
if ($result_historico_parada) {
    while ($row = $result_historico_parada->fetch_assoc()) {
        $historico_paradas[] = $row;
    }
}
?>
 
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes OP <?php echo $op['OP_ID']; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container { width: 80%; margin: 0 auto; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        .info-box { background-color: black; color: white; padding: 10px; font-size: 16px; margin: 5px 0; }
        .descricao { color: #00aaff; font-weight: bold; }
        #op_diario_bordo { width: 100%; height: 150px; background-color:rgb(19, 19, 19); color: white; }
        .obrigatorio { border: 2px solid red; }
        .voltar-link {
            position: absolute;
            top: 15px;
            left: 130px;
            text-decoration: none;
            font-size: 30px;
            color: #00aaff;
            font-weight: bold;
        }
        /* Adicionar estilos para modo visualização */
        .readonly {
            background-color: #f0f0f0;
            color: #666;
            border: 1px solid #ccc;
            pointer-events: none;
        }
        .readonly-textarea {
            background-color: #f0f0f0;
            color: #666;
            border: 1px solid #ccc;
            resize: none;
        }
        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    
    <div class="container">
        <!-- Link para voltar adicionado aqui -->
        <a href="http://localhost/funcionarios_web/pages/producao.php" class="voltar-link">⬅</a>
       
        <h2>Detalhes da OP <?php echo $op['OP_ID']; ?></h2>
       
        <div class="info-box">
            <span class="descricao">Produto:</span> <?php echo htmlspecialchars($descricao_produto); ?>
        </div>
 
        <div class="info-box">
            <span class="descricao">Quantidade Programada:</span> <?php echo $op['OP_QTD_PRODUTO']; ?>
        </div>
 
        <div class="info-box">
            <span class="descricao">Linha:</span> <?php echo $op['OP_LINHA']; ?>
        </div>
 
        <h3>Atualizar Dados da OP</h3>
        <form method="POST" action="detalhes_op.php?id=<?php echo $op['OP_ID']; ?>" id="form_op">
            <label for="op_recebedor">Recebedor:</label>
            <input type="text" name="op_recebedor" id="op_recebedor"
                   value="<?php echo htmlspecialchars($op['OP_RECEBEDOR']); ?>"
                   <?php if($isPCP) echo 'class="readonly" readonly'; else echo 'required'; ?>>
 
            <label for="op_status">Status:</label>
            <select name="op_status" id="op_status" <?php if($isPCP) echo 'class="readonly" disabled'; ?>>
                <option value="Aberto" <?php echo $op['OP_STATUS'] === 'Aberto' ? 'selected' : ''; ?>>Aberto</option>
                <option value="Finalizado" <?php echo $op['OP_STATUS'] === 'Finalizado' ? 'selected' : ''; ?>>Finalizado</option>
            </select>
 
            <label for="op_hora_inicio">Hora Início:</label>
            <input type="datetime-local" name="op_hora_inicio" id="op_hora_inicio"
                   value="<?php echo $op['OP_HORA_INICIO']; ?>"
                   <?php if($isPCP) echo 'class="readonly" readonly'; else echo 'required'; ?>>
 
            <label for="op_hora_fim">Hora Fim:</label>
            <input type="datetime-local" name="op_hora_fim" id="op_hora_fim"
                   value="<?php echo $op['OP_HORA_FIM']; ?>"
                   <?php if($isPCP) echo 'class="readonly" readonly'; ?>>
 
            <label for="op_qtd_real">Quantidade Real:</label>
            <input type="number" name="op_qtd_real" id="op_qtd_real"
                   value="<?php echo $op['OP_QTD_REAL']; ?>"
                   <?php if($isPCP) echo 'class="readonly" readonly'; ?>>
 
            <label for="op_diario_bordo">Diário de Bordo:</label>
            <textarea name="op_diario_bordo" id="op_diario_bordo"
                      <?php if($isPCP) echo 'class="readonly-textarea" readonly'; ?>><?php echo htmlspecialchars($op['OP_DIARIO_BORDO']); ?></textarea>
 
            <?php if(!$isPCP): ?>
                <button type="submit">Atualizar OP</button>
            <?php endif; ?>
        </form>

        
     <!-- Botão para abrir a página de criação de OS -->
        <form action="abrir_ordem_servico.php" method="get">
            <input type="hidden" name="id_op" value="<?php echo $op['OP_ID']; ?>" />
            <button type="submit" class="btn-abertura-os">Abrir Ordem de Serviço</button>
        </form>


        <?php if(!$isPCP): ?>
            <h3>Apontar Refugo</h3>
            <form method="POST" action="detalhes_op.php?id=<?php echo $op['OP_ID']; ?>">
                <input type="number" name="qtd_refugo" placeholder="Quantidade de Refugo" required>
                <input type="text" name="tipo_refugo" placeholder="Tipo do Refugo" required>
                <select name="refugo_unidade" required>
                    <option value="">Selecione a unidade</option>
                    <option value="KG">KG</option>
                    <option value="UN">UN</option>
                    <option value="M2">M2</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                </select>
                <button type="submit">Apontar Refugo</button>
            </form>
 
            <h3>Apontar Parada</h3>
            <form method="POST" action="detalhes_op.php?id=<?php echo $op['OP_ID']; ?>">
                <input type="datetime-local" name="inicio" required>
                <input type="datetime-local" name="fim" required>
                <input type="text" name="tipo_parada" placeholder="Tipo da Parada" required>
                <button type="submit">Apontar Parada</button>
            </form>
 
            <h3>Apontar Lotes</h3>
            <form method="POST" action="detalhes_op.php?id=<?php echo $op['OP_ID']; ?>">
                <?php foreach ($estrutura as $item): ?>
                    <label for="lote_<?php echo $item['ESTRUTURA_ID']; ?>"><?php echo $item['PROD_DESC']; ?>:</label>
                    <input type="number" name="lotes[<?php echo $item['ESTRUTURA_ID']; ?>]" id="lote_<?php echo $item['ESTRUTURA_ID']; ?>" value="">
                <?php endforeach; ?>
                <button type="submit">Apontar Lotes</button>
            </form>
        <?php endif; ?>
 
        <h3>Histórico de Refugos</h3>
        <table>
            <thead>
                <tr>
                    <th>Quantidade</th>
                    <th>Unidade</th>
                    <th>Tipo de Refugo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historico_refugos as $refugo): ?>
                    <tr>
                        <td><?php echo $refugo['REFUGO_QTD']; ?></td>
                        <td><?php echo $refugo['REFUGO_UNIDADE']; ?></td>
                        <td><?php echo $refugo['REFUGO_TIPO']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
 
        <h3>Histórico de Paradas</h3>
        <table>
            <thead>
                <tr>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Tipo de Parada</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historico_paradas as $parada): ?>
                    <tr>
                        <td><?php echo $parada['PARADA_INICIO']; ?></td>
                        <td><?php echo $parada['PARADA_FIM']; ?></td>
                        <td><?php echo $parada['PARADA_TIPO']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
 
        <h3>Histórico de Lotes</h3>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Lote Apontado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historico_lotes as $lote): ?>
                    <tr>
                        <td><?php echo $lote['PROD_DESC']; ?></td>
                        <td><?php echo $lote['LOTE_APONTADO']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
 
    <?php if(!$isPCP): ?>
    <script>
        document.getElementById('op_status').addEventListener('change', function() {
            var status = this.value;
            var camposObrigatorios = [
                'op_recebedor',
                'op_hora_inicio',
                'op_hora_fim',
                'op_qtd_real'
            ];
 
            camposObrigatorios.forEach(function(campoId) {
                var campo = document.getElementById(campoId);
                if (status === 'Finalizado') {
                    campo.setAttribute('required', 'required');
                } else {
                    campo.removeAttribute('required');
                }
            });
        });
 
        window.onload = function() {
            var status = document.getElementById('op_status').value;
            if (status === 'Finalizado') {
                document.getElementById('op_recebedor').setAttribute('required', 'required');
                document.getElementById('op_hora_inicio').setAttribute('required', 'required');
                document.getElementById('op_hora_fim').setAttribute('required', 'required');
                document.getElementById('op_qtd_real').setAttribute('required', 'required');
            }
        };

    </script>
    <?php endif; ?>

</body>

</html>