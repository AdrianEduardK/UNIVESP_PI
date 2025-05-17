<?php
include 'conexao.php';

$hoje = date('Y-m-d');
$sql = "SELECT * FROM TAB_OP WHERE OP_DATA_PROG = '$hoje'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($op = $result->fetch_assoc()) {
        echo "<p><a href='detalhes_op.php?id=" . $op['OP_ID'] . "'>OP: " . $op['OP_ID'] . " - Linha: " . $op['OP_LINHA'] . "</a></p>";
    }
} else {
    echo "Nenhuma OP para hoje.";
}
?>
