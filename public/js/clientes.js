document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modalCliente");
    const dialog = modal?.querySelector(".customer-modal");
    const form = document.getElementById("formCliente");

    if (!modal || !form) return;

    const config = window.clienteModalConfig || {};
    const titulo = document.getElementById("modalClienteTitulo");
    const metodo = document.getElementById("metodoCliente");
    const modo = document.getElementById("modoCliente");
    const clienteId = document.getElementById("clienteId");
    const nome = document.getElementById("clienteNome");
    const telefone = document.getElementById("clienteTelefone");
    const salvar = document.getElementById("salvarCliente");
    const grupoStatus = document.getElementById("grupoStatusCliente");
    const status = document.getElementById("clienteStatus");

    function mascaraTelefone(valor) {
        const numeros = valor.replace(/\D/g, "").slice(0, 11);

        if (!numeros) return "";

        if (numeros.length <= 2) {
            return `(${numeros}`;
        }

        const ddd = numeros.slice(0, 2);
        const numero = numeros.slice(2);

        if (numeros.length <= 10) {
            return `(${ddd}) ${numero.slice(0, 4)}${numero.length > 4 ? "-" + numero.slice(4) : ""}`;
        }

        return `(${ddd}) ${numero.slice(0, 5)}${numero.length > 5 ? "-" + numero.slice(5) : ""}`;
    }

    telefone.addEventListener("input", function () {
        this.value = mascaraTelefone(this.value);
    });

    let elementoAnterior = null;

    function limparErros() {
        form.querySelectorAll(".is-invalid").forEach((input) => {
            input.classList.remove("is-invalid");
        });

        form.querySelectorAll(".field-error").forEach((erro) => {
            erro.remove();
        });
    }

    function abrirModal() {
        elementoAnterior = document.activeElement;
        modal.hidden = false;
        document.body.classList.add("customer-modal-open");
        nome.focus();
    }

    function fecharModal() {
        modal.hidden = true;
        document.body.classList.remove("customer-modal-open");
        elementoAnterior?.focus();
    }

    function configurarNovoCliente() {
        form.reset();
        limparErros();

        form.action = form.dataset.storeUrl;
        metodo.value = "POST";
        modo.value = "novo";
        clienteId.value = "";
        grupoStatus.hidden = true;
        status.disabled = true;
        status.value = "1";

        titulo.textContent = "Novo cliente";
        salvar.innerHTML = '<i class="bi bi-check-lg"></i> Cadastrar cliente';

        abrirModal();
    }

    function configurarEdicao(
        id,
        nomeCliente,
        telefoneCliente,
        statusCliente = "1",
        url = null,
    ) {
        form.reset();
        limparErros();

        form.action = url || config.updateUrlTemplate.replace("__ID__", id);
        metodo.value = "PUT";
        modo.value = "editar";
        clienteId.value = id;

        nome.value = nomeCliente || "";
        telefone.value = mascaraTelefone(telefoneCliente || "");

        grupoStatus.hidden = false;
        status.disabled = false;
        status.value = String(statusCliente);

        titulo.textContent = "Editar cliente";
        salvar.innerHTML = '<i class="bi bi-check-lg"></i> Salvar alterações';

        abrirModal();
    }

    document
        .getElementById("btnNovoCliente")
        ?.addEventListener("click", configurarNovoCliente);

    document
        .getElementById("btnPrimeiroCliente")
        ?.addEventListener("click", configurarNovoCliente);

    document.querySelectorAll(".btn-editar-cliente").forEach((botao) => {
        botao.addEventListener("click", () => {
            configurarEdicao(
                botao.dataset.id,
                botao.dataset.nome,
                botao.dataset.telefone,
                botao.dataset.status,
                botao.dataset.url,
            );
        });
    });
    document
        .getElementById("fecharModalCliente")
        ?.addEventListener("click", fecharModal);

    document
        .getElementById("cancelarModalCliente")
        ?.addEventListener("click", fecharModal);

    modal.addEventListener("click", (event) => {
        if (event.target === modal) fecharModal();
    });

    document.addEventListener("keydown", (event) => {
        if (modal.hidden) return;

        if (event.key === "Escape") {
            fecharModal();
        }

        if (event.key === "Tab") {
            const elementos = [
                ...dialog.querySelectorAll(
                    "button:not([disabled]), input:not([disabled]), a[href]",
                ),
            ].filter((elemento) => elemento.getClientRects().length);

            const primeiro = elementos[0];
            const ultimo = elementos[elementos.length - 1];

            if (!primeiro || !ultimo) return;

            if (event.shiftKey && document.activeElement === primeiro) {
                event.preventDefault();
                ultimo.focus();
            } else if (!event.shiftKey && document.activeElement === ultimo) {
                event.preventDefault();
                primeiro.focus();
            }
        }
    });

    // Reabre o modal caso o Laravel retorne erros de validação.
    if (config.hasErrors && config.oldMode) {
        const erros = [...form.querySelectorAll(".field-error")].map(
            (erro) => ({
                nome: erro
                    .closest(".form-group")
                    ?.querySelector(".form-control")?.name,
                mensagem: erro.textContent,
            }),
        );

        if (config.oldMode === "editar" && config.oldId) {
            configurarEdicao(config.oldId, config.oldNome, config.oldTelefone);
        } else {
            configurarNovoCliente();
            nome.value = config.oldNome || "";
            telefone.value = mascaraTelefone(config.oldTelefone || "");
        }

        erros.forEach((erro) => {
            const campo = form.elements.namedItem(erro.nome);

            if (!(campo instanceof HTMLElement)) return;

            campo.classList.add("is-invalid");

            const mensagem = document.createElement("span");
            mensagem.className = "field-error";
            mensagem.textContent = erro.mensagem;

            campo.after(mensagem);
        });
    }
});
