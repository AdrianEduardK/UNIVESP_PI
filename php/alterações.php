<?php 
session_start(); 
include('../php/conexao.php');

// Verificar o segmento do usuário
$segmento = $_SESSION['segmento'];
$isPCP = ($segmento == 'PCP'); // Flag para verificar se é PCP

$id_op = $_GET['id'] ?? null; 
if (!$id_op) { 
    echo "ID da OP não informado!"; 
    exit; 
} 

// [Restante do código de consultas permanece igual...]
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- [Head permanece igual...] -->
    <style>
        /* Adicionar estilos para modo visualização */
        .readonly {
            background-color: #f0f0f0;
            color: #666;
            border: 1px solid #ccc;
            pointer-events: none;
        }
        .readonly-textarea {
            background-color: #f0f0f0;
            color: #666;
            border: 1px solid #ccc;
            resize: none;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- [Cabeçalho e informações permanecem iguais...] -->

        <h3>Atualizar Dados da OP</h3>
        <form method="POST" action="detalhes_op.php?id=<?php echo $op['OP_ID']; ?>" id="form_op">
            <label for="op_recebedor">Recebedor:</label>
            <input type="text" name="op_recebedor" id="op_recebedor" 
                   value="<?php echo htmlspecialchars($op['OP_RECEBEDOR']); ?>" 
                   <?php if($isPCP) echo 'class="readonly" readonly'; else echo 'required'; ?>>

            <label for="op_status">Status:</label>
            <select name="op_status" id="op_status" <?php if($isPCP) echo 'class="readonly" disabled'; ?>>
                <option value="Aberto" <?php echo $op['OP_STATUS'] === 'Aberto' ? 'selected' : ''; ?>>Aberto</option>
                <option value="Finalizado" <?php echo $op['OP_STATUS'] === 'Finalizado' ? 'selected' : ''; ?>>Finalizado</option>
            </select>

            <!-- [Demais campos do formulário com a mesma lógica...] -->

            <?php if(!$isPCP): ?>
                <button type="submit">Atualizar OP</button>
            <?php endif; ?>
        </form>

        <?php if(!$isPCP): ?>
            <h3>Apontar Refugo</h3>
            <form method="POST" action="detalhes_op.php?id=<?php echo $op['OP_ID']; ?>">
                <!-- Campos do refugo... -->
                <button type="submit">Apontar Refugo</button>
            </form>

            <h3>Apontar Parada</h3>
            <form method="POST" action="detalhes_op.php?id=<?php echo $op['OP_ID']; ?>">
                <!-- Campos da parada... -->
                <button type="submit">Apontar Parada</button>
            </form>

            <h3>Apontar Lotes</h3>
            <form method="POST" action="detalhes_op.php?id=<?php echo $op['OP_ID']; ?>">
                <!-- Campos dos lotes... -->
                <button type="submit">Apontar Lotes</button>
            </form>
        <?php endif; ?>

        <!-- [Históricos permanecem iguais...] -->
    </div>

    <?php if(!$isPCP): ?>
    <script>
        // [Script permanece igual...]
    </script>
    <?php endif; ?>
</body>
</html>