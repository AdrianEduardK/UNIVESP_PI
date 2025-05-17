<?php
session_start();
include 'conexao.php';
 
$nome = $_POST['nome'];
$senha = $_POST['senha'];
 
$sql = "SELECT * FROM TAB_LOGIN WHERE LOGIN_NOME='$nome' AND LOGIN_SENHA='$senha'";
$result = $conn->query($sql);
 
if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $_SESSION['nome'] = $usuario['LOGIN_NOME'];
    $_SESSION['segmento'] = $usuario['LOGIN_SEGMENTO'];
 
    // Verifica o segmento do usuário
    if ($_SESSION['segmento'] == 'PRODUÇÃO') {
        header('Location: ../pages/producao.php');  // Direciona para a página de PRODUÇÃO
    } else {
        header('Location: ../pages/lobby.php');  // Caso contrário, para o lobby padrão
    }
    exit(); // Importante para parar o script após o redirecionamento
} else {
    echo "Usuário ou senha inválidos!";
}
?>
