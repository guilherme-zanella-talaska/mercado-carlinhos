/*----------------------------------*/
/*---------- CONTROLE SIDEBAR ------*/
/*----------------------------------*/

document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const sidebar = document.getElementById("sidebar");
    const toggle = document.getElementById("sidebarToggle");
    const close = document.getElementById("sidebarClose");
    const menuButton = document.getElementById("menuButton");
    const overlay = document.getElementById("sidebarOverlay");

    if (!sidebar) return;

    const storageKey = "mercado_sidebar_collapsed";
    const mobileQuery = window.matchMedia("(max-width: 900px)");

    let focoAnterior = null;

    function atualizarBotao() {
        const collapsed = body.classList.contains("sidebar-collapsed");

        if (!toggle) return;

        const descricao = collapsed ? "Expandir menu" : "Recolher menu";

        toggle.title = descricao;
        toggle.setAttribute("aria-label", descricao);
        toggle.setAttribute("aria-expanded", String(!collapsed));
    }

    function abrirMenuMobile() {
        focoAnterior = document.activeElement;

        body.classList.add("menu-open");

        menuButton?.setAttribute("aria-expanded", "true");
        close?.focus();
    }

    function fecharMenuMobile() {
        if (!body.classList.contains("menu-open")) return;

        body.classList.remove("menu-open");

        menuButton?.setAttribute("aria-expanded", "false");
        focoAnterior?.focus();
    }

    function alternarSidebar() {
        if (mobileQuery.matches) return;

        const collapsed = body.classList.toggle("sidebar-collapsed");

        try {
            localStorage.setItem(storageKey, String(collapsed));
        } catch (error) {
            // Continua funcionando sem armazenamento local.
        }

        atualizarBotao();
    }

    // Recupera a preferência do desktop
    try {
        if (localStorage.getItem(storageKey) === "true") {
            body.classList.add("sidebar-collapsed");
        }
    } catch (error) {
        // Continua com a sidebar expandida.
    }

    atualizarBotao();

    // Desktop: recolher ou expandir
    toggle?.addEventListener("click", alternarSidebar);

    // Mobile: abrir e fechar
    menuButton?.addEventListener("click", abrirMenuMobile);
    close?.addEventListener("click", fecharMenuMobile);
    overlay?.addEventListener("click", fecharMenuMobile);

    // Tecla Escape fecha o menu mobile
    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            fecharMenuMobile();
        }
    });

    // Ao voltar para desktop, fecha o menu mobile
    mobileQuery.addEventListener("change", (event) => {
        if (!event.matches) {
            fecharMenuMobile();
        }
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("toastContainer");

    if (!container) return;

    const messages = JSON.parse(container.dataset.messages || "{}");

    Object.entries(messages).forEach(([type, message]) => {
        if (message) {
            showToast(message, type);
        }
    });
});

function showToast(message, type = "info", duration = 4500) {
    const container = document.getElementById("toastContainer");
    if (!container) return;

    const types = ["success", "error", "warning", "info"];
    if (!types.includes(type)) type = "info";

    const icons = {
        success: "bi-check-circle-fill",
        error: "bi-x-circle-fill",
        warning: "bi-exclamation-triangle-fill",
        info: "bi-info-circle-fill",
    };

    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.setAttribute("role", type === "error" ? "alert" : "status");

    const icon = document.createElement("i");
    icon.className = `bi ${icons[type]}`;

    const text = document.createElement("div");
    text.className = "toast-message";
    text.textContent = String(message);

    const close = document.createElement("button");
    close.type = "button";
    close.className = "toast-close";
    close.setAttribute("aria-label", "Fechar notificação");
    close.textContent = "×";

    toast.append(icon, text, close);
    container.appendChild(toast);

    let removed = false;
    let timeout;

    function dismiss() {
        if (removed) return;
        removed = true;
        clearTimeout(timeout);
        toast.classList.add("leaving");
        setTimeout(() => toast.remove(), 220);
    }

    close.addEventListener("click", dismiss);
    timeout = setTimeout(dismiss, duration);
}

// Validação global dos formulários
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("form[data-validate]").forEach((form) => {
        form.noValidate = true;

        const campos = form.querySelectorAll("input, select, textarea");

        function validarCampo(campo) {
            if (
                campo.disabled ||
                ["hidden", "submit", "button"].includes(campo.type)
            ) {
                return true;
            }

            const valido = campo.checkValidity();
            const grupo = campo.closest(".form-group");
            const mensagemAnterior = grupo?.querySelector(".field-error");

            campo.classList.toggle("is-invalid", !valido);
            campo.setAttribute("aria-invalid", String(!valido));
            mensagemAnterior?.remove();

            if (!valido && grupo) {
                const mensagem = document.createElement("span");
                mensagem.className = "field-error";

                if (campo.validity.valueMissing) {
                    mensagem.textContent = "Este campo é obrigatório.";
                } else if (campo.validity.typeMismatch) {
                    mensagem.textContent = "Informe um valor válido.";
                } else if (campo.validity.patternMismatch) {
                    mensagem.textContent = "O formato informado é inválido.";
                } else if (
                    campo.validity.rangeUnderflow ||
                    campo.validity.rangeOverflow
                ) {
                    mensagem.textContent = "Valor fora do intervalo permitido.";
                } else {
                    mensagem.textContent = "Verifique o valor informado.";
                }

                grupo.appendChild(mensagem);
            }

            return valido;
        }

        campos.forEach((campo) => {
            campo.addEventListener("input", () => {
                if (campo.classList.contains("is-invalid")) {
                    validarCampo(campo);
                }
            });

            campo.addEventListener("change", () => {
                if (campo.classList.contains("is-invalid")) {
                    validarCampo(campo);
                }
            });
        });

        form.addEventListener("submit", (e) => {
            let primeiroInvalido = null;

            campos.forEach((campo) => {
                if (!validarCampo(campo) && !primeiroInvalido) {
                    primeiroInvalido = campo;
                }
            });

            if (primeiroInvalido) {
                e.preventDefault();

                primeiroInvalido.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });

                primeiroInvalido.focus({ preventScroll: true });

                if (typeof showToast === "function") {
                    showToast(
                        "Preencha corretamente os campos destacados.",
                        "warning",
                    );
                }
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const dataFiltro = document.getElementById("dataFiltro");

    dataFiltro?.addEventListener("change", function () {
        if (!this.value) return;

        const url = new URL(window.location.href);
        url.searchParams.set("data", this.value);

        window.location.href = url.toString();
    });
});
