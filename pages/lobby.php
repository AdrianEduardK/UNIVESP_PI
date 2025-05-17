<?php
session_start();
include('../php/conexao.php');

// Consultas para os dados dos gráficos
$sql_op = "SELECT OP_LINHA, SUM(OP_QTD_PRODUTO) AS total_producao FROM tab_op GROUP BY OP_LINHA";
$result_op = mysqli_query($conn, $sql_op);
$linhas = [];
$qtd_produto = [];
while ($row = mysqli_fetch_assoc($result_op)) {
    $linhas[] = $row['OP_LINHA'];
    $qtd_produto[] = $row['total_producao'];
}

$sql_paradas = "SELECT PARADA_TIPO, COUNT(*) AS total_paradas FROM tab_paradas_produtivas GROUP BY PARADA_TIPO";
$result_paradas = mysqli_query($conn, $sql_paradas);
$tipos_parada = [];
$qtd_paradas = [];
while ($row = mysqli_fetch_assoc($result_paradas)) {
    $tipos_parada[] = $row['PARADA_TIPO'];
    $qtd_paradas[] = $row['total_paradas'];
}

$sql_refugo = "SELECT REFUGO_TIPO, SUM(REFUGO_QTD) AS total_refugo FROM tab_refugo GROUP BY REFUGO_TIPO";
$result_refugo = mysqli_query($conn, $sql_refugo);
$tipos_refugo = [];
$qtd_refugo = [];
while ($row = mysqli_fetch_assoc($result_refugo)) {
    $tipos_refugo[] = $row['REFUGO_TIPO'];
    $qtd_refugo[] = $row['total_refugo'];
}

$sql_dde = "SELECT DDE_TITULO, COUNT(*) AS total_dde FROM tab_dde GROUP BY DDE_TITULO";
$result_dde = mysqli_query($conn, $sql_dde);
$de_titulos = [];
$qtd_dde = [];
while ($row = mysqli_fetch_assoc($result_dde)) {
    $de_titulos[] = $row['DDE_TITULO'];
    $qtd_dde[] = $row['total_dde'];
}

// Consulta para o número de funcionários
$sql_funcionarios = "SELECT COUNT(*) AS total_funcionarios FROM tab_login";
$result_funcionarios = mysqli_query($conn, $sql_funcionarios);
$row_funcionarios = mysqli_fetch_assoc($result_funcionarios);
$total_funcionarios = $row_funcionarios['total_funcionarios'];

// Consulta para o número de assinaturas de DDE
$sql_dde_assinaturas = "SELECT COUNT(*) AS total_assinaturas FROM tab_dde_assinatura";
$result_dde_assinaturas = mysqli_query($conn, $sql_dde_assinaturas);
$row_dde_assinaturas = mysqli_fetch_assoc($result_dde_assinaturas);
$total_assinaturas = $row_dde_assinaturas['total_assinaturas'];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Lobby</title>
<link rel="stylesheet" href="../css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #1a1a1a;
        color: #fff;
        margin: 0;
        padding: 0;
    }
    .container {
        margin: 50px auto;
        padding: 30px;
        background-color: #222;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        width: 90%;
        max-width: 1200px;
    }
    h2 {
        color: #00aaff;
        font-size: 28px;
        text-align: center;
        margin-bottom: 20px;
    }
    .botoes-lobby {
        text-align: center;
        margin-top: 30px;
    }
    .botoes-lobby a {
        margin: 10px;
        text-decoration: none;
    }
    .botoes-lobby button {
        background-color: #00aaff;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .botoes-lobby button:hover {
        background-color: #0099cc;
    }
    .grafico-container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 30px;
        margin-top: 30px;
        justify-content: center;
    }
    .grafico {
        width: 48%; /* Garante que todos os gráficos tenham a mesma largura */
        height: 300px;
        background-color: #333;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        display: flex;
        justify-content: center;
        align-items: center;
        #graficoParadas, #graficoDDE {
        width: 50% !important; /* Reduzindo a largura para 40% */
        height: 270px !important; /* Ajustando a altura para 250px */
    }

    }
