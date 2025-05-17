<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Produção</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="tela-login">
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST" action="php/login.php">
            <input type="text" name="nome" placeholder="Nome de usuário" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
