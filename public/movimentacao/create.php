<?php
require_once __DIR__ . '/../../src/Database/Connection.php';

// Cria a conexão com o banco.
$connection = Connection::getConnection();

$sql = "
    SELECT id, nome, tipo
    FROM categoria
    ORDER BY nome";

// Executa a consulta.
$stmt = $connection->query($sql);


// Como nossa consulta retorna várias linhas (uma para cada categoria),
// utilizamos fetchAll() para recuperar todos os resultados.
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $valor = $_POST['valor'];
    $descricao = $_POST['descricao'];
    $data = $_POST['data'];
    $categoria = $_POST['categoria'];

    $erros = [];
    

    if ($tipo === 'receita' || $tipo === 'despesa'){

    }else{
        $erros[] = "Tipo inválido";
    }

    if(is_numeric($valor) && $valor>0){
        
    }else{
        $erros[] = "O valor precisa ser maior que zero";
    }

    if(trim($descricao) !== ("")){
        
    }else{
        $erros[] = "Descrição inválida";
    }
    
    $partes = explode("-", $data);
    if(count($partes) === 3 && 
    ctype_digit($partes[0]) && strlen($partes[0]) === 4 && (int)$partes[0]>= 1900 && (int)$partes[0] <= 2100 &&
    ctype_digit($partes[1]) &&
    ctype_digit($partes[2]) &&
    checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])){
        
    }else{
        $erros[] = "Data inválida";
    }

    if(!empty($erros)){
        foreach($erros as $erro){
            echo "- " . $erro . "<br>";
        }
    }else{
        echo "Não existem erros";
    }

    // Verifica se a categoria informada existe no banco de dados.
    $categoriaValida = false;

    foreach($categorias as $item){
        if($item['id'] === (int)$categoria && $item['tipo'] === $tipo){
            $categoriaValida = true;
        }
    }
    if($categoriaValida === false){
        $erros[] = "Categoria inválida ou imcompatível com o tipo selecionado";
    }
}

?>

<form action="create.php" method="POST">

    <label for="tipo">Tipo da transação: </label>
    <select id="tipo" name="tipo">
        <option value="receita">Receita</option>
        <option value="despesa">Despesa</option>
    </select>

    <label for="valor">Valor: </label>
    <input type="number" id="valor" name="valor" min="0.01" step="0.01" required>

    <label for="descricao">Descrição: </label>
    <input type="text" id="descricao" name="descricao" required>

    <label for="data_lancamento">Data do lançamento: </label>
    <input type="text" id="data_lancamento" name="data" required>

    <label for="categoria">Defina a categoria: </label>
    <select id="categoria" name="categoria">
        <?php foreach($categorias as $categoria): ?>
            <option value="<?= (int) $categoria['id'] ?>"
                data-tipo="<?= htmlspecialchars($categoria['tipo'], ENT_QUOTES, 'UTF-8') ?>"
            >
                <?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Salvar</button>


</form>

<!-- Script responsável pelo filtro dinâmico de categorias -->
<script src="/assets/js/movimentacao.js"></script>
