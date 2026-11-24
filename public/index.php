<?php
require_once __DIR__ . '/../src/Database/Connection.php';

// Cria a conexão com o banco.
$connection = Connection::getConnection();

$dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
$dataFim = $_GET['data_fim'] ?? date('Y-m-t');

if(!(is_string($dataInicio))){
    $dataInicio = date('Y-m-01');
}

if(!(is_string($dataFim))){
    $dataFim = date("Y-m-t");
}

$inicioObjeto = DateTimeImmutable::createFromFormat('!Y-m-d', $dataInicio);
$fimObjeto = DateTimeImmutable::createFromFormat('!Y-m-d', $dataFim);

$inicioValido = $inicioObjeto !== false && $inicioObjeto->format('Y-m-d') === $dataInicio;
$fimValido = $fimObjeto !== false && $fimObjeto->format('Y-m-d') === $dataFim;

if(!$inicioValido || !$fimValido){
    $dataInicio = date('Y-m-01');
    $dataFim = date('Y-m-t');
}

if($dataInicio > $dataFim){
    $dataInicio = date('Y-m-01');
    $dataFim = date('Y-m-t');
}

$inicioFormatado = (new DateTimeImmutable($dataInicio))->format('d/m/Y');
$fimFormatado = (new DateTimeImmutable($dataFim))->format('d/m/Y');
$data = new DateTimeImmutable($dataInicio);
$primeiroDia = $data->format('Y-m-01');
$ultimoDia = $data->format('Y-m-t');
$mesCompleto = ($dataInicio === $primeiroDia) && ($dataFim === $ultimoDia);
$meses = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
];
$nomeMes = $meses[(int)$data->format('n')];
if(!$mesCompleto){
    $tituloPeriodo = $inicioFormatado . " a " . $fimFormatado;
}else{
    $tituloPeriodo = $nomeMes . " de " . $data->format('Y');
}

$mes = $_GET['mes'] ?? date('n');
$ano = $_GET['ano'] ?? date('Y');

if(!(is_string($mes) && ctype_digit($mes) && (int)$mes >= 1 && (int)$mes <= 12)){
    $mes = date('n');
}

if(!(is_string($ano) && ctype_digit($ano) && strlen($ano) === 4 && (int)$ano >= 1900 && (int)$ano <= 2100)){
    $ano = date('Y');
}


/*
 * Consulta o resumo financeiro de agosto de 2026.
 *
 * O WHERE limita as movimentações ao mês desejado.
 * O CASE separa receitas e despesas.
 * O SUM calcula os totais.
 */
$sql = "
    SELECT
        SUM(
            CASE
                WHEN categoria.tipo = 'receita'
                THEN movimentacao.valor
                ELSE 0
            END
        ) AS tot_receitas,

        SUM(
            CASE
                WHEN categoria.tipo = 'despesa'
                THEN movimentacao.valor
                ELSE 0
            END
        ) AS tot_despesas

    FROM movimentacao

    JOIN categoria
        ON categoria.id = movimentacao.id_categoria

    WHERE movimentacao.data
        BETWEEN :dataInicio AND :dataFim
";

// Executa a consulta.
$stmt = $connection->prepare($sql);

$stmt->execute([
    ':dataInicio' => $dataInicio,
    ':dataFim' => $dataFim

]);

// Como nossa consulta retorna apenas uma linha,
// fetch() é suficiente.
$resumo = $stmt->fetch();

// Guarda os resultados em variáveis PHP.
$totalReceitas = $resumo['tot_receitas'];
$totalDespesas = $resumo['tot_despesas'];

// Calcula o saldo.
$saldo = $totalReceitas - $totalDespesas;

/*
 * Consulta as movimentações de agosto de 2026.
 *
 * Estamos reaproveitando uma consulta SQL que
 * já testamos diretamente no MySQL.
 */
