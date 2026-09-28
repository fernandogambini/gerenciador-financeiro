<?php
require_once __DIR__ . '/../src/Database/Connection.php';

// Cria a conexão com o banco.
$connection = Connection::getConnection();

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
        BETWEEN '2026-08-01' AND '2026-08-31'
";

// Executa a consulta.
$stmt = $connection->query($sql);

// Como nossa consulta retorna apenas uma linha,
// fetch() é suficiente.
$resumo = $stmt->fetch();

// Guarda os resultados em variáveis PHP.
$totalReceitas = $resumo['tot_receitas'];
$totalDespesas = $resumo['tot_despesas'];

// Calcula o saldo.
$saldo = $totalReceitas - $totalDespesas;
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
            <button type="button" class="btn-primary">
                + Nova movimentação
            </button>

        </div>
    </header>


    <main class="container">

        <!--
            SELETOR DO PERÍODO:
            Estamos simulando o mês que o usuário está visualizando.
            Mais tarde isso poderá ser alterado pelo usuário.
        -->
        <section class="period-section">

            <div>
                <p class="section-label">Período</p>
                <h2>Agosto de 2026</h2>
            </div>

            <button type="button" class="btn-secondary">
                Alterar período
            </button>

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

                    <div class="category-item">
                        <div>
                            <strong>Mercado</strong>
                            <span>Alimentação</span>
                        </div>

                        <strong>R$ 350,00</strong>
                    </div>


                    <div class="category-item">
                        <div>
                            <strong>Gasolina</strong>
                            <span>Transporte</span>
                        </div>

                        <strong>R$ 200,00</strong>
                    </div>

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

                    <!-- Receita -->
                    <div class="transaction-item">

                        <div>
                            <strong>Salário de agosto</strong>
                            <span>05/08/2026 · Salário</span>
                        </div>

                        <strong class="transaction-income">
                            + R$ 5.000,00
                        </strong>

                    </div>


                    <!-- Despesa -->
                    <div class="transaction-item">

                        <div>
                            <strong>Compras do mês</strong>
                            <span>07/08/2026 · Mercado</span>
                        </div>

                        <strong class="transaction-expense">
                            - R$ 350,00
                        </strong>

                    </div>


                    <!-- Despesa -->
                    <div class="transaction-item">

                        <div>
                            <strong>Abastecimento</strong>
                            <span>09/08/2026 · Gasolina</span>
                        </div>

                        <strong class="transaction-expense">
                            - R$ 200,00
                        </strong>

                    </div>


                    <!-- Receita -->
                    <div class="transaction-item">

                        <div>
                            <strong>Trabalho freelance</strong>
                            <span>15/08/2026 · Freelance</span>
                        </div>

                        <strong class="transaction-income">
                            + R$ 800,00
                        </strong>

                    </div>

                </div>

            </article>

        </section>

    </main>

</body>

</html>