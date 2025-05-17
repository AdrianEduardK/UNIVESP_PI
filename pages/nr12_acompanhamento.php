<?php
session_start();
include('../php/conexao.php');
date_default_timezone_set('America/Sao_Paulo');

$segmento = $_SESSION['segmento'];

$dataSelecionada = '';
$linhaSelecionada = isset($_POST['linha']) ? $_POST['linha'] : '';

// Se for PRODUÇÃO, força a data de hoje e bloqueia filtros
if ($segmento == 'PRODUÇÃO') {
    $dataSelecionada = date('Y-m-d');
    $linhaSelecionada = '';
} else {
    $dataSelecionada = isset($_POST['data']) ? $_POST['data'] : '';
}

// Consulta base
$sql = "SELECT * FROM tab_nr12 WHERE 1=1";

if ($dataSelecionada) {
    $sql .= " AND NR12_DATA = '$dataSelecionada'";
}
if ($linhaSelecionada && $segmento != 'PRODUÇÃO') {
    $sql .= " AND NR12_LINHA = '$linhaSelecionada'";
}

$sql .= " ORDER BY NR12_DATA DESC";

$result = mysqli_query($conn, $sql);

// Dados para os filtros
$linhas = mysqli_query($conn, "SELECT DISTINCT NR12_LINHA FROM tab_nr12 ORDER BY NR12_LINHA ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Acompanhamento NR12</title>
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
    margin: 20px 0;
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
<h2>Acompanhamento NR12</h2>

<!-- Filtros -->
<form method="POST" action="">
<div class="filtros">
    <div>
        <label for="data">Filtrar por Data:</label>
        <input type="date" id="data" name="data" value="<?php echo $dataSelecionada; ?>" <?php echo ($segmento == 'PRODUÇÃO') ? 'disabled' : ''; ?>>
    </div>

    <div>
        <label for="linha">Filtrar por Linha:</label>
        <select id="linha" name="linha" <?php echo ($segmento == 'PRODUÇÃO') ? 'disabled' : ''; ?>>
            <option value="">Todas</option>
            <?php while ($rowLinha = mysqli_fetch_assoc($linhas)) { ?>
                <option value="<?php echo $rowLinha['NR12_LINHA']; ?>" <?php if ($linhaSelecionada == $rowLinha['NR12_LINHA']) echo 'selected'; ?>>
                    <?php echo $rowLinha['NR12_LINHA']; ?>
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
        <th>Data</th>
        <th>Linha</th>
        <th>Início</th>
        <th>Fim</th>
        <th>Operador</th>
        <th>OP</th>
        <th>Observação</th>
        <th>Formulário</th>
    </tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['ID_TAB_NR12'] . "</td>";
        echo "<td>" . date('d/m/Y', strtotime($row['NR12_DATA'])) . "</td>";
        echo "<td>" . $row['NR12_LINHA'] . "</td>";
        echo "<td>" . $row['NR12_INICIO'] . "</td>";
        echo "<td>" . $row['NR12_FIM'] . "</td>";
        echo "<td>" . $row['NR12_OPERADOR'] . "</td>";
        echo "<td>" . $row['NR12_OP'] . "</td>";
        echo "<td>" . $row['NR12_OBS'] . "</td>";
        echo "<td>
            <a href='../pages/nr12_visualizar.php?id=" . $row['NR12_OP'] . "'>
                <button type='button'>Ver Formulário</button>
            </a>
        </td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p style='margin-top:20px;'>Nenhum registro encontrado.</p>";
}
?>
</div>
</body>
</html>
