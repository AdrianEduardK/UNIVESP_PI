<?php
include('../php/conexao.php');
session_start();

// Inserção do novo DDE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $tema = $_POST['tema'];
    $desenvolvimento = $_POST['desenvolvimento'];
    $data = $_POST['data'];
    $responsavel = $_SESSION['nome']; // pegando da sessão

    $sql = "INSERT INTO TAB_DDE (DDE_TITULO, DDE_TEMA, DDE_DESENVOLVIMENTO, DDE_DATA, DDE_RESPONSAVEL)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $titulo, $tema, $desenvolvimento, $data, $responsavel);

    if ($stmt->execute()) {
        echo "<script>alert('DDE cadastrado com sucesso!'); window.location.href='http://localhost/funcionarios_web/pages/lobby.php';</script>";
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
}

// Filtragem de DDEs por data
$filterData = '';
if (isset($_GET['data_filter'])) {
    $filterData = $_GET['data_filter'];
    $filterData = mysqli_real_escape_string($conn, $filterData);
    $query = "SELECT * FROM TAB_DDE WHERE DDE_DATA = '$filterData' ORDER BY DDE_DATA DESC";
} else {
    $query = "SELECT * FROM TAB_DDE ORDER BY DDE_DATA DESC";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar DDE</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: #1a1a1a;
            color: #fff;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .form-container {
            background-color: #222;
            padding: 30px;
            border-radius: 15px;
            width: 80%;
            max-width: 600px;
            margin: auto;
        }
        label, input, textarea, button {
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        input, textarea {
            padding: 10px;
            border-radius: 5px;
            border: none;
        }
        button {
            background-color: #00aaff;
            color: white;
            padding: 10px;
            margin-top: 20px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0099cc;
        }
        .table-container {
            background-color: #222;
            padding: 20px;
            border-radius: 15px;
            margin-top: 30px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #444;
            text-align: left;
        }
        th {
            background-color: #333;
        }
        td {
            background-color: #2a2a2a;
        }
        .filter-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Criar novo DDE</h2>
    <form method="post">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" required>

        <label for="tema">Tema:</label>
        <input type="text" id="tema" name="tema" required>

        <label for="desenvolvimento">Desenvolvimento:</label>
        <textarea id="desenvolvimento" name="desenvolvimento" rows="6" required></textarea>

        <label for="data">Data desejada de exibição:</label>
        <input type="date" id="data" name="data" required>

        <button type="submit">Cadastrar DDE</button>
    </form>

    <button onclick="window.location.href='http://localhost/funcionarios_web/pages/lobby.php';">Voltar</button>
</div>

<!-- Filtragem por data -->
<div class="table-container">
    <h3>Filtrar DDEs por Data</h3>
    <form method="GET" class="filter-form">
        <label for="data_filter">Data:</label>
        <input type="date" id="data_filter" name="data_filter" value="<?php echo $filterData; ?>">
        <button type="submit">Filtrar</button>
    </form>

    <h3>Todos os DDEs Cadastrados</h3>
    <table>
        <tr>
            <th>Título</th>
            <th>Tema</th>
            <th>Desenvolvimento</th>
            <th>Data</th>
            <th>Responsável</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['DDE_TITULO']}</td>
                        <td>{$row['DDE_TEMA']}</td>
                        <td>{$row['DDE_DESENVOLVIMENTO']}</td>
                        <td>{$row['DDE_DATA']}</td>
                        <td>{$row['DDE_RESPONSAVEL']}</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Nenhum DDE encontrado</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
