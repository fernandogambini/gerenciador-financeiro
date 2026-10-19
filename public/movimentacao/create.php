<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $valor = $_POST['valor'];
    $descricao = $_POST['descricao'];
    $data = $_POST['data'];
    $categoria = $_POST['categoria'];

    if ($tipo === 'receita' || $tipo === 'despesa'){
        echo "Tipo válido";
    }else{
        echo "tipo inválido";
    }

    if(is_numeric($valor) && $valor>0){
        echo "Valor válido";
    }else{
        echo "Valor inválido";
    }

    if(trim($descricao) !== ("")){
        echo "Descrição válida";
    }else{
        echo "Descrição inválida";
    }

    $partes = explode("-", $data);
    if(count($partes) === 3 && 
    ctype_digit($partes[0]) && strlen($partes[0]) === 4 && 
    ctype_digit($partes[1]) &&
    ctype_digit($partes[2]) &&
    checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])){
        echo "Data válida";
    }else{
        echo "Data inválida";
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
        <option value="salario">Salário</option>
        <option value="freelance">Freelance</option>
        <option value="aluguel">Aluguel</option>
        <option value="condominio">Condomínio</option>
        <option value="energia">Energia</option>
        <option value="agua">Água</option>
        <option value="gas">Gás</option>
        <option value="celular">Celular</option>
        <option value="internet">Internet</option>
        <option value="gasolina">Gasolina</option>
        <option value="alimentacao">Alimentação</option>
        <option value="mercado">Mercado</option>
        <option value="saude">Saúde</option>
        <option value="lazer">Lazer</option>
    </select>

    <button type="submit">Salvar</button>


</form>