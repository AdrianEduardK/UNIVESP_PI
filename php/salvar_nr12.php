<?php
session_start();
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coletar dados do formulário
    $op_id = $_POST['op_id'];
    $op_linha = $_POST['op_linha'];
    $data = $_POST['data'];
    $inicio = $_POST['inicio'];
    $fim = $_POST['fim'];
    $operador = $_POST['operador'];
    $obs = $_POST['obs'];
    
    // Verificar se já existe um registro para esta OP
    $check_query = "SELECT ID_TAB_NR12 FROM TAB_NR12 WHERE NR12_OP = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "i", $op_id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
    
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        // Atualizar registro existente
        $query = "UPDATE TAB_NR12 SET 
            NR12_DATA = ?,
            NR12_LINHA = ?,
            NR12_INICIO = ?,
            NR12_FIM = ?,
            NR12_OPERADOR = ?,
            Q1 = ?, Q2 = ?, Q3 = ?, Q4 = ?, Q5 = ?,
            Q6 = ?, Q7 = ?, Q8 = ?, Q9 = ?, Q10 = ?,
            Q11 = ?, Q12 = ?, Q13 = ?, Q14 = ?,
            NR12_OBS = ?
            WHERE NR12_OP = ?";
    } else {
        // Inserir novo registro
        $query = "INSERT INTO TAB_NR12 (
            NR12_DATA, NR12_LINHA, NR12_INICIO, NR12_FIM, NR12_OPERADOR,
            Q1, Q2, Q3, Q4, Q5, Q6, Q7, Q8, Q9, Q10,
            Q11, Q12, Q13, Q14, NR12_OP, NR12_OBS
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    }
    
    $stmt = mysqli_prepare($conn, $query);
    
    // Coletar respostas das perguntas
    $respostas = [];
    for ($i = 1; $i <= 14; $i++) {
        $respostas[] = $_POST["Q$i"];
    }
    
    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        // Parâmetros para UPDATE
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssssssssssssssi",
            $data, $op_linha, $inicio, $fim, $operador,
            $respostas[0], $respostas[1], $respostas[2], $respostas[3], $respostas[4],
            $respostas[5], $respostas[6], $respostas[7], $respostas[8], $respostas[9],
            $respostas[10], $respostas[11], $respostas[12], $respostas[13],
            $obs,
            $op_id
        );
    } else {
        // Parâmetros para INSERT
        mysqli_stmt_bind_param(
            $stmt,
            "sssssssssssssssssssss",
            $data, $op_linha, $inicio, $fim, $operador,
            $respostas[0], $respostas[1], $respostas[2], $respostas[3], $respostas[4],
            $respostas[5], $respostas[6], $respostas[7], $respostas[8], $respostas[9],
            $respostas[10], $respostas[11], $respostas[12], $respostas[13],
            $op_id,
            $obs
        );
    }
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../pages/producao.php?success=1");
    } else {
        header("Location: ../pages/nr12_formulario.php?id=$op_id&error=1");
    }
} else {
    header("Location: ../pages/producao.php");
}