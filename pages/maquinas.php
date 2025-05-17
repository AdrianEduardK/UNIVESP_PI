<?php
session_start();
include('../php/conexao.php');

// Inserção ou edição
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $desc = $_POST['MAQ_DESC'];
    $tag = $_POST['MAQ_TAG'];
    $ativo = $_POST['MAQ_ATIVO'];

    if (isset($_POST['editar_id'])) {
        $id = $_POST['editar_id'];
        $sql = "UPDATE TAB_MAQ SET MAQ_DESC = '$desc', MAQ_TAG = '$tag', MAQ_ATIVO = '$ativo' WHERE MAQ_ID = $id";
    } else {
        $sql = "INSERT INTO TAB_MAQ (MAQ_DESC, MAQ_TAG, MAQ_ATIVO) VALUES ('$desc', '$tag', '$ativo')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: maquinas.php");
        exit();
    } else {
        echo "Erro: " . $conn->error;
    }
}

// Edição
$maqEdit = null;
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    $result = $conn->query("SELECT * FROM TAB_MAQ WHERE MAQ_ID = $id");
    if ($result->num_rows > 0) {
        $maqEdit = $result->fetch_assoc();
    }
}

// Exclusão
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $conn->query("DELETE FROM TAB_MAQ WHERE MAQ_ID = $id");
    header("Location: maquinas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Máquinas</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        a.botao-branco {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        a.botao-branco:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo $maqEdit ? 'Editar Máquina' : 'Cadastrar Máquina'; ?></h2>
        <form method="POST">
            <label>Descrição:</label>
            <input type="text" name="MAQ_DESC" value="<?php echo $maqEdit['MAQ_DESC'] ?? ''; ?>" required><br>

            <label>TAG:</label>
            <input type="text" name="MAQ_TAG" value="<?php echo $maqEdit['MAQ_TAG'] ?? ''; ?>" required><br>

            <label>Ativo:</label>
            <select name="MAQ_ATIVO">
                <option value="ATIVO" <?php if ($maqEdit && $maqEdit['MAQ_ATIVO'] == 'ATIVO') echo 'selected'; ?>>ATIVO</option>
                <option value="INATIVO" <?php if ($maqEdit && $maqEdit['MAQ_ATIVO'] == 'INATIVO') echo 'selected'; ?>>INATIVO</option>
            </select><br>

            <?php if ($maqEdit): ?>
                <input type="hidden" name="editar_id" value="<?php echo $maqEdit['MAQ_ID']; ?>">
            <?php endif; ?>

            <button type="submit"><?php echo $maqEdit ? 'Atualizar' : 'Cadastrar'; ?></button>
        </form>

        <hr>
        <h3>Máquinas Cadastradas</h3>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>TAG</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
            <?php
            $result = $conn->query("SELECT * FROM TAB_MAQ ORDER BY MAQ_ID DESC");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['MAQ_ID']}</td>
                    <td>{$row['MAQ_DESC']}</td>
                    <td>{$row['MAQ_TAG']}</td>
                    <td>{$row['MAQ_ATIVO']}</td>
                    <td>
                        <a class='botao-branco' href='maquinas.php?editar={$row['MAQ_ID']}'>Editar</a> | 
                        <a class='botao-branco' href='maquinas.php?excluir={$row['MAQ_ID']}' onclick=\"return confirm('Deseja realmente excluir esta máquina?');\">Excluir</a>
                    </td>
                </tr>";
            }
            ?>
        </table>

        <!-- Botão de Voltar -->
        <a href="lobby.php">
            <button type="button" style="margin-top: 20px;">← Voltar</button>
        </a>
    </div>
</body>
</html>

