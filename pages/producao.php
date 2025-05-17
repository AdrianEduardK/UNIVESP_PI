<?php
session_start();
include('../php/conexao.php');

// Definir o fuso horário para São Paulo
date_default_timezone_set('America/Sao_Paulo');

// Verificar o segmento do usuário
$segmento = $_SESSION['segmento'];

// Definir filtros selecionados (se existirem)
$dataSelecionada = '';
$linhaSelecionada = isset($_POST['op_linha']) ? $_POST['op_linha'] : '';
$maqSelecionada = isset($_POST['op_tag_maq']) ? $_POST['op_tag_maq'] : '';

// Se o segmento for "PRODUÇÃO", definir a data automaticamente para o dia de hoje e bloquear os filtros
if ($segmento == 'PRODUÇÃO') {
    $dataSelecionada = date('Y-m-d');
    $linhaSelecionada = '';
    $maqSelecionada = '';
} else {
    $dataSelecionada = isset($_POST['op_data_prog']) ? $_POST['op_data_prog'] : '';
}

// Montar a consulta
$sql = "SELECT * FROM tab_op WHERE 1=1";

if ($dataSelecionada) {
    $sql .= " AND OP_DATA_PROG = '$dataSelecionada'";
}

if ($linhaSelecionada && $segmento != 'PRODUÇÃO') {
    $sql .= " AND OP_LINHA = '$linhaSelecionada'";
}

if ($maqSelecionada && $segmento != 'PRODUÇÃO') {
    $sql .= " AND OP_TAG_MAQ = '$maqSelecionada'";
}

$sql .= " ORDER BY OP_DATA_PROG DESC";

$result = mysqli_query($conn, $sql);

