<?php
session_start();
include('../php/conexao.php');

// Inserção ou Edição
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['linha_desc'])) {
    $desc = $conn->real_escape_string($_POST['linha_desc']);
    $id = $_POST['linha_id'];

    if ($id == '') {
        // Inserir nova linha
        $conn->query("INSERT INTO TAB_LINHAS (LINHA_DESC) VALUES ('$desc')");
    } else {
        // Editar linha existente
        $conn->query("UPDATE TAB_LINHAS SET LINHA_DESC = '$desc' WHERE LINHA_ID = $id");
    }
    header("Location: linhas_producao.php"); // evita repost
    exit;
}

// Exclusão
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $conn->query("DELETE FROM TAB_LINHAS WHERE LINHA_ID = $id");
    header("Location: linhas_producao.php"); // evita repost
    exit;
}

// Buscar linhas
$linhas = $conn->query("SELECT * FROM TAB_LINHAS ORDER BY LINHA_ID DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Linhas de Produção</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Gerenciar Linhas de Produção</h2>
      

        <form method="post" class="formulario">
            <input type="hidden" name="linha_id" id="linha_id">
            <label for="linha_desc">Descrição da Linha:</label>
            <input type="text" name="linha_desc" id="linha_desc" required>
            <button type="submit">Salvar</button>
        </form>

        <h3>Linhas Cadastradas</h3>
        <table class="tabela-linhas">
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
            <?php while ($linha = $linhas->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $linha['LINHA_ID']; ?></td>
                    <td><?php echo $linha['LINHA_DESC']; ?></td>
                    <td>
                        <button type="button" onclick="editarLinha('<?php echo $linha['LINHA_ID']; ?>', '<?php echo addslashes($linha['LINHA_DESC']); ?>')">Editar</button>
                        <a href="?excluir=<?php echo $linha['LINHA_ID']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta linha?')">
                            <button type="button">Excluir</button>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <a href="lobby.php">
            <button type="button" style="margin-top: 20px;">← Voltar</button>
        </a>
    </div>

    <script>
        function editarLinha(id, desc) {
            document.getElementById('linha_id').value = id;
            document.getElementById('linha_desc').value = desc;
        }
    </script>
</body>
</html>
