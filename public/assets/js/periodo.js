
// Localiza os elementos do filtro.
const botaoPeriodo = document.getElementById('toggle-periodo');
const painelPeriodo = document.getElementById('painel-periodo');
const formularioPeriodo = document.getElementById('form-periodo');

const dataInicio = document.getElementById('data_inicio');
const dataFim = document.getElementById('data_fim');
const erroPeriodo = document.getElementById('erro-periodo');

const atalhos = document.querySelectorAll('[data-atalho]');

// Converte uma data para o formato YYYY-MM-DD.
function formatarData(data) {
    const ano = data.getFullYear();
    const mes = String(data.getMonth() + 1).padStart(2, '0');
    const dia = String(data.getDate()).padStart(2, '0');

    return `${ano}-${mes}-${dia}`;
}

// Destaca o atalho selecionado.
function destacarAtalho(tipo) {
    atalhos.forEach(function(botao) {
        botao.classList.toggle('active', botao.dataset.atalho === tipo);
    });
}

// Preenche as datas conforme o atalho.
function aplicarAtalho(tipo) {
    const hoje = new Date();
    const ano = hoje.getFullYear();
    const mes = hoje.getMonth();

    let inicio;
    let fim;

    if (tipo === 'mes-atual') {
        inicio = new Date(ano, mes, 1);
        fim = new Date(ano, mes + 1, 0);
    } else if (tipo === 'mes-anterior') {
        inicio = new Date(ano, mes - 1, 1);
        fim = new Date(ano, mes, 0);
    } else if (tipo === 'ano-atual') {
        inicio = new Date(ano, 0, 1);
        fim = new Date(ano, 11, 31);
    } else {
        // Mantém as datas para edição manual.
        destacarAtalho('personalizado');
        dataInicio.focus();
        return;
    }

    dataInicio.value = formatarData(inicio);
    dataFim.value = formatarData(fim);

    destacarAtalho(tipo);
    erroPeriodo.hidden = true;
}

// Abre e fecha o painel.
botaoPeriodo.addEventListener('click', function() {
    painelPeriodo.hidden = !painelPeriodo.hidden;

    botaoPeriodo.setAttribute(
        'aria-expanded',
        String(!painelPeriodo.hidden)
    );
});

// Configura os atalhos.
atalhos.forEach(function(botao) {
    botao.addEventListener('click', function() {
        aplicarAtalho(botao.dataset.atalho);
    });
});

// A edição manual ativa o modo personalizado.
[dataInicio, dataFim].forEach(function(campo) {
    campo.addEventListener('change', function() {
        destacarAtalho('personalizado');
        erroPeriodo.hidden = true;
    });
});

// Verifica se o intervalo está na ordem correta.
formularioPeriodo.addEventListener('submit', function(evento) {
    if (dataInicio.value > dataFim.value) {
        evento.preventDefault();
        erroPeriodo.hidden = false;
    }
});

// Recupera datas presentes na URL, quando disponíveis.
const parametros = new URLSearchParams(window.location.search);
const inicioURL = parametros.get('data_inicio');
const fimURL = parametros.get('data_fim');

if (
    inicioURL && fimURL &&
    /^\d{4}-\d{2}-\d{2}$/.test(inicioURL) &&
    /^\d{4}-\d{2}-\d{2}$/.test(fimURL)
) {
    dataInicio.value = inicioURL;
    dataFim.value = fimURL;

    painelPeriodo.hidden = false;
    botaoPeriodo.setAttribute('aria-expanded', 'true');
    destacarAtalho('personalizado');
} else {
    // Padrão: mês atual.
    aplicarAtalho('mes-atual');
}
