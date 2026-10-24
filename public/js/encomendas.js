document.addEventListener('DOMContentLoaded', () => {
  const $ = id => document.getElementById(id);
  const aviso = (message, type = 'info') => typeof showToast === 'function' && showToast(message, type);

  document.querySelectorAll('.mc-status-form').forEach(form => {
    const select = form.querySelector('select[name="status"]');
    const button = form.querySelector('button[type="submit"]');
    select.addEventListener('change', () => button.disabled = select.value === form.dataset.statusAtual);
    form.addEventListener('submit', e => {
      if (select.value === 'cancelado' && !confirm('Cancelar este pedido?')) e.preventDefault();
      if (select.value === 'nao_retirado' && !confirm('Registrar que este pedido não foi retirado?')) e.preventDefault();
    });
  });

  const modal = $('modalPedido');
  if (!modal) return;

  const rapido = $('modalClienteRapido');
  const form = $('formPedido');
  const formRapido = $('formClienteRapido');
  const select = $('pedidoClienteId');
  const tipo = $('pedidoTipo');
  const endereco = $('pedidoEndereco');
  const selectWidget = typeof TomSelect !== 'undefined'
    ? new TomSelect(select, {
        create: false,
        maxItems: 1,
        maxOptions: 80,
        searchField: ['text'],
        placeholder: 'Buscar por nome ou telefone...',
        closeAfterSelect: true
      })
    : null;

  function atualizarEndereco() {
    const entrega = tipo.value === 'entrega';
    $('grupoEndereco').hidden = !entrega;
    endereco.required = entrega;
    if (!entrega) endereco.value = '';
  }
  tipo.addEventListener('change', atualizarEndereco);
  atualizarEndereco();

  function abrirPedido(limpar = true) {
    if (limpar) {
      form.reset();
      if (selectWidget) selectWidget.clear();
      $('pedidoData').value = $('dataFiltro')?.value || new Date().toLocaleDateString('sv-SE');
      atualizarEndereco();
    }
    modal.hidden = false;
    document.body.classList.add('customer-modal-open');
    (selectWidget?.focus ? selectWidget : $('pedidoQuantidade')).focus();
  }
  function fecharPedido() {
    if (!rapido.hidden) return;
    modal.hidden = true;
    document.body.classList.remove('customer-modal-open');
    $('abrirNovoPedido')?.focus();
  }
  $('abrirNovoPedido')?.addEventListener('click', () => abrirPedido());
  $('abrirPrimeiroPedido')?.addEventListener('click', () => abrirPedido());
  $('fecharModalPedido')?.addEventListener('click', fecharPedido);
  $('cancelarModalPedido')?.addEventListener('click', fecharPedido);
  modal.addEventListener('click', e => { if (e.target === modal) fecharPedido(); });

  form.addEventListener('submit', e => {
    const clienteSelecionado = selectWidget ? selectWidget.getValue() : select.value;
    if (!clienteSelecionado) {
      e.preventDefault();
      (selectWidget?.focus ? selectWidget : select).focus();
      aviso('Selecione um cliente ou cadastre um novo.', 'warning');
      return;
    }
    const invalidos = [...form.querySelectorAll('input,select,textarea')]
      .filter(el => el !== select && !el.disabled && !el.checkValidity());
    if (invalidos.length) {
      e.preventDefault();
      invalidos[0].focus();
      aviso('Preencha os campos obrigatórios do pedido.', 'warning');
    }
  });

  function mascaraTelefone(valor) {
    const n = String(valor).replace(/\D/g, '').slice(0, 11);
    if (!n) return '';
    if (n.length <= 2) return `(${n}`;
    const d = n.slice(0, 2), r = n.slice(2), limite = n.length > 10 ? 5 : 4;
    return `(${d}) ${r.slice(0, limite)}${r.length > limite ? '-' + r.slice(limite) : ''}`;
  }
  $('clienteRapidoTelefone').addEventListener('input', e => e.target.value = mascaraTelefone(e.target.value));

  function abrirRapido() {
    formRapido.reset();
    $('erroClienteRapido').hidden = true;
    rapido.hidden = false;
    $('clienteRapidoNome').focus();
  }
  function fecharRapido() {
    rapido.hidden = true;
    $('abrirClienteRapido').focus();
  }
  $('abrirClienteRapido').addEventListener('click', abrirRapido);
  $('fecharClienteRapido').addEventListener('click', fecharRapido);
  $('cancelarClienteRapido').addEventListener('click', fecharRapido);
  rapido.addEventListener('click', e => { if (e.target === rapido) fecharRapido(); });
  document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    if (!rapido.hidden) fecharRapido();
    else if (!modal.hidden) fecharPedido();
  });

  formRapido.addEventListener('submit', async e => {
    e.preventDefault();
    const button = $('salvarClienteRapido');
    const erro = $('erroClienteRapido');
    erro.hidden = true;
    if (!formRapido.checkValidity()) {
      formRapido.querySelector(':invalid')?.focus();
      aviso('Informe nome e telefone completos.', 'warning');
      return;
    }
    button.disabled = true;
    try {
      const response = await fetch(formRapido.action, {
        method: 'POST',
        headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
        body: new FormData(formRapido),
        credentials: 'same-origin'
      });
      const data = await response.json();
      if (!response.ok) {
        erro.textContent = response.status === 422
          ? Object.values(data.errors || {}).flat().join(' ')
          : data.message || 'Não foi possível cadastrar o cliente.';
        erro.hidden = false;
        return;
      }
      const cliente = data.cliente;
      const value = String(cliente.id);
      const text = `${cliente.nome} — ${cliente.telefone}`;
      if (selectWidget) {
        selectWidget.addOption({value, text});
        selectWidget.setValue(value);
      } else {
        select.add(new Option(text, value, true, true));
      }
      fecharRapido();
      aviso(data.message || 'Cliente cadastrado com sucesso!', 'success');
    } catch (_) {
      erro.textContent = 'Não foi possível conectar ao servidor.';
      erro.hidden = false;
    } finally {
      button.disabled = false;
    }
  });

  if ($('orderPageConfig')?.dataset.hasErrors === '1') {
    abrirPedido(false);
    aviso('Revise os dados informados no pedido.', 'warning');
  }
});