</style>
</head>
<body>
<div class="container">
    <h2>Bem-vindo, <?php echo $_SESSION['nome']; ?>!</h2>
    <p style="text-align: center;">Escolha uma ação:</p>

    <!-- Botões Lobby -->
<?php if ($_SESSION['segmento'] != 'PRODUÇÃO') { ?>
    <div class="botoes-lobby">
        <a href="../pages/pcp.php">
            <button type="button">Cadastrar OP</button>
        </a>
        <a href="../pages/producao.php">
            <button type="button">Visualizar OP</button>
        </a>
        <a href="../pages/cadastro_produto.php">
            <button type="button">Cadastrar Produto</button>
        </a>
        <a href="../pages/linhas_producao.php">
            <button type="button">Gerenciar Linhas de Produção</button>
        </a>
        <a href="../pages/maquinas.php">
            <button type="button">Gerenciar Máquinas</button>
        </a>
        <a href="../php/cadastro_dde.php">
            <button type="button">Criar DDE</button>
        </a>
        <!-- NOVO BOTÃO -->
        <a href="../pages/nr12_acompanhamento.php">
            <button type="button">Acompanhamento NR12</button>
        </a>
    </div>
<?php } else { ?>
    <div class="botoes-lobby">
        <a href="../pages/producao.php">
            <button type="button">Visualizar OP na Produção</button>
        </a>
    </div>
<?php } ?>


    <!-- Gráficos -->
    <div class="grafico-container">
        <!-- Gráfico Produção por Linha -->
        <div class="grafico">
            <canvas id="graficoProducao"></canvas>
        </div>
        <!-- Gráfico Paradas Produtivas por Tipo -->
        <div class="grafico">
            <canvas id="graficoParadas"></canvas>
        </div>
    </div>
    
    <div class="grafico-container">
        <!-- Gráfico Refugos por Tipo -->
        <div class="grafico">
            <canvas id="graficoRefugos"></canvas>
        </div>
        <!-- Gráfico DDE por Título -->
        <div class="grafico">
            <canvas id="graficoDDE"></canvas>
        </div>
    </div>
</div>

<script>
// Gráfico de Produção por Linha
new Chart(document.getElementById('graficoProducao'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($linhas); ?>,
        datasets: [{
            label: 'Quantidade de Produção',
            data: <?php echo json_encode($qtd_produto); ?>,
            backgroundColor: '#00aaff',
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Gráfico de Paradas Produtivas por Tipo
new Chart(document.getElementById('graficoParadas'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($tipos_parada); ?>,
        datasets: [{
            label: 'Paradas Produtivas',
            data: <?php echo json_encode($qtd_paradas); ?>,
            backgroundColor: ['#ff5733', '#33ff57', '#3357ff', '#f4e842'],
        }]
    },
    options: {
        responsive: true,
    }
});

// Gráfico de Refugos por Tipo
new Chart(document.getElementById('graficoRefugos'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($tipos_refugo); ?>,
        datasets: [{
            label: 'Quantidade de Refugos',
            data: <?php echo json_encode($qtd_refugo); ?>,
            backgroundColor: '#ff5733',
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Gráfico de DDE por Título (comparando com o total de funcionários)
new Chart(document.getElementById('graficoDDE'), {
    type: 'doughnut',
    data: {
        labels: ['Funcionários Ativos', 'Assinaturas DDE'],
        datasets: [{
            label: 'DDE - Assinaturas vs Funcionários',
            data: [
                <?php echo $total_funcionarios; ?>,
                <?php echo $total_assinaturas; ?> // Agora, considerando as assinaturas
            ],
            backgroundColor: ['#ff0000', '#66ccff'],
        }]
    },
    options: {
        responsive: true,
    }
});
</script>

</body>
</html>
