<?php
include 'conexao.php';

$data_prog = $_POST['data_prog'];
$id_produto = $_POST['id_produto'];
$qtd_produto = $_POST['qtd_produto'];
$linha = $_POST['linha'];
$programador = $_POST['programador'];
$tag_maq = $_POST['tag_maq'];

$sql = "INSERT INTO TAB_OP (OP_DATA_PROG, OP_ID_PRODUTO, OP_QTD_PRODUTO, OP_LINHA, OP_PROGRAMADOR, OP_TAG_MAQ)
VALUES ('$data_prog', '$id_produto', '$qtd_produto', '$linha', '$programador', '$tag_maq')";

if ($conn->query($sql) === TRUE) {
    echo "OP cadastrada com sucesso!";
} else {
    echo "Erro ao cadastrar OP: " . $conn->error;
}
?>
