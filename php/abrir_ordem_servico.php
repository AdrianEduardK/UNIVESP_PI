<?php
session_start();
include('../php/conexao.php');

// Verificar se o usuário está logado
if (!isset($_SESSION['nome'])) {
    echo "Você precisa estar logado para acessar esta página.";
    exit;
}

// Verificar se a OP foi passada
$id_op = $_GET['id_op'] ?? null;
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

// Buscar TAGs de máquinas
$tags_maquinas = $conn->query("SELECT MAQ_TAG FROM TAB_MAQ");

// Buscar linhas
$linhas = $conn->query("SELECT LINHA_DESC FROM TAB_LINHAS");

// Processar formulário de criação de OS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $os_solicitante = $conn->real_escape_string($_POST['os_solicitante']);
    $os_status = $conn->real_escape_string($_POST['os_status']);
    $os_tag_maq = $conn->real_escape_string($_POST['os_tag_maq']);
    $os_linha = $conn->real_escape_string($_POST['os_linha']);
    $os_classificacao = $conn->real_escape_string($_POST['os_classificacao']);
    $os_situacao = $conn->real_escape_string($_POST['os_situacao']);
    $os_gravidade = (int)$_POST['os_gravidade'];
    $os_urgencia = (int)$_POST['os_urgencia'];
    $os_tendencia = (int)$_POST['os_tendencia'];
    $os_soma_gut = $os_gravidade + $os_urgencia + $os_tendencia;
    $os_desc_pedido = $conn->real_escape_string($_POST['os_desc_pedido']);
    $os_data_solicitacao = date('Y-m-d H:i:s');
    $os_id_op = $id_op;

    $sql_os = "INSERT INTO TAB_ORDEM_SERVICO (
                    OS_SOLICITANTE, OS_STATUS, OS_TAG_MAQ, OS_LINHA, OS_CLASSIFICACAO, 
                    OS_SITUACAO, OS_GRAVIDADE, OS_URGENCIA, OS_TENDENCIA, OS_SOMA_GUT, 
                    OS_DESC_PEDIDO, OS_DATA_SOLICITACAO, OS_ID_OP
                ) VALUES (
                    '$os_solicitante', '$os_status', '$os_tag_maq', '$os_linha', '$os_classificacao',
                    '$os_situacao', $os_gravidade, $os_urgencia, $os_tendencia, $os_soma_gut, 
                    '$os_desc_pedido', '$os_data_solicitacao', $os_id_op
                )";

    if ($conn->query($sql_os)) {
        echo "<script>alert('Ordem de serviço criada com sucesso!'); window.location.href = 'detalhes_op.php?id=$id_op';</script>";
    } else {
        echo "<script>alert('Erro ao criar a ordem de serviço: " . addslashes($conn->error) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Abertura de Ordem de Serviço</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-os { width: 60%; margin: 20px auto; }
        .form-os input, .form-os select, .form-os textarea { width: 100%; padding: 8px; margin: 10px 0; }
        .form-os button { background-color:rgb(0, 198, 248); color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .form-os button:hover { background-color:rgb(0, 170, 220); }
        .gut-info { background-color: #f0f0f0; padding: 10px; margin-bottom: 15px; border-left: 5px solid #007BFF; }
    </style>
    <script>
        function calcularSomaGUT() {
            const gravidade = parseInt(document.querySelector('[name="os_gravidade"]').value) || 0;
            const urgencia = parseInt(document.querySelector('[name="os_urgencia"]').value) || 0;
            const tendencia = parseInt(document.querySelector('[name="os_tendencia"]').value) || 0;
            document.querySelector('[name="os_soma_gut"]').value = gravidade + urgencia + tendencia;
        }
    </script>
</head>
<body>
    <div class="form-os">
        <h2>Abrir Ordem de Serviço para OP <?php echo $id_op; ?></h2>

        <div class="gut-info">
            <strong>Obs:</strong><br>
            Cada campo (Gravidade, Urgência e Tendência) deve receber um valor entre <strong>1</strong> (menor grau) e <strong>5</strong> (maior grau).A <strong>Soma GUT</strong> é calculada automaticamente com base nesses três valores.<br>
            
        </div>

        <form method="POST">
            <label for="os_solicitante">Solicitante:</label>
            <input type="text" name="os_solicitante" value="<?php echo $_SESSION['nome']; ?>" readonly required>
            
            <label for="os_status">Status:</label>
            <select name="os_status" required>
                <option value="Aberto">Aberto</option>
            </select>

            <label for="os_tag_maq">Tag Máquina:</label>
            <select name="os_tag_maq" required>
                <?php while ($tag = $tags_maquinas->fetch_assoc()): ?>
                    <option value="<?php echo $tag['MAQ_TAG']; ?>"><?php echo $tag['MAQ_TAG']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="os_linha">Linha:</label>
            <select name="os_linha" required>
                <?php while ($linha = $linhas->fetch_assoc()): ?>
                    <option value="<?php echo $linha['LINHA_DESC']; ?>"><?php echo $linha['LINHA_DESC']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="os_classificacao">Classificação:</label>
            <select name="os_classificacao" required>
                <option value="MECANICA">MECÂNICA</option>
                <option value="ELETRICA">ELÉTRICA</option>
                <option value="PREDIAL">PREDIAL</option>
                <option value="ELETROMECANICA">ELETROMECÂNICA</option>
            </select>

            <label for="os_situacao">Situação:</label>
            <select name="os_situacao" required>
                <option value="Linha parada">Linha parada</option>
                <option value="Linha segue em atuação">Linha segue em atuação</option>
            </select>

            <label for="os_gravidade">Gravidade (1 a 5):</label>
            <input type="number" name="os_gravidade" min="1" max="5" required oninput="calcularSomaGUT()">

            <label for="os_urgencia">Urgência (1 a 5):</label>
            <input type="number" name="os_urgencia" min="1" max="5" required oninput="calcularSomaGUT()">

            <label for="os_tendencia">Tendência (1 a 5):</label>
            <input type="number" name="os_tendencia" min="1" max="5" required oninput="calcularSomaGUT()">

            <label for="os_soma_gut">Soma GUT:</label>
            <input type="number" name="os_soma_gut" readonly required>

            <label for="os_desc_pedido">Descrição do Pedido:</label>
            <textarea name="os_desc_pedido" rows="5" required></textarea>

            <button type="submit">Criar Ordem de Serviço</button>
            <button type="button" onclick="window.location.href='detalhes_op.php?id=<?php echo $id_op; ?>'">
                Voltar para a OP
            </button>
        </form>
    </div>
</body>
</html>