$sqlMovimentacoes = "
    SELECT
        categoria.nome AS categoria_nome,
        movimentacao.valor,
        movimentacao.data,
        movimentacao.descricao,
        categoria.tipo
    FROM movimentacao
    JOIN categoria
        ON categoria.id = movimentacao.id_categoria
    WHERE movimentacao.data
        BETWEEN :dataInicio AND :dataFim
    ORDER BY movimentacao.data ASC
";

$stmtMovimentacoes = $connection->prepare($sqlMovimentacoes);

$stmtMovimentacoes->execute([
    ':dataInicio' => $dataInicio,
    ':dataFim' => $dataFim
]);

$movimentacoes = $stmtMovimentacoes->fetchAll();

$sqlCategorias = "
    SELECT 
        categoria.nome, 
        SUM(movimentacao.valor) as total
    FROM categoria 
    JOIN movimentacao 
        ON categoria.id = movimentacao.id_categoria
    WHERE categoria.tipo = 'despesa' AND movimentacao.data 
        BETWEEN :dataInicio AND :dataFim 
    GROUP BY categoria.nome
    ";

$stmtCategorias = $connection->prepare($sqlCategorias);
$stmtCategorias->execute([
    ':dataInicio' => $dataInicio,
    ':dataFim' => $dataFim
]);

$categorias = $stmtCategorias->fetchAll();
?>





