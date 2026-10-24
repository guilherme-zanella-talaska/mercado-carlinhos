<div class="customer-modal-backdrop" id="modalPedido" hidden>
  <div class="customer-modal mc-order-modal" role="dialog" aria-modal="true" aria-labelledby="tituloModalPedido" tabindex="-1">
    <div class="customer-modal-header">
      <div class="customer-modal-heading"><span class="customer-modal-icon"><i class="bi bi-bag-plus"></i></span><div><h2 id="tituloModalPedido">Novo pedido de frango</h2><p>Preencha os dados da encomenda.</p></div></div>
      <button type="button" class="customer-modal-close" id="fecharModalPedido" aria-label="Fechar"><i class="bi bi-x-lg"></i></button>
    </div>
    <form id="formPedido" method="POST" action="{{ route('encomendas.store') }}" novalidate>
      @csrf
      <div class="customer-modal-body mc-form-body">
        <div class="form-group mc-customer-field">
          <label class="form-label" for="pedidoClienteId">Cliente *</label>
          <div class="mc-search-line">
            <div class="mc-select-container">
              <select id="pedidoClienteId" name="id_cliente" required>
                <option value="">Selecione um cliente...</option>
                @foreach($clientes as $cliente)
                  <option value="{{ $cliente->id }}" @selected(old('id_cliente') == $cliente->id)>{{ $cliente->nome }} — {{ $cliente->telefone }}</option>
                @endforeach
              </select>
            </div>
            <button type="button" class="btn btn-secondary" id="abrirClienteRapido"><i class="bi bi-person-plus"></i> Novo cliente</button>
          </div>
          @error('id_cliente')<span class="field-error">{{ $message }}</span>@enderror
        </div>
        <div class="mc-form-grid">
          <div class="form-group"><label class="form-label" for="pedidoQuantidade">Quantidade de frangos *</label><input type="number" min="1" max="999" id="pedidoQuantidade" name="quantidade" value="{{ old('quantidade', 1) }}" class="form-control" required>@error('quantidade')<span class="field-error">{{ $message }}</span>@enderror</div>
          <div class="form-group"><label class="form-label" for="pedidoData">Data *</label><input type="date" id="pedidoData" name="data_entrega" class="form-control" value="{{ old('data_entrega', $data) }}" required>@error('data_entrega')<span class="field-error">{{ $message }}</span>@enderror</div>
          <div class="form-group"><label class="form-label" for="pedidoHorario">Horário *</label><input type="time" id="pedidoHorario" name="horario_entrega" class="form-control" value="{{ old('horario_entrega') }}" required>@error('horario_entrega')<span class="field-error">{{ $message }}</span>@enderror</div>
          <div class="form-group"><label class="form-label" for="pedidoTipo">Recebimento *</label><select id="pedidoTipo" name="tipo_entrega" class="form-control" required><option value="retirada" @selected(old('tipo_entrega') !== 'entrega')>Retirada no mercado</option><option value="entrega" @selected(old('tipo_entrega') === 'entrega')>Entrega no endereço</option></select></div>
        </div>
        <div class="form-group" id="grupoEndereco" hidden><label class="form-label" for="pedidoEndereco">Endereço de entrega *</label><input type="text" id="pedidoEndereco" name="endereco" class="form-control" maxlength="255" value="{{ old('endereco') }}" placeholder="Rua, número e referência">@error('endereco')<span class="field-error">{{ $message }}</span>@enderror</div>
        <div class="form-group"><label class="form-label" for="pedidoObs">Observações</label><textarea id="pedidoObs" name="observacoes" rows="3" class="form-control" maxlength="2000">{{ old('observacoes') }}</textarea></div>
      </div>
      <div class="customer-modal-footer"><button type="button" class="btn btn-secondary" id="cancelarModalPedido">Cancelar</button><button type="submit" class="btn btn-primary" id="salvarPedido"><i class="bi bi-check-lg"></i> Salvar pedido</button></div>
    </form>
  </div>
</div>

<div class="customer-modal-backdrop" id="modalClienteRapido" hidden>
  <div class="customer-modal mc-fast-modal" role="dialog" aria-modal="true" aria-labelledby="tituloClienteRapido" tabindex="-1">
    <div class="customer-modal-header"><div class="customer-modal-heading"><span class="customer-modal-icon"><i class="bi bi-person-plus"></i></span><div><h2 id="tituloClienteRapido">Novo cliente</h2><p>Será selecionado automaticamente no pedido.</p></div></div><button type="button" class="customer-modal-close" id="fecharClienteRapido" aria-label="Fechar"><i class="bi bi-x-lg"></i></button></div>
    <form id="formClienteRapido" method="POST" action="{{ route('clientes.rapido') }}" novalidate>
      @csrf
      <div class="customer-modal-body">
        <div class="form-group"><label class="form-label" for="clienteRapidoNome">Nome completo *</label><input type="text" id="clienteRapidoNome" name="nome" maxlength="150" class="form-control" required></div>
        <div class="form-group"><label class="form-label" for="clienteRapidoTelefone">Telefone *</label><input type="tel" id="clienteRapidoTelefone" name="telefone" maxlength="15" inputmode="numeric" placeholder="(49) 99999-9999" class="form-control" required></div>
        <div class="field-error" id="erroClienteRapido" role="alert" hidden></div>
      </div>
      <div class="customer-modal-footer"><button type="button" class="btn btn-secondary" id="cancelarClienteRapido">Cancelar</button><button type="submit" class="btn btn-primary" id="salvarClienteRapido"><i class="bi bi-check-lg"></i> Salvar cliente</button></div>
    </form>
  </div>
</div>
<div id="orderPageConfig" data-has-errors="{{ $errors->any() ? '1' : '0' }}" hidden></div>
