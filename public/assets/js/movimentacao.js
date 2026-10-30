// Localiza os campos de tipo e categoria.
    const tipoSelect = document.getElementById('tipo');
    const categoriaSelect = document.getElementById('categoria');

    // Guarda uma cópia de todas as opções originais.
    const todasCategorias = Array.from(categoriaSelect.options);

    // Atualiza as categorias conforme o tipo selecionado.
    function filtrarCategorias() {
        const tipoSelecionado = tipoSelect.value;

        // Seleciona apenas as categorias do tipo correspondente.
        const categoriasFiltradas = todasCategorias.filter(function(opcao) {
            return opcao.dataset.tipo === tipoSelecionado;
        });

        // Atualiza as opções disponíveis no campo.
        categoriaSelect.replaceChildren(...categoriasFiltradas);

        // Seleciona a primeira categoria da lista filtrada.
        categoriaSelect.selectedIndex = 0;
    }

    // Executa o filtro sempre que o usuário alterar o tipo.
    tipoSelect.addEventListener('change', filtrarCategorias);

    // Executa o filtro quando a página é carregada.
    filtrarCategorias();