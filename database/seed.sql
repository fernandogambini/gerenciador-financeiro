INSERT INTO usuario (nome)
VALUES ('Fernando');

INSERT INTO categoria (nome, tipo)
VALUES
('Salário', 'receita'),
('Freelance', 'receita'),
('Aluguel', 'despesa'),
('Condomínio', 'despesa'),
('Energia', 'despesa'),
('Água', 'despesa'),
('Gás', 'despesa'),
('Celular', 'despesa'),
('Internet', 'despesa'),
('Gasolina', 'despesa'),
('Alimentação', 'despesa'),
('Mercado', 'despesa'),
('Saúde', 'despesa'),
('Lazer', 'despesa');

INSERT INTO movimentacao
    (valor, data, descricao, id_usuario, id_categoria)
VALUES
    (
        '5000.00',
        '2026-08-05',
        'Salário de agosto',
        (SELECT id FROM usuario WHERE nome = 'Fernando'),
        (SELECT id
        FROM categoria
        WHERE nome = 'Salário'
        AND tipo = 'receita')
    );

INSERT INTO movimentacao
    (valor, data, descricao, id_usuario, id_categoria)
VALUES
    (
        '350.00',
        '2026-08-07',
        'Compras do mês',
        (SELECT id FROM usuario WHERE nome = 'Fernando'),
        (SELECT id
        FROM categoria
        WHERE nome = 'Mercado'
        AND tipo = 'despesa')
    );

INSERT INTO movimentacao
    (valor, data, descricao, id_usuario, id_categoria)
VALUES
    (
        '200.00',
        '2026-08-09',
        'Abastecimento',
        (SELECT id FROM usuario WHERE nome = 'Fernando'),
        (SELECT id
        FROM categoria
        WHERE nome = 'Gasolina'
        AND tipo = 'despesa')
    );

    INSERT INTO movimentacao
    (valor, data, descricao, id_usuario, id_categoria)
VALUES
    (
        '800.00',
        '2026-08-15',
        'Trabalho freelance',
        (SELECT id FROM usuario WHERE nome = 'Fernando'),
        (SELECT id
        FROM categoria
        WHERE nome = 'Freelance'
        AND tipo = 'receita')
    );

    INSERT INTO movimentacao
    (valor, data, descricao, id_usuario, id_categoria)
VALUES
    (
        '1500.00',
        '2026-09-10',
        'Aluguel de setembro',
        (SELECT id FROM usuario WHERE nome = 'Fernando'),
        (SELECT id
        FROM categoria
        WHERE nome = 'Aluguel'
        AND tipo = 'despesa')
    );