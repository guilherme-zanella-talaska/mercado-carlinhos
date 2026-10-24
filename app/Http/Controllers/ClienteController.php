<?php

namespace App\Http\Controllers;

use App\Services\ClienteService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function __construct(
        private ClienteService $service
    ) {
    }

    public function index(Request $request): View
    {
        $busca = trim((string) $request->query('busca', ''));

        return view('clientes.index', [
            'clientes' => $this->service->listar($busca),
            'busca' => $busca,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'telefone' => [
                'required',
                'string',
                'regex:/^\(\d{2}\) \d{4,5}-\d{4}$/',
                'max:20',
            ],
        ]);

        $this->service->criar($dados);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function update(Request $request, int $cliente): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'telefone' => [
                'required',
                'string',
                'regex:/^\(\d{2}\) \d{4,5}-\d{4}$/',
                'max:20',
            ],
            'status' => ['required', 'boolean'],
        ]);

        $this->service->atualizar($cliente, $dados);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(int $cliente): RedirectResponse
    {
        try {
            $this->service->excluir($cliente);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('clientes.index')
                ->with('warning', $exception->errors()['exclusao'][0]);
        } catch (QueryException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1451) {
                return redirect()
                    ->route('clientes.index')
                    ->with('warning', 'Este cliente possui encomendas e não pode ser excluído.');
            }

            throw $exception;
        }

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }
}