<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <!-- Faz a página se adaptar melhor a diferentes tamanhos de tela -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciador Financeiro</title>

    <!-- Importa nosso arquivo CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!--
        HEADER:
        Parte superior da aplicação.
        Aqui colocamos o nome do sistema e ações principais.
    -->
    <header class="header">
        <div class="container header-content">

            <div>
                <h1>Gerenciador Financeiro</h1>
                <p class="subtitle">Controle financeiro pessoal</p>
            </div>

            <!--
                Por enquanto esse botão não faz nada.
                Mais tarde ele abrirá a tela/formulário
                para cadastrar uma nova movimentação.
            -->
            <!-- Acessa o formulário de cadastro de movimentações -->
            <a href="/movimentacao/create.php" class="btn-primary">
                + Nova movimentação
            </a>

        </div>
    </header>


    <main class="container">

        <!--
            SELETOR DO PERÍODO:
            Estamos simulando o mês que o usuário está visualizando.
            Mais tarde isso poderá ser alterado pelo usuário.
        -->
        
    <!-- Período exibido no dashboard -->
    <section class="period-section">
        <div>
            <p class="section-label">Período</p>
            <h2><?= htmlspecialchars($tituloPeriodo, ENT_QUOTES, 'UTF-8') ?></h2>
        </div>

        <button
            type="button"
            id="toggle-periodo"
            class="btn-secondary"
            aria-expanded="false"
            aria-controls="painel-periodo"
        >
            Alterar período
        </button>
    </section>

    <!-- Painel de filtros, inicialmente oculto -->
    <section id="painel-periodo" class="period-filter" hidden>
        <form action="/" method="GET" id="form-periodo">

            <h3>Selecionar período</h3>
            <p class="filter-description">
                Escolha um atalho ou defina um intervalo personalizado.
            </p>

            <!-- Atalhos de período -->
            <div class="period-shortcuts">
                <button type="button" class="period-shortcut" data-atalho="mes-atual">
                    Este mês
                </button>

                <button type="button" class="period-shortcut" data-atalho="mes-anterior">
                    Mês anterior
                </button>

                <button type="button" class="period-shortcut" data-atalho="ano-atual">
                    Este ano
                </button>

                <button type="button" class="period-shortcut" data-atalho="personalizado">
                    Personalizado
                </button>
            </div>

            <!-- Seleção manual de datas -->
            <div class="period-dates">
                <div class="period-field">
                    <label for="data_inicio">Data inicial</label>
                    <input
                        type="date"
                        id="data_inicio"
                        name="data_inicio"
                        min="1900-01-01"
                        max="2100-12-31"
                        required
                    >
                </div>

                <div class="period-field">
                    <label for="data_fim">Data final</label>
                    <input
                        type="date"
                        id="data_fim"
                        name="data_fim"
                        min="1900-01-01"
                        max="2100-12-31"
                        required
                    >
                </div>
            </div>

            <p id="erro-periodo" class="period-error" role="alert" hidden>
                A data final não pode ser anterior à inicial.
            </p>

            <div class="period-actions">
                <button type="submit" class="btn-primary">
                    Aplicar período
                </button>
            </div>

        </form>
    </section>



        <!--
            CARDS DO DASHBOARD:
            Esses são os três indicadores principais definidos
            durante nossa etapa de requisitos.
        -->
        <section class="summary-grid">

            <!-- Total recebido -->
            <article class="summary-card">
                <p class="card-label">Valor depositado total</p>
                <h3 class="card-value">
                    R$ <?= number_format($totalReceitas, 2, ',', '.') ?>
                </h3>
                <span class="card-description">
                    Total de receitas no mês
                </span>
            </article>


            <!-- Total gasto -->
            <article class="summary-card">
                <p class="card-label">Valor gasto no mês</p>
                <h3 class="card-value">R$ <?= number_format($totalDespesas, 2, ',', '.') ?></h3>
                <span class="card-description">
                    Total de despesas no mês
                </span>
            </article>


            <!-- Saldo -->
            <article class="summary-card">
                <p class="card-label">Valor restante</p>
                <h3 class="card-value positive">R$ <?= number_format($saldo, 2, ',', '.') ?></h3>
                <span class="card-description">
                    Receitas menos despesas
                </span>
            </article>

        </section>


        <!--
            CORPO DO DASHBOARD:
            Aqui dividimos a área em duas partes.
            Por enquanto, os dados ainda são estáticos.
        -->
        <section class="dashboard-grid">

            <!--
                ÁREA DE GASTOS POR CATEGORIA
                Futuramente teremos um gráfico real aqui.
            -->
            <article class="panel">

                <div class="panel-header">
                    <div>
                        <p class="section-label">Despesas</p>
                        <h2>Gastos por categoria</h2>
                    </div>
                </div>

                <div class="category-list">

                    <?php foreach ($categorias as $categoria): ?>

                        <div class="category-item">

                            <div>
                                <strong>
                                    <?= htmlspecialchars($categoria['nome']) ?>
                                </strong>

                                <span>
                                    Despesa
                                </span>
                            </div>

                            <strong>
                                R$ <?= number_format($categoria['total'], 2, ',', '.') ?>
                            </strong>

                        </div>

                    <?php endforeach; ?>

                </div>

            </article>


            <!--
                ÁREA DE MOVIMENTAÇÕES RECENTES
            -->
            <article class="panel">

                <div class="panel-header">
                    <div>
                        <p class="section-label">Lançamentos</p>
                        <h2>Movimentações recentes</h2>
                    </div>
                </div>


                <div class="transaction-list">

                    <?php foreach ($movimentacoes as $movimentacao): ?>

                        <?php
                        /*
                        * Define se a movimentação será exibida
                        * como receita ou despesa.
                        */
                        $classeTipo = $movimentacao['tipo'] === 'receita'
                            ? 'transaction-income'
                            : 'transaction-expense';

                        /*
                        * Define o sinal exibido no valor.
                        */
                        $sinal = $movimentacao['tipo'] === 'receita'
                            ? '+'
                            : '-';
                        ?>

                        <div class="transaction-item">

                            <div>

                                <strong>
                                    <?= htmlspecialchars($movimentacao['descricao']) ?>
                                </strong>

                                <span>
                                    <?= date('d/m/Y', strtotime($movimentacao['data'])) ?>
                                    ·
                                    <?= htmlspecialchars($movimentacao['categoria_nome']) ?>
                                </span>

                            </div>

                            <strong class="<?= $classeTipo ?>">
                                <?= $sinal ?>
                                R$ <?= number_format($movimentacao['valor'], 2, ',', '.') ?>
                            </strong>

                        </div>

                    <?php endforeach; ?>

                </div>

            </article>

        </section>

    </main>
    <script src="/assets/js/periodo.js"></script>
</body>

</html>