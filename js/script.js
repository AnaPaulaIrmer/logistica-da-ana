document.addEventListener("DOMContentLoaded", function () {
    const listaProdutos = document.getElementById("lista-produtos");
    const botaoAdicionar = document.getElementById("adicionar-produto");

    if (listaProdutos && botaoAdicionar) {
        botaoAdicionar.addEventListener("click", function () {
            const primeiraSelecao = listaProdutos.querySelector("select");
            const novaSelecao = primeiraSelecao.cloneNode(true);

            novaSelecao.selectedIndex = -1;
            novaSelecao.required = false;
            novaSelecao.classList.add("mt-2");

            listaProdutos.appendChild(novaSelecao);
        });
    }
});