// Pegar valores únicos de Linha e Máquina para os filtros
$linhas = mysqli_query($conn, "SELECT DISTINCT OP_LINHA FROM tab_op ORDER BY OP_LINHA ASC");
$maquinas = mysqli_query($conn, "SELECT DISTINCT OP_TAG_MAQ FROM tab_op ORDER BY OP_TAG_MAQ ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Produção - Ordens Programadas</title>
<link rel="stylesheet" href="../css/style.css">
<style>
    .voltar-link {
        position: absolute;
        top: 30px;
        left: 50px;
        text-decoration: none;
        font-size: 30px;
        color: #00aaff;
        font-weight: bold;
        z-index: 1000;
    }
 
    .filtros {
        display: flex;
        font-size: 12px;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 20px;
        margin-bottom: 20px;
        align-items: center;
        justify-content: space-between;
    }
 
    .filtros > div {
        display: flex;
        flex-direction: column;
        min-width: 150px;
    }
 
    table {
        width: 100%;
        margin-top: 20px;
        font-size: 12px;
        border-collapse: collapse;
        background: #111;
        color: #00aaff;
    }
 
    th, td {
        padding: 12px;
        text-align: center;
        border: 1px solid #00aaff;
    }
 
    .filtros input[disabled], .filtros select[disabled] {
        background-color: #ccc;
    }
   
    .container {
        position: relative;
        padding-top: 10px;
    }
</style>
</head>
<body>

<a href="javascript:history.back()" class="voltar-link">⬅</a>

<div class="container">
<h2>Ordens de Produção</h2>

<a href="dde_hoje.php">
    <button type="button">📋 DDE (Diálogo Diário de Excelência)</button>
</a>

<form method="POST" action="">
<div class="filtros">
    <div>
        <label for="op_data_prog">Filtrar por Data:</label>
        <input type="date" id="op_data_prog" name="op_data_prog" value="<?php echo $dataSelecionada; ?>" <?php echo ($segmento == 'PRODUÇÃO') ? 'disabled' : ''; ?>>
    </div>
 
    <div>
        <label for="op_linha">Filtrar por Linha:</label>
        <select id="op_linha" name="op_linha" <?php echo ($segmento == 'PRODUÇÃO') ? 'disabled' : ''; ?>>
            <option value="">Todas</option>
            <?php while ($rowLinha = mysqli_fetch_assoc($linhas)) { ?>
                <option value="<?php echo $rowLinha['OP_LINHA']; ?>" <?php if ($linhaSelecionada == $rowLinha['OP_LINHA']) echo 'selected'; ?>>
                    <?php echo $rowLinha['OP_LINHA']; ?>
                </option>
            <?php } ?>
        </select>
    </div>
 
    <div>
        <label for="op_tag_maq">Filtrar por Máquina:</label>
        <select id="op_tag_maq" name="op_tag_maq" <?php echo ($segmento == 'PRODUÇÃO') ? 'disabled' : ''; ?>>
            <option value="">Todas</option>
            <?php while ($rowMaq = mysqli_fetch_assoc($maquinas)) { ?>
                <option value="<?php echo $rowMaq['OP_TAG_MAQ']; ?>" <?php if ($maqSelecionada == $rowMaq['OP_TAG_MAQ']) echo 'selected'; ?>>
                    <?php echo $rowMaq['OP_TAG_MAQ']; ?>
                </option>
            <?php } ?>
        </select>
    </div>
 
    <div>
        <button type="submit" <?php echo ($segmento == 'PRODUÇÃO') ? 'disabled' : ''; ?>>Filtrar</button>
    </div>
</div>
</form>

<?php
if (mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr>
        <th>ID</th>
        <th>Data Programada</th>
        <th>ID Produto</th>
        <th>Quantidade</th>
        <th>Linha</th>
        <th>Máquina</th>
        <th>Programador</th>
        <th>Início</th>
        <th>Fim</th>";
    if ($segmento == 'PRODUÇÃO' || $segmento == 'PCP') {
        echo "<th>Ações</th>";
    }
    echo "</tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['OP_ID'] . "</td>";
        echo "<td>" . $row['OP_DATA_PROG'] . "</td>";
        echo "<td>" . $row['OP_ID_PRODUTO'] . "</td>";
        echo "<td>" . $row['OP_QTD_PRODUTO'] . "</td>";
        echo "<td>" . $row['OP_LINHA'] . "</td>";
        echo "<td>" . $row['OP_TAG_MAQ'] . "</td>";
        echo "<td>" . $row['OP_PROGRAMADOR'] . "</td>";
        echo "<td>" . ($row['OP_HORA_INICIO'] ? date('d/m/Y H:i', strtotime($row['OP_HORA_INICIO'])) : '-') . "</td>";
        echo "<td>" . ($row['OP_HORA_FIM'] ? date('d/m/Y H:i', strtotime($row['OP_HORA_FIM'])) : '-') . "</td>";
        
        if ($segmento == 'PRODUÇÃO' || $segmento == 'PCP') {
            $op_id = $row['OP_ID'];
            $check_nr12 = mysqli_query($conn, "SELECT 1 FROM TAB_NR12 WHERE NR12_OP = $op_id");
            $nr12_preenchido = (mysqli_num_rows($check_nr12) > 0);
            
            echo "<td>";
            
            if ($segmento == 'PCP') {
                echo "<a href='../php/detalhes_op.php?id=" . $op_id . "'>";
                echo "<button type='button'>Visualizar OP</button>";
                echo "</a>";
            } else {
                if ($op_id > 0) {
                    if (!$nr12_preenchido) {
                        echo "<a href='nr12_formulario.php?id=" . $op_id . "'>";
                        echo "<button type='button' style='background-color: #ff9800;'>Preencher NR12</button>";
                        echo "</a>";
                    } else {
                        echo "<a href='../php/detalhes_op.php?id=" . $op_id . "'>";
                        echo "<button type='button'>Detalhar/Apontar</button>";
                        echo "</a>";
                    }
                } else {
                    echo "<button type='button' style='background-color: #ff0000;' disabled>OP sem ID válido</button>";
                }
            }
            
            echo "</td>";
        }
        
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p style='margin-top:20px;'>Nenhuma OP encontrada.</p>";
}
?>
</div>
</body>
</html>