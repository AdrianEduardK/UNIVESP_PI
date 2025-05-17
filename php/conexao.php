<?php
$usuario = 'AmandaFragnan';
$senha = 'Belinha@1234';
$banco = 'UNIVESP_PI';
$host = 'localhost';

// Conectar ao MySQL
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
