<?php
session_start();  // Início da sessão para acessar o nome do usuário logado
include('../php/conexao.php');  // Conexão com o banco de dados
date_default_timezone_set('America/Sao_Paulo');
$dataAtual = date('Y-m-d');

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_dde']) && isset($_POST['assinante_nome'])) {
    $id_dde = intval($_POST['id_dde']);
    $assinante_nome = mysqli_real_escape_string($conn, $_POST['assinante_nome']);

    // Evita assinaturas duplicadas por nome no mesmo DDE
    $check_sql = "SELECT * FROM TAB_DDE_ASSINATURA WHERE ID_DDE = $id_dde AND ASSINANTE_NOME = '$assinante_nome'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) == 0) {
        $sql_insert = "INSERT INTO TAB_DDE_ASSINATURA (ID_DDE, ASSINANTE_NOME) VALUES ($id_dde, '$assinante_nome')";
        mysqli_query($conn, $sql_insert);
    }
}

// Buscar o DDE do dia
$sql = "SELECT * FROM TAB_DDE WHERE DDE_DATA = '$dataAtual'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>DDE de Hoje</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            margin: 30px;
            font-family: Arial, sans-serif;
            color: #00aaff;
            background-color: #111;
            padding: 20px;
            border-radius: 10px;
        }
        h2 {
            margin-bottom: 20px;
        }
        .dde-item {
            border: 1px solid #00aaff;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 10px;
            background: #222;
        }
        .dde-item h3 {
            margin-top: 0;
        }
        form {
            margin-top: 20px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #00aaff;
            background-color: #000;
            color: #fff;
            font-weight: bold;
        }
        button {
            padding: 10px 15px;
            background-color: #00aaff;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .assinaturas {
            margin-top: 20px;
            padding: 10px;
            background-color: #333;
            border-radius: 5px;
        }
        .assinaturas table {
            width: 100%;
            border-collapse: collapse;
            color: #ccc;
        }
        .assinaturas th, .assinaturas td {
            border: 1px solid #00aaff;
            padding: 8px;
            text-align: left;
        }
        .assinaturas th {
            background-color: #222;
        }
        a.voltar-link {
            text-decoration: none;
            font-size: 20px;
            color: #00aaff;
            margin-bottom: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="javascript:history.back()" class="voltar-link">⬅ Voltar</a>
    <h2>DDE de Hoje (<?php echo date('d/m/Y'); ?>)</h2>

    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($dde = mysqli_fetch_assoc($result)) {
            $id_dde = $dde['ID_DDE'];

            echo "<div class='dde-item'>";
            echo "<h3><strong>" . htmlspecialchars($dde['DDE_TITULO']) . "</strong></h3>";
            echo "<p><strong>Tema:</strong> " . htmlspecialchars($dde['DDE_TEMA']) . "</p>";
            echo "<p><strong>Desenvolvimento:</strong><br>" . nl2br(htmlspecialchars($dde['DDE_DESENVOLVIMENTO'])) . "</p>";
            echo "<p><strong>Responsável:</strong> " . htmlspecialchars($dde['DDE_RESPONSAVEL']) . "</p>";

            // Formulário de assinatura
            $usuario_logado = isset($_SESSION['nome']) ? $_SESSION['nome'] : ''; // Pega o nome do usuário logado
            echo "<form method='POST'>";
            echo "<input type='hidden' name='id_dde' value='" . $id_dde . "'>";
            echo "<label for='assinante_nome'><strong>Seu nome:</strong></label><br>";
            echo "<input type='text' name='assinante_nome' value='" . htmlspecialchars($usuario_logado) . "' required>";
            echo "<button type='submit'>Assinar</button>";
            echo "</form>";

            // Buscar assinaturas
            $sql_assinaturas = "SELECT ASSINANTE_NOME, DATA_ASSINATURA FROM TAB_DDE_ASSINATURA WHERE ID_DDE = $id_dde ORDER BY DATA_ASSINATURA ASC";
            $res_assinaturas = mysqli_query($conn, $sql_assinaturas);

            if (mysqli_num_rows($res_assinaturas) > 0) {
                echo "<div class='assinaturas'>";
                echo "<strong>Assinaturas:</strong>";
                echo "<table>";
                echo "<tr><th>Nome</th><th>Data/Hora</th></tr>";
                while ($linha = mysqli_fetch_assoc($res_assinaturas)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($linha['ASSINANTE_NOME']) . "</td>";
                    echo "<td>" . date("d/m/Y H:i", strtotime($linha['DATA_ASSINATURA'])) . "</td>";
                    echo "</tr>";
                }
                echo "</table></div>";
            }

            echo "</div>"; // fim dde-item
        }
    } else {
        echo "<p>Nenhum DDE cadastrado para hoje.</p>";
    }
    ?>
</div>

</body>
</html>
