<?php
session_start();
include('../php/conexao.php');

// Verificar se o ID da OP foi passado
if (!isset($_GET['id'])) {
    die("ID da OP não fornecido");
}

$op_id = $_GET['id'];

// Buscar dados da OP no banco de dados
$query = "SELECT OP_ID, OP_LINHA, OP_DATA_PROG FROM tab_op WHERE OP_ID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $op_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("OP não encontrada");
}

$op_data = mysqli_fetch_assoc($result);

// Verificar se já existe um checklist NR12 para esta OP
$check_query = "SELECT * FROM TAB_NR12 WHERE NR12_OP = ?";
$check_stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($check_stmt, "i", $op_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
$nr12_data = mysqli_fetch_assoc($check_result);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Checklist NR12</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>input[type="radio"] {
    appearance: none;
    -webkit-appearance: none;
    background-color: white;
    border: 2px solid #00aaff; /* Borda azul */
    width: 20px; /* Tamanho ajustado para corresponder ao que você mostrou */
    height: 20px; /* Tamanho ajustado para corresponder ao que você mostrou */
    border-radius: 50%; /* Torna o botão redondo */
    cursor: default; /* Como o input está em modo readonly */
    position: relative;
}

input[type="radio"]:checked::before {
    content: '';
    position: absolute;
    top: 4px; /* Ajuste para centralizar o ponto dentro do círculo */
    left: 4px; /* Ajuste para centralizar o ponto dentro do círculo */
    width: 12px; /* Ajuste o tamanho do ponto para ficar proporcional ao círculo */
    height: 12px; /* Ajuste o tamanho do ponto para ficar proporcional ao círculo */
    background-color: #00aaff; /* Cor do ponto quando selecionado */
    border-radius: 50%; /* Deixa o ponto também redondo */
}


        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }.voltar-link {
    position: absolute;
    top: 30px;
    left: 50px;
    text-decoration: none;
    font-size: 30px;
    color: #00aaff;
    font-weight: bold;
    z-index: 1000;
}
        .info-row {
            display: flex;
            margin-bottom: 15px;
        }
        .info-row label {
            margin-right: 20px;
        }
        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .checklist-table th {
            background-color: #00aaff;
            padding: 10px;
            text-align: center;
        }
        .checklist-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .question {
            text-align: left;
            font-weight: normal;
        }
        .options {
            text-align: center;
            white-space: nowrap;
        }
        .options input {
            margin: 0 5px;
        }
        textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body><a href="javascript:history.back()" class="voltar-link">⬅</a>

    <h2>Checklist NR12 - OP <?php echo htmlspecialchars($op_data['OP_ID']); ?> - Linha <?php echo htmlspecialchars($op_data['OP_LINHA']); ?> - <?php echo date('d/m/Y', strtotime($op_data['OP_DATA_PROG'])); ?></h2>
    
    <form method="POST" action="../php/salvar_nr12.php">
        <input type="hidden" name="op_id" value="<?php echo htmlspecialchars($op_data['OP_ID']); ?>">
        <input type="hidden" name="op_linha" value="<?php echo htmlspecialchars($op_data['OP_LINHA']); ?>">
        <input type="hidden" name="data" value="<?php echo htmlspecialchars($op_data['OP_DATA_PROG']); ?>">
        
        <div class="info-row">
            <label>Horário Início: <input type="time" name="inicio" value="<?php echo isset($nr12_data['NR12_INICIO']) ? substr($nr12_data['NR12_INICIO'], 0, 5) : ''; ?>" required readonly></label>
            <label>Horário Fim: <input type="time" name="fim" value="<?php echo isset($nr12_data['NR12_FIM']) ? substr($nr12_data['NR12_FIM'], 0, 5) : ''; ?>" required readonly></label>
            <label>Operador: <input type="text" name="operador" value="<?php echo isset($nr12_data['NR12_OPERADOR']) ? htmlspecialchars($nr12_data['NR12_OPERADOR']) : ''; ?>" required readonly></label>
        </div>

        <table class="checklist-table">
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>CONFORME</th>
                    <th>NÃO CONFORME</th>
                    <th>NÃO APLICÁVEL</th>
                </tr>
            </thead>
            <tbody>
    <?php
    $questions = [
        1 => "HÁ PROTEÇÕES NA MÁQUINA DE OPERAÇÃO?",
        2 => "HÁ ALGUMA PARTE MÓVEL QUE ESTÁ EXPOSTA (CORRENTES, CORREIAS, ENGRAVAGENS, ETC)?",
        3 => "EXISTEM PROTEÇÕES FIXAS E/OU MÓVEIS QUANDO O ACESSO A UMA ZONA DE PERIGO FOR REQUERIDO UMA OU MAIS VEZES DURANTE A OPERAÇÃO?",
        4 => "HÁ RISCO DE CHOQUE ELÉTRICO EM VIRTUDE DE CONEXÃO EXPOSTA?",
        5 => "TODOS OS FIOS OU QUAISQUER COMPONENTES ELÉTRICOS POTENCIALMENTE PERIGOSO FORAM DEVIDAMENTE IDENTIFICADOS?",
        6 => "OS COMANDOS DO QUADRO ELÉTRICO ESTÃO SINALIZADOS / IDENTIFICADOS?",
        7 => "HÁ ALGUMA FIAÇÃO SOBRE O PISO POR ONDE OS TRABALHADORES TÊM DE PASSAR?",
        8 => "OS TRABALHADORES ESTÃO TREINADOS A FIM DE GARANTIR A PRONTA DISPONIBILIDADE DA MÁQUINA E IDENTIFICAR PROBLEMAS INTERPONDO SUA ATIVIDADE PARA SOLICITAR A MANUTENÇÃO DA MESMA PARA A ÁREA COMPETENTE?",
        9 => "OS CONTROLES DE PARTIDA, PARADA E A CHAVE GERAL FORAM TESTADOS PARA DESLIGAMENTO TOTAL DO MAQUINÁRIO?",
        10 => "A MÁQUINA POSSUI DISPOSITIVO DE PARADA DE EMERGÊNCIA E OS MESMOS ENCONTRAM-SE AO ALCANCE DAS MÃOS?",
        11 => "OS DISPOSITIVOS DE PARTIDA, ACIONAMENTO E PARADA PODEM SER ACIONADOS OU DESLIGADOS POR OUTRA PESSOA QUE NÃO SEJA O OPERADOR?",
        12 => "VOCÊ ESTÁ TREINANDO A COMO UTILIZAR O MAQUINÁRIO DE FORMA APROPRIADA?",
        13 => "VOCÊ RECEBEU O TREINAMENTO A COMO REAGIR CORRETAMENTE A QUESTÕES DE SEGURANÇA?",
        14 => "OS PROCEDIMENTOS OPERACIONAIS SÃO DOCUMENTADOS NO LOCAL ONDE A MÁQUINA ESTÁ INSTALADA?"
    ];

    foreach ($questions as $num => $question) {
        echo "<tr>";
        echo "<td class='question'>$num. $question</td>";

        $options = ['CONFORME', 'NÃO CONFORME', 'NÃO APLICÁVEL'];
        foreach ($options as $option) {
            $checked = (isset($nr12_data["Q$num"]) && $nr12_data["Q$num"] == $option) ? 'checked' : '';
            echo "<td class='options'><input type='radio' name='Q$num' value='$option' $checked disabled></td>";
        }

        echo "</tr>";
    }
    ?>
</tbody>
</table>

<label>Observações:<br>
    <textarea name="obs" rows="4" cols="50" readonly><?php echo isset($nr12_data['NR12_OBS']) ? htmlspecialchars($nr12_data['NR12_OBS']) : ''; ?></textarea>
</label><br><br>

    </form>
</body>
</html>