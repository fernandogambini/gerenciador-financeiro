// Localiza os campos do formulário.
const tipoSelect = document.getElementById('tipo');
const categoriaSelect = document.getElementById('categoria');

// Guarda todas as categorias originais.
const todasCategorias = Array.from(categoriaSelect.options);

// Filtra as categorias conforme o tipo selecionado.
function filtrarCategorias() {
    const tipoSelecionado = tipoSelect.value;

    // Guarda a categoria selecionada anteriormente.
    const categoriaAnterior = categoriaSelect.value;

    // Seleciona apenas as categorias correspondentes ao tipo.
    const categoriasFiltradas = todasCategorias.filter(function(opcao) {
        return opcao.dataset.tipo === tipoSelecionado;
    });

    // Atualiza as opções disponíveis.
    categoriaSelect.replaceChildren(...categoriasFiltradas);

    // Verifica se a categoria anterior ainda está disponível.
    const categoriaExiste = categoriasFiltradas.some(function(opcao) {
        return opcao.value === categoriaAnterior;
    });

    if (categoriaExiste) {
        // Mantém a categoria selecionada anteriormente.
        categoriaSelect.value = categoriaAnterior;
    } else {
        // Caso contrário, seleciona a primeira categoria disponível.
        categoriaSelect.selectedIndex = 0;
    }
}

// Atualiza o filtro quando o usuário altera o tipo.
tipoSelect.addEventListener('change', filtrarCategorias);

// Aplica o filtro ao carregar a página.
filtrarCategorias();
