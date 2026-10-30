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
    $tipo = $_POST['tipo'] ?? '';
    $valor = $_POST['valor'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $data = $_POST['data'] ?? '';
    $categoria = $_POST['categoria'] ?? '';

    $erros = [];


    if (!($tipo === 'receita' || $tipo === 'despesa')){
        $erros[] = "Tipo inválido";
    }

    if(!(is_string($valor) &&
    is_numeric($valor) && $valor>0 &&
    preg_match('/^[0-9]{1,8}(\.[0-9]{1,2})?$/D', $valor) === 1)){
        $erros[] = "Valor inválido. Informe um número positivo com até duas casas decimais.";
    }

    if(!(is_string($descricao) && trim($descricao) !== "" && mb_strlen($descricao, 'UTF-8') <= 200)){
        $erros[] = "A descrição é obrigatória e deve conter no máximo 200 caracteres.";
    }

    if(is_string($data)){
        $partes = explode("-", $data);
        if(!(count($partes) === 3 &&
        ctype_digit($partes[0]) && strlen($partes[0]) === 4 && (int)$partes[0]>= 1900 && (int)$partes[0] <= 2100 &&
        ctype_digit($partes[1]) && strlen($partes[1]) === 2 &&
        ctype_digit($partes[2]) && strlen($partes[2])=== 2 &&
        checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0]))){
            $erros[] = "Data inválida";
        }
    }else{
        $erros[] = "Data inválida";
    }

    // Verifica se a categoria informada existe no banco de dados.
    $categoriaValida = false;

    if(is_string($categoria) && ctype_digit($categoria) && (int) $categoria > 0){
        foreach($categorias as $item){
            if((int)$item['id'] === (int)$categoria && $item['tipo'] === $tipo){
                $categoriaValida = true;
            }
        }
    }

    if($categoriaValida === false){
        $erros[] = "Categoria inválida ou incompatível com o tipo selecionado";
    }

    if(!empty($erros)){
        foreach($erros as $erro){
            echo "- " . $erro . "<br>";
        }
    }else{
        echo "Não existem erros";
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
    <input type="text" id="descricao" name="descricao" maxlength="200" required>

    <label for="data_lancamento">Data do lançamento: </label>
    <input type="date" id="data_lancamento" name="data" min="1900-01-01" max="2100-12-31" required>

    <label for="categoria">Defina a categoria: </label>
    <select id="categoria" name="categoria" required>
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